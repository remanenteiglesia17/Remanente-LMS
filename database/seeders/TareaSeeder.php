<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Tarea;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TareaSeeder extends Seeder
{
    /**
     * Crea las tareas/quizzes/parciales/proyecto de cada curso, ligadas
     * a su módulo correspondiente. Esquema de pesos (suma 100%):
     *   - 3 Tareas   -> 30% (10% c/u)
     *   - 2 Quizzes  -> 20% (10% c/u)
     *   - 2 Parciales-> 30% (15% c/u)
     *   - 1 Proyecto -> 20%
     */
    public function run(): void
    {
        $inicioCurso = Carbon::now()->subWeeks(6); // el curso empezó hace 6 semanas

        $contenido = [
            'ANT-2026' => [
                'tareas' => [
                    'Análisis del relato de la Creación (Génesis 1-2)',
                    'Resumen de la vida de los Patriarcas',
                    'El Éxodo y la Ley Mosaica',
                ],
                'quizzes' => ['Quiz: El Pentateuco', 'Quiz: Libros Históricos'],
                'parciales' => ['Parcial I: Pentateuco y Libros Históricos', 'Parcial II: Literatura Poética y Profetas'],
                'proyecto' => 'Proyecto: Línea de tiempo del Antiguo Testamento',
            ],
            'EV-2026' => [
                'tareas' => [
                    'Mi testimonio personal escrito',
                    'Práctica de las 4 Leyes Espirituales',
                    'Plan de seguimiento a un nuevo creyente',
                ],
                'quizzes' => ['Quiz: Fundamentos bíblicos del evangelismo', 'Quiz: Objeciones comunes a la fe'],
                'parciales' => ['Parcial I: Fundamentos del Evangelismo', 'Parcial II: Técnicas de Acercamiento'],
                'proyecto' => 'Proyecto: Campaña de evangelismo en la comunidad',
            ],
            'TEO-2026' => [
                'tareas' => [
                    'Ensayo sobre la inspiración de las Escrituras',
                    'Los atributos comunicables e incomunicables de Dios',
                    'Cuadro comparativo: naturaleza humana antes y después de la caída',
                ],
                'quizzes' => ['Quiz: Bibliología', 'Quiz: Teología Propia'],
                'parciales' => ['Parcial I: Bibliología', 'Parcial II: Teología Propia y Trinidad'],
                'proyecto' => 'Proyecto: Monografía sobre un atributo de Dios',
            ],
        ];

        foreach ($contenido as $codigoCurso => $data) {
            $curso = Curso::where('codigo', $codigoCurso)->first();
            if (!$curso) {
                continue;
            }

            $modulos = Modulo::where('curso_id', $curso->id)->orderBy('orden')->get();
            if ($modulos->count() < 3) {
                continue;
            }
            [$m1, $m2, $m3] = [$modulos[0], $modulos[1], $modulos[2]];

            // ---------- Módulo 1: Tarea 1, Tarea 2, Quiz 1 ----------
            $this->crearTarea($curso->id, $m1->id, 'tarea', $data['tareas'][0], $inicioCurso->copy()->addWeek(), 10);
            $this->crearTarea($curso->id, $m1->id, 'tarea', $data['tareas'][1], $inicioCurso->copy()->addWeeks(2), 10);
            $this->crearTarea($curso->id, $m1->id, 'quiz', $data['quizzes'][0], $inicioCurso->copy()->addWeeks(2)->addDays(2), 10);

            // ---------- Módulo 2: Tarea 3, Quiz 2, Parcial I ----------
            $this->crearTarea($curso->id, $m2->id, 'tarea', $data['tareas'][2], $inicioCurso->copy()->addWeeks(3), 10);
            $this->crearTarea($curso->id, $m2->id, 'quiz', $data['quizzes'][1], $inicioCurso->copy()->addWeeks(3)->addDays(3), 10);
            $this->crearTarea($curso->id, $m2->id, 'examen', $data['parciales'][0], $inicioCurso->copy()->addWeeks(4), 15);

            // ---------- Módulo 3 (en curso): Parcial II (recién cerrado), Proyecto (aún abierto) ----------
            $this->crearTarea($curso->id, $m3->id, 'examen', $data['parciales'][1], Carbon::now()->subDays(2), 15);
            $this->crearTarea($curso->id, $m3->id, 'proyecto', $data['proyecto'], Carbon::now()->addWeeks(2), 20, Carbon::now()->subWeeks(2));
        }
    }

    private function crearTarea(int $cursoId, int $moduloId, string $tipo, string $titulo, Carbon $fechaEntrega, float $puntaje, ?Carbon $aperturaOverride = null): void
    {
        Tarea::create([
            'curso_id' => $cursoId,
            'modulo_id' => $moduloId,
            'tipo' => $tipo,
            'fecha_apertura' => $aperturaOverride ?? $fechaEntrega->copy()->subDays(7),
            'titulo_tarea' => $titulo,
            'descripcion_tarea' => match ($tipo) {
                'quiz' => 'Evaluación corta de opción múltiple sobre el contenido del módulo.',
                'examen' => 'Evaluación parcial escrita sobre los temas vistos hasta la fecha.',
                'proyecto' => 'Trabajo final integrador del módulo, entregable individual.',
                default => 'Actividad de aplicación sobre el contenido revisado en clase.',
            },
            'requisitos' => 'Haber revisado el material del módulo correspondiente.',
            'criterios_evaluacion' => 'Comprensión del contenido, argumentación y cumplimiento de los requisitos.',
            'fecha_entrega' => $fechaEntrega,
            'permite_entregas_tardias' => $tipo === 'tarea' || $tipo === 'proyecto',
            'penalizacion_tardia' => $tipo === 'tarea' || $tipo === 'proyecto' ? 10 : 0,
            'visible' => true,
            'intentos_permitidos' => 1,
            'formato_entrega' => $tipo === 'quiz' ? 'texto' : 'archivo',
            'formatos_aceptados' => $tipo === 'quiz' ? null : '.pdf,.docx',
            'tamanio_maximo' => 20,
            'puntaje' => $puntaje,
        ]);
    }
}
