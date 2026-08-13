<?php

namespace App\Http\Controllers\Academico\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\Curso;
use Illuminate\Support\Facades\Auth;

class CalificacionController extends Controller
{
    /**
     * Ver todas las calificaciones del estudiante, agrupadas por curso
     * y por tipo de evaluación (tarea, quiz, parcial, proyecto...).
     */
    public function index()
    {
        $estudiante = Auth::user()->estudiante;

        // Obtener todas las calificaciones publicadas del estudiante
        $calificaciones = Calificacion::where('estudiante_id', $estudiante->id)
            ->where('publicada', true)
            ->with(['curso', 'entrega'])
            ->orderBy('fecha_calificacion', 'desc')
            ->get();

        // Agrupar por curso
        $calificacionesPorCurso = $calificaciones->groupBy('curso_id');

        // Calcular estadísticas reales por curso (sin datos quemados)
        $promedios = [];
        foreach ($calificacionesPorCurso as $cursoId => $califs) {
            $curso = $califs->first()->curso;

            $promedioPonderado = Calificacion::promedioPonderadoEstudianteCurso(
                $estudiante->id,
                $cursoId
            );

            // Promedio del curso entre todos los estudiantes (para comparar)
            $estadisticasCurso = Calificacion::estadisticasCurso($cursoId);

            $totalTareasCurso = $curso->tareas()->where('visible', true)->count();
            $tareasCalificadas = $califs->count();

            $promedios[$cursoId] = [
                'curso' => $curso,
                'promedio' => $promedioPonderado,
                'promedio_curso' => round($estadisticasCurso['promedio'] ?? 0, 2),
                'total_calificaciones' => $tareasCalificadas,
                'total_tareas_curso' => $totalTareasCurso,
                'porcentaje_completado' => $totalTareasCurso > 0
                    ? round(($tareasCalificadas / $totalTareasCurso) * 100)
                    : 0,
                'aprobado' => $promedioPonderado >= 3.0,
                // Evaluaciones agrupadas por tipo (tarea, quiz, parcial, proyecto...)
                'por_tipo' => $califs->groupBy('tipo_evaluacion')->map(function ($grupo) {
                    return [
                        'items' => $grupo->sortBy('fecha_calificacion')->values(),
                        'peso_total' => $grupo->sum('porcentaje'),
                        'promedio' => round($grupo->avg('nota'), 2),
                    ];
                }),
            ];
        }

        return view('estudiante.calificaciones.index', compact(
            'calificaciones',
            'calificacionesPorCurso',
            'promedios'
        ));
    }

    /**
     * Ver calificaciones de un curso específico
     */
    public function porCurso(Curso $curso)
    {
        $estudiante = Auth::user()->estudiante;

        // Verificar que el estudiante esté inscrito en el curso
        abort_unless($estudiante->cursos->contains($curso->id),403,'No tienes acceso a este curso.');

        // Calificaciones del curso
        $calificaciones = Calificacion::where('estudiante_id', $estudiante->id)
            ->where('curso_id', $curso->id)
            ->where('publicada', true)
            ->with('entrega')
            ->orderBy('fecha_calificacion', 'desc')
            ->get();

        // Calcular promedio ponderado
        $promedioPonderado = Calificacion::promedioPonderadoEstudianteCurso(
            $estudiante->id,
            $curso->id
        );

        // Estadísticas
        $estadisticas = [
            'total_evaluaciones' => $calificaciones->count(),
            'promedio_ponderado' => $promedioPonderado,
            'nota_maxima' => $calificaciones->max('nota') ?? 0,
            'nota_minima' => $calificaciones->min('nota') ?? 0,
            'aprobadas' => $calificaciones->filter(fn($c) => $c->nota >= 3.0)->count(),
            'reprobadas' => $calificaciones->filter(fn($c) => $c->nota < 3.0)->count(),
            'estado' => $promedioPonderado >= 3.0 ? 'Aprobado' : 'Reprobado',
        ];

        // Agrupar por tipo de evaluación
        $porTipo = $calificaciones->groupBy('tipo_evaluacion');

        return view('estudiante.calificaciones.por-curso', compact(
            'curso',
            'calificaciones',
            'promedioPonderado',
            'estadisticas',
            'porTipo'
        ));
    }
}