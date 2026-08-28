<?php

namespace App\Http\Controllers\Academico\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\Curso;
use Illuminate\Support\Facades\Auth;

class CalificacionController extends Controller
{
    public function index()
    {
        $estudiante = Auth::user()->estudiante;

        $calificaciones = Calificacion::where('estudiante_id', $estudiante->id)
            ->where('publicada', true)
            ->with(['curso', 'entrega'])
            ->orderBy('fecha_calificacion', 'desc')
            ->get();

        $calificacionesPorCurso = $calificaciones->groupBy('curso_id');

        $promedios = [];
        foreach ($calificacionesPorCurso as $cursoId => $califs) {
            $curso = $califs->first()->curso;

            $promedioPonderado = Calificacion::promedioPonderadoEstudianteCurso($estudiante->id, $cursoId);
            $estadisticasCurso = Calificacion::estadisticasCurso($cursoId);

            $totalTareasCurso = $curso->tareas()->where('visible', true)->count();
            $tareasCalificadas = $califs->count();
            $aprobado = $promedioPonderado >= 3.0;

            // Validación de Certificado (Sin lógica en Blade)
            $cursoPivot = $estudiante->cursos()
                ->where('cursos.id', $cursoId)
                ->withPivot('horas_realizadas', 'estado')
                ->first();

            $estadoPivot  = $cursoPivot?->pivot->estado ?? 'activo';
            $horasOk      = $cursoPivot && $cursoPivot->pivot->horas_realizadas >= $curso->horas_requeridas;
            $puedeDescargar = ($estadoPivot === 'aprobado') || ($aprobado && $horasOk);

            $promedios[$cursoId] = [
                'curso' => $curso,
                'promedio' => $promedioPonderado,
                'promedio_curso' => round($estadisticasCurso['promedio'] ?? 0, 2),
                'total_calificaciones' => $tareasCalificadas,
                'total_tareas_curso' => $totalTareasCurso,
                'porcentaje_completado' => $totalTareasCurso > 0
                    ? round(($tareasCalificadas / $totalTareasCurso) * 100)
                    : 0,
                'aprobado' => $aprobado,
                'puede_descargar' => $puedeDescargar,
                'razon_bloqueo' => !$aprobado ? 'Nota insuficiente' : 'Pendiente de aprobación',
            ];
        }

        return view('estudiante.calificaciones.index', compact('promedios'));
    }

    public function porCurso(Curso $curso)
    {
        $estudiante = Auth::user()->estudiante;

        abort_unless($estudiante->cursos->contains($curso->id), 403, 'No tienes acceso a este curso.');

        $calificaciones = Calificacion::where('estudiante_id', $estudiante->id)
            ->where('curso_id', $curso->id)
            ->where('publicada', true)
            ->with(['entrega', 'tarea.modulo'])
            ->orderBy('fecha_calificacion', 'desc')
            ->get();

        $promedioPonderado = Calificacion::promedioPonderadoEstudianteCurso($estudiante->id, $curso->id);

        $estadisticas = [
            'total_evaluaciones' => $calificaciones->count(),
            'promedio_ponderado' => $promedioPonderado,
            'aprobadas' => $calificaciones->filter(fn($c) => $c->nota >= 3.0)->count(),
            'reprobadas' => $calificaciones->filter(fn($c) => $c->nota < 3.0)->count(),
        ];

        // Agrupar por módulo (estructura real de la ponderación) y, dentro de
        // cada módulo, por tipo de actividad.
        $modulos = \App\Models\Modulo::where('curso_id', $curso->id)->orderBy('orden')->get();

        $porModulo = $modulos->map(function ($modulo) use ($calificaciones) {
            $calificacionesModulo = $calificaciones->filter(
                fn ($c) => $c->tarea && $c->tarea->modulo_id === $modulo->id
            );

            $pesosCategoria = $modulo->pesosPorCategoria();

            $porTipo = $calificacionesModulo->groupBy('tipo_evaluacion')->map(function ($grupo, $tipo) use ($pesosCategoria) {
                return [
                    'items' => $grupo->sortBy('fecha_calificacion')->values(),
                    'peso_categoria' => $pesosCategoria[$tipo] ?? 0,
                    'cantidad' => $grupo->count(),
                    'promedio' => round($grupo->avg('nota'), 2),
                ];
            });

            return [
                'modulo' => $modulo,
                'por_tipo' => $porTipo,
                'promedio_modulo' => \App\Models\Calificacion::notaModulo($modulo, $calificacionesModulo),
                'tiene_calificaciones' => $calificacionesModulo->isNotEmpty(),
            ];
        })->filter(fn ($m) => $m['tiene_calificaciones']);

        $profesor = $curso->profesores()->with('user')->first();

        return view('estudiante.calificaciones.por-curso', compact(
            'curso',
            'calificaciones',
            'estadisticas',
            'porModulo',
            'profesor'
        ));
    }
}