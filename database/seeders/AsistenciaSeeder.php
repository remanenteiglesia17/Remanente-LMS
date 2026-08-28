<?php

namespace Database\Seeders;

use App\Models\Asistencia;
use App\Models\Clase;
use App\Models\Curso;
use Illuminate\Database\Seeder;

class AsistenciaSeeder extends Seeder
{
    /**
     * Por cada curso que ya tiene profesor asignado (HorarioSeeder) y
     * estudiantes inscritos (EstudianteSeeder), crea:
     *  - 4 clases "dictadas" en el pasado, con asistencia ya registrada
     *    (variando presente/ausente/tardanza/excusado) para poder ver el
     *    reporte de inasistencias y el estadístico de cada estudiante.
     *  - 1 clase "programada" en el futuro, SIN asistencia, para poder
     *    probar el flujo real de tomar asistencia desde /admin/profesor/asistencia.
     *
     * A un estudiante de cada curso se le deja explícitamente con 3
     * inasistencias injustificadas para poder probar el reprobado
     * automático (ver AsistenciaController::verificarInasistenciasYReprobar).
     */
    public function run(): void
    {
        $cursos = Curso::with(['profesores', 'estudiantes'])->get();

        foreach ($cursos as $curso) {
            $profesor = $curso->profesores->first();
            $estudiantes = $curso->estudiantes;

            if (!$profesor || $estudiantes->isEmpty()) {
                continue; // sin profesor o sin inscritos no hay a quién tomarle asistencia
            }

            // ---- Clases pasadas (ya dictadas) ----
            for ($i = 4; $i >= 1; $i--) {
                $inicio = now()->subWeeks($i)->setTime(18, 0);

                $clase = Clase::create([
                    'titulo' => "{$curso->nombre} - Sesión " . (5 - $i),
                    'fecha_hora_inicio' => $inicio,
                    'fecha_hora_fin' => $inicio->copy()->addHours(2),
                    'color' => '#3c8dbc',
                    'estado' => 'dictada',
                    'curso_id' => $curso->id,
                    'profesor_id' => $profesor->id,
                ]);

                $clase->estudiantes()->attach($estudiantes->pluck('id'));

                foreach ($estudiantes as $index => $estudiante) {
                    // El primer estudiante del curso acumula 3 ausencias
                    // injustificadas (sesiones 2, 3 y 4) para poder probar
                    // el reprobado automático por inasistencias.
                    if ($index === 0 && $i <= 3) {
                        $estado = 'ausente';
                    } else {
                        $estado = ['presente', 'presente', 'tardanza', 'ausente', 'excusado'][($index + $i) % 5];
                    }

                    Asistencia::create([
                        'clase_id' => $clase->id,
                        'estudiante_id' => $estudiante->id,
                        'estado' => $estado,
                        'observaciones' => $estado === 'excusado' ? 'Justificado por el estudiante.' : null,
                    ]);
                }
            }

            // ---- Clase futura (sin asistencia todavía) ----
            $proxima = now()->addWeek()->setTime(18, 0);

            $claseFutura = Clase::create([
                'titulo' => "{$curso->nombre} - Próxima sesión",
                'fecha_hora_inicio' => $proxima,
                'fecha_hora_fin' => $proxima->copy()->addHours(2),
                'color' => '#00a65a',
                'estado' => 'programada',
                'curso_id' => $curso->id,
                'profesor_id' => $profesor->id,
            ]);

            $claseFutura->estudiantes()->attach($estudiantes->pluck('id'));
        }
    }
}
