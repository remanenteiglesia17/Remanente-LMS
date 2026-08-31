<?php

namespace App\Http\Controllers\Academico\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalificacionController extends Controller
{
public function index()
{
    // 1. Cargar el estudiante autenticado con sus relaciones
    $estudiante = Estudiante::where('user_id', Auth::id())
        ->with(['calificaciones', 'cursos.tareas'])
        ->first();

    if (!$estudiante) {
        return redirect()->route('admin.home')
            ->with('error', 'No se encontró información del estudiante.');
    }

    // 2. Filtrar solo los cursos activos del estudiante
    $cursos = $estudiante->cursos->where('estado', true);

    if ($cursos->isEmpty()) {
        return view('estudiante.sin-curso');
    }

    $promedios = [];

    foreach ($cursos as $curso) {
        // Tareas que componen el curso
        $tareasDelCurso = $curso->tareas;
        $totalTareasCurso = $tareasDelCurso->count();
        $idsTareasCurso = $tareasDelCurso->pluck('id')->toArray();
        $titulosTareas = $tareasDelCurso->pluck('titulo_tarea')->toArray();

        // 3. Obtener calificaciones del estudiante por tarea_id o por concepto
        $calificacionesCurso = $estudiante->calificaciones
            ->filter(function ($cal) use ($curso, $idsTareasCurso, $titulosTareas) {
                $perteneceAlCurso = $cal->curso_id == $curso->id
                    || in_array($cal->tarea_id, $idsTareasCurso)
                    || in_array($cal->concepto, $titulosTareas);

                return $perteneceAlCurso && $cal->nota !== null && $cal->nota !== '';
            });

        // 4. Calcular el promedio ponderado/simple real
        $promedioEstudiante = $calificacionesCurso->count() > 0
            ? round($calificacionesCurso->avg('nota'), 2)
            : 0.00;

        // 5. Promedio del grupo completo utilizando el Modelo Calificacion
        $promedioCurso = Calificacion::where(function ($query) use ($curso, $idsTareasCurso, $titulosTareas) {
                $query->where('curso_id', $curso->id);

                if (!empty($idsTareasCurso)) {
                    $query->orWhereIn('tarea_id', $idsTareasCurso);
                }

                if (!empty($titulosTareas)) {
                    $query->orWhereIn('concepto', $titulosTareas);
                }
            })
            ->whereNotNull('nota')
            ->avg('nota') ?? 0;

        $totalCalificadas = $calificacionesCurso->count();
        $porcentajeCompletado = $totalTareasCurso > 0
            ? round(($totalCalificadas / $totalTareasCurso) * 100)
            : 0;

        $aprobado = $promedioEstudiante >= 3.0;

        $promedios[$curso->id] = [
            'curso' => $curso,
            'promedio' => $promedioEstudiante,
            'promedio_curso' => round($promedioCurso, 2),
            'total_calificaciones' => $totalCalificadas,
            'total_tareas_curso' => $totalTareasCurso,
            'porcentaje_completado' => $porcentajeCompletado,
            'aprobado' => $aprobado,
            'puede_descargar' => $aprobado && ($porcentajeCompletado == 100),
            'razon_bloqueo' => !$aprobado
                ? 'Tu promedio actual es inferior a 3.0'
                : 'Aún tienes tareas pendientes por evaluar'
        ];
    }

    // Colección de todas las calificaciones asociadas al estudiante para la tabla del historial
    $calificaciones = $estudiante->calificaciones;

    return view('estudiante.index', compact('promedios', 'estudiante', 'calificaciones'));
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
                fn($c) => $c->tarea && $c->tarea->modulo_id === $modulo->id
            );

            $pesosCategoria = $modulo->pesosPorCategoria();

            // Se agrupa por el tipo_evaluacion original (para conservar la
            // etiqueta "Parciales" en la vista), pero el peso mostrado usa
            // el tipo normalizado: un parcial hereda el peso de "examen".
            $porTipo = $calificacionesModulo->groupBy('tipo_evaluacion')->map(function ($grupo, $tipo) use ($pesosCategoria) {
                $tipoNormalizado = \App\Models\Calificacion::normalizarTipoParaPeso($tipo);
                return [
                    'items' => $grupo->sortBy('fecha_calificacion')->values(),
                    'peso_categoria' => $pesosCategoria[$tipoNormalizado] ?? 0,
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
        })->filter(fn($m) => $m['tiene_calificaciones']);

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
