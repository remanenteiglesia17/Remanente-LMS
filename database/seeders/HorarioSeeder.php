<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Horario;
use App\Models\HorarioProfesorCurso;
use App\Models\Profesor;
use Illuminate\Database\Seeder;

class HorarioSeeder extends Seeder
{
    /**
     * Un profesor puede dictar varios cursos (y un curso puede tener varios
     * profesores) a través de la tabla pivote horario_profesor_curso.
     */
    public function run(): void
    {
        $profesores = Profesor::orderBy('id')->get();
        $cursos = Curso::orderBy('id')->get();

        if ($profesores->isEmpty() || $cursos->isEmpty()) {
            return;
        }

        // codigo => [ [profesor_index, dia, hora_inicio, hora_fin], ... ]
        $asignaciones = [
            // Profesor 1 (Lewis) dicta 2 cursos
            ['profesor' => 0, 'curso' => 'ANT-2026', 'dia' => 'LUNES', 'inicio' => '06:00:00', 'fin' => '09:00:00', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-06-30'],
            ['profesor' => 0, 'curso' => 'TEO-2026', 'dia' => 'MIERCOLES', 'inicio' => '06:00:00', 'fin' => '09:00:00', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-06-30'],

            // Profesor 2 (TEACHER Gallardo) dicta 2 cursos (uno compartido con Lewis)
            ['profesor' => 1, 'curso' => 'ANT-2026', 'dia' => 'MARTES', 'inicio' => '18:00:00', 'fin' => '20:00:00', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-06-30'],
            ['profesor' => 1, 'curso' => 'EV-2026', 'dia' => 'JUEVES', 'inicio' => '18:00:00', 'fin' => '20:00:00', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-06-30'],

            // Profesor 3 (Julio) dicta 1 curso
            ['profesor' => 2, 'curso' => 'EV-2026', 'dia' => 'SABADO', 'inicio' => '08:00:00', 'fin' => '11:00:00', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-06-30'],

            // Profesor 4 (Martin) dicta 1 curso
            ['profesor' => 3, 'curso' => 'TEO-2026', 'dia' => 'VIERNES', 'inicio' => '18:00:00', 'fin' => '20:00:00', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-06-30'],
        ];

        foreach ($asignaciones as $a) {
            $profesor = $profesores->get($a['profesor']);
            $curso = $cursos->firstWhere('codigo', $a['curso']);

            if (!$profesor || !$curso) {
                continue;
            }

            $horario = Horario::create([
                'dia' => $a['dia'],
                'hora_inicio' => $a['inicio'],
                'hora_fin' => $a['fin'],
                'profesor_id' => $profesor->id,
            ]);

            HorarioProfesorCurso::create([
                'horario_id' => $horario->id,
                'curso_id' => $curso->id,
                'profesor_id' => $profesor->id,
            ]);
        }
    }
}
