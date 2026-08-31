<?php

namespace App\Http\Controllers;

use App\Models\Asistencia;
use App\Models\Estudiante;
use App\Models\Clase;
use App\Models\Curso;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsistenciaController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.asistencias.index')->only('index');
        $this->middleware('can:admin.asistencias.store')->only('store');
        $this->middleware('can:admin.asistencias.inasistencias')->only('excusar');
    }

    /**
     * Panel Principal de Asistencias (Pase de Lista + Reporte de Inasistencias)
     */
    public function index()
    {
        $hoy = Carbon::now()->format('Y-m-d');
        $esAdmin = Auth::user()->hasRole(['admin', 'superAdmin']);

        // 1. Obtener Clases (Para Pestaña 'Tomar Asistencia')
        $queryClases = Clase::with(['curso', 'profesor.user', 'estudiantes'])
            ->whereDate('fecha_hora_inicio', '>=', now())
            ->orderBy('fecha_hora_inicio', 'asc');

        if (!$esAdmin) {
            $queryClases->whereHas('profesor', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }
        $clases = $queryClases->get();

        // 2. Asistencias del día indexadas
        $asistencias = Asistencia::with('clase', 'estudiante')
            ->whereHas('clase', function ($query) use ($hoy) {
                $query->whereDate('fecha_hora_inicio', $hoy);
            })
            ->get()
            ->keyBy(fn($item) => $item->clase_id . '-' . $item->estudiante_id);

        // 3. Reporte de Inasistencias (Para Pestaña 'Reporte de Inasistencias')
        $queryInasistencias = Estudiante::select(
                'estudiantes.id',
                'estudiantes.user_id',
                'asistencias.id AS asistencia_id',
                'clases.titulo AS nombre_clase',
                'clases.fecha_hora_inicio',
                'clases.fecha_hora_fin',
                'asistencias.estado',
                'cursos.nombre AS nombre_curso'
            )
            ->with('user')
            ->join('asistencias', 'estudiantes.id', '=', 'asistencias.estudiante_id')
            ->join('clases', 'asistencias.clase_id', '=', 'clases.id')
            ->join('cursos', 'clases.curso_id', '=', 'cursos.id')
            ->where('asistencias.estado', 'ausente');

        if (!$esAdmin) {
            $queryInasistencias->whereHas('clases.profesor', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $inasistencias = $queryInasistencias->orderBy('clases.fecha_hora_inicio', 'desc')->get();

        // Cálculo de horas para las inasistencias
        foreach ($inasistencias as $item) {
            $inicio = new \DateTime($item->fecha_hora_inicio);
            $fin = new \DateTime($item->fecha_hora_fin);
            $diff = $inicio->diff($fin);
            $item->cant_horas = round($diff->h + ($diff->i / 60), 2);
        }

        return view('admin.asistencias.index', compact('clases', 'asistencias', 'inasistencias'));
    }

    /**
     * Registrar asistencias masivas de una clase
     */
    public function store(Request $request)
    {
        $request->validate([
            'clase_id' => 'required|exists:clases,id',
            'asistencias' => 'required|array',
            'asistencias.*.estudiante_id' => 'required|exists:estudiantes,id',
            'asistencias.*.estado' => 'required|in:presente,ausente,tardanza,excusado',
        ]);

        $clase = Clase::with('curso')->findOrFail($request->clase_id);

        DB::beginTransaction();
        try {
            foreach ($request->asistencias as $asistenciaData) {
                $estudianteId = $asistenciaData['estudiante_id'];
                $estado = $asistenciaData['estado'];

                Asistencia::updateOrCreate(
                    [
                        'clase_id' => $clase->id,
                        'estudiante_id' => $estudianteId,
                    ],
                    [
                        'estado' => $estado,
                        'hora_registro' => now(),
                    ]
                );

                $this->asegurarInscripcion($estudianteId, $clase->curso_id);
                $this->verificarInasistenciasYReprobar($estudianteId, $clase->curso_id);
            }

            $clase->update(['estado' => 'dictada']);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Asistencias registradas correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar asistencias: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Justificar/Excusar una inasistencia
     */
    public function excusar(Request $request, $asistenciaId)
    {
        $request->validate([
            'observaciones' => 'nullable|string|max:500'
        ]);

        $asistencia = Asistencia::with('clase')->findOrFail($asistenciaId);

        $asistencia->update([
            'estado' => 'excusado',
            'observaciones' => $request->observaciones,
        ]);

        if ($asistencia->clase) {
            $this->verificarInasistenciasYReprobar($asistencia->estudiante_id, $asistencia->clase->curso_id);
        }

        return redirect()->back()->with('success', 'Inasistencia excusada correctamente');
    }

    private const LIMITE_INASISTENCIAS_INJUSTIFICADAS = 3;

    private function verificarInasistenciasYReprobar($estudianteId, $cursoId)
    {
        $inasistenciasInjustificadas = Asistencia::where('estudiante_id', $estudianteId)
            ->where('estado', 'ausente')
            ->whereHas('clase', fn($query) => $query->where('curso_id', $cursoId))
            ->count();

        $inscripcion = DB::table('estudiante_curso')
            ->where('estudiante_id', $estudianteId)
            ->where('curso_id', $cursoId)
            ->first();

        if (!$inscripcion) return;

        if ($inasistenciasInjustificadas >= self::LIMITE_INASISTENCIAS_INJUSTIFICADAS) {
            if ($inscripcion->estado !== 'reprobado') {
                DB::table('estudiante_curso')
                    ->where('estudiante_id', $estudianteId)
                    ->where('curso_id', $cursoId)
                    ->update(['estado' => 'reprobado']);
            }
        } elseif ($inscripcion->estado === 'reprobado') {
            DB::table('estudiante_curso')
                ->where('estudiante_id', $estudianteId)
                ->where('curso_id', $cursoId)
                ->update(['estado' => 'activo']);
        }
    }

    private function asegurarInscripcion($estudianteId, $cursoId)
    {
        $inscripcion = DB::table('estudiante_curso')
            ->where('estudiante_id', $estudianteId)
            ->where('curso_id', $cursoId)
            ->first();

        if (!$inscripcion) {
            DB::table('estudiante_curso')->insert([
                'estudiante_id' => $estudianteId,
                'curso_id' => $cursoId,
                'estado' => 'activo',
                'fecha_inscripcion' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function estadisticas($estudianteId)
    {
        $estudiante = Estudiante::findOrFail($estudianteId);

        $estadisticas = Asistencia::where('estudiante_id', $estudianteId)
            ->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado');

        $totalClases = $estadisticas->sum();
        
        $porcentajes = [
            'presente' => $totalClases > 0 ? round(($estadisticas->get('presente', 0) / $totalClases) * 100, 2) : 0,
            'ausente' => $totalClases > 0 ? round(($estadisticas->get('ausente', 0) / $totalClases) * 100, 2) : 0,
            'tardanza' => $totalClases > 0 ? round(($estadisticas->get('tardanza', 0) / $totalClases) * 100, 2) : 0,
            'excusado' => $totalClases > 0 ? round(($estadisticas->get('excusado', 0) / $totalClases) * 100, 2) : 0,
        ];

        return view('admin.asistencias.estadisticas', compact('estudiante', 'estadisticas', 'porcentajes', 'totalClases'));
    }
}