<?php

namespace Database\Seeders;

use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\Entrega;
use App\Models\Tarea;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EntregaCalificacionSeeder extends Seeder
{
    // Mapea el tipo de la tarea al enum tipo_evaluacion de calificacions
    private array $mapaTipoEvaluacion = [
        'tarea' => 'tarea',
        'quiz' => 'quiz',
        'examen' => 'parcial',
        'proyecto' => 'proyecto',
        'foro' => 'participacion',
    ];

    private const NOTA_MINIMA_APROBACION = 3.0;
    private const NOTA_MAXIMA = 5.0;

    public function run(): void
    {
        mt_srand(2026); // datos reproducibles cada vez que se corre el seeder

        $cursos = Curso::with(['estudiantes', 'tareas', 'profesores'])->get();

        foreach ($cursos as $curso) {
            // Profesor que califica: el primero asignado al curso (si tiene varios)
            $profesor = $curso->profesores->first();
            if (!$profesor) {
                continue;
            }

            $tareas = $curso->tareas()->orderBy('fecha_entrega')->get();
            if ($tareas->isEmpty()) {
                continue;
            }

            foreach ($curso->estudiantes as $estudiante) {
                // Perfil de rendimiento propio del estudiante en este curso,
                // para que sus notas sean consistentes entre evaluaciones (no puro azar).
                $perfil = $this->perfilAleatorio();

                $sumaPonderada = 0;
                $sumaPorcentajes = 0;
                $tareasCalificadas = 0;

                foreach ($tareas as $tarea) {
                    /** @var Tarea $tarea */
                    $fechaEntregaTarea = Carbon::parse($tarea->fecha_entrega);
                    $fechaApertura = $tarea->fecha_apertura ? Carbon::parse($tarea->fecha_apertura) : $fechaEntregaTarea->copy()->subDays(7);
                    $yaDebioAbrirse = $fechaApertura->isPast();

                    // Si la tarea todavía no abre, no hay entrega posible.
                    if (!$yaDebioAbrirse) {
                        continue;
                    }

                    // Probabilidad de entrega (según perfil del estudiante)
                    $entrego = mt_rand(1, 100) <= $perfil['probabilidad_entrega'];
                    if (!$entrego) {
                        continue;
                    }

                    $esTardia = $fechaEntregaTarea->isPast() && mt_rand(1, 100) <= 15;
                    $fechaReal = $esTardia
                        ? $fechaEntregaTarea->copy()->addDays(mt_rand(1, 3))
                        : $fechaEntregaTarea->copy()->subDays(mt_rand(0, 2));

                    // Si la tarea aún no vence, la entrega existe pero está pendiente de calificar.
                    $tareaYaVencio = $fechaEntregaTarea->isPast();
                    $estadoEntrega = !$tareaYaVencio
                        ? 'pendiente'
                        : ($esTardia ? 'tardia' : 'calificada');
                    // Una pequeña porción de lo ya vencido aún no ha sido calificado por el profesor
                    if ($tareaYaVencio && $estadoEntrega !== 'tardia' && mt_rand(1, 100) <= 10) {
                        $estadoEntrega = 'pendiente';
                    }

                    $entrega = Entrega::create([
                        'tarea_id' => $tarea->id,
                        'estudiante_id' => $estudiante->id,
                        'comentario' => $this->comentarioEntrega($tarea->tipo),
                        'archivo' => $tarea->formato_entrega === 'texto' ? null : "entregas/{$tarea->id}_{$estudiante->id}.pdf",
                        'fecha_entrega' => $fechaReal,
                        'entrega_tardia' => $esTardia,
                        'estado' => $estadoEntrega,
                    ]);

                    if ($estadoEntrega === 'pendiente') {
                        continue; // aún no calificada
                    }

                    $nota = $this->notaSegunPerfil($perfil, $esTardia, (float) $tarea->penalizacion_tardia);

                    Calificacion::create([
                        'estudiante_id' => $estudiante->id,
                        'curso_id' => $curso->id,
                        'profesor_id' => $profesor->id,
                        'entrega_id' => $entrega->id,
                        'tarea_id' => $tarea->id, // <-- resuelve Módulo (tarea->modulo) y permite calcular Aporte Final
                        'concepto' => $tarea->titulo_tarea,
                        'nota' => $nota,
                        'nota_maxima' => self::NOTA_MAXIMA,
                        'tipo_evaluacion' => $this->mapaTipoEvaluacion[$tarea->tipo] ?? 'otro',
                        'periodo' => $curso->periodo,
                        'fecha_calificacion' => $fechaReal->copy()->addDays(mt_rand(1, 4)),
                        'observaciones' => $this->observacion($nota),
                        'publicada' => true,
                    ]);

                    $sumaPonderada += ($nota / self::NOTA_MAXIMA) * $tarea->puntaje;
                    $sumaPorcentajes += $tarea->puntaje;
                    $tareasCalificadas++;
                }

                // ------- Actualizar estado real en la tabla pivote estudiante_curso -------
                $promedioPonderado = $sumaPorcentajes > 0
                    ? round(($sumaPonderada / $sumaPorcentajes) * self::NOTA_MAXIMA, 2)
                    : 0;

                $totalTareasCurso = $tareas->count();
                $porcentajeAvance = $totalTareasCurso > 0 ? $tareasCalificadas / $totalTareasCurso : 0;

                // Solo se marca aprobado/reprobado si ya se calificó todo lo que ha vencido
                // y el curso no tiene tareas pendientes de calificar por el profesor.
                $tareasVencidas = $tareas->filter(fn ($t) => Carbon::parse($t->fecha_entrega)->isPast())->count();
                $estadoFinal = 'activo';
                if ($tareasVencidas > 0 && $tareasCalificadas >= $tareasVencidas && $tareasVencidas === $totalTareasCurso) {
                    $estadoFinal = $promedioPonderado >= self::NOTA_MINIMA_APROBACION ? 'aprobado' : 'reprobado';
                }

                DB::table('estudiante_curso')
                    ->where('estudiante_id', $estudiante->id)
                    ->where('curso_id', $curso->id)
                    ->update([
                        'estado' => $estadoFinal,
                        'horas_realizadas' => (int) round($curso->horas_requeridas * $porcentajeAvance),
                        'fecha_inscripcion' => $estudiante->pivot->fecha_inscripcion ?? Carbon::now()->subWeeks(6),
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    /**
     * Perfil de rendimiento: alto / medio / bajo, con distinta probabilidad
     * de entrega y rango de notas. 60% alto-medio, 40% con riesgo de reprobar.
     */
    private function perfilAleatorio(): array
    {
        $tipo = mt_rand(1, 100);

        return match (true) {
            $tipo <= 40 => ['nivel' => 'alto', 'min' => 4.2, 'max' => 5.0, 'probabilidad_entrega' => 95],
            $tipo <= 75 => ['nivel' => 'medio', 'min' => 3.3, 'max' => 4.3, 'probabilidad_entrega' => 88],
            default => ['nivel' => 'bajo', 'min' => 2.0, 'max' => 3.4, 'probabilidad_entrega' => 70],
        };
    }

    private function notaSegunPerfil(array $perfil, bool $esTardia, float $penalizacion): float
    {
        $nota = mt_rand((int) ($perfil['min'] * 100), (int) ($perfil['max'] * 100)) / 100;

        if ($esTardia && $penalizacion > 0) {
            $nota = max(0, $nota - ($nota * $penalizacion / 100));
        }

        return round(min($nota, self::NOTA_MAXIMA), 2);
    }

    private function comentarioEntrega(string $tipo): string
    {
        return match ($tipo) {
            'quiz' => 'Quiz respondido dentro del tiempo asignado.',
            'examen' => 'Evaluación parcial entregada.',
            'proyecto' => 'Entrega del proyecto final del módulo.',
            default => 'Entrega realizada según los requisitos de la actividad.',
        };
    }

    private function observacion(float $nota): string
    {
        if ($nota >= 4.5) return 'Excelente trabajo, cumple todos los criterios.';
        if ($nota >= 4.0) return 'Buen desempeño, con mínimas observaciones.';
        if ($nota >= self::NOTA_MINIMA_APROBACION) return 'Cumple lo mínimo requerido, puede reforzar algunos puntos.';
        return 'No alcanza la nota mínima, se recomienda repasar el material y buscar tutoría.';
    }
}