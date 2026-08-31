<?php

namespace Tests\Support;

use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Horario;
use App\Models\Modulo;
use App\Models\Profesor;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * Helpers para construir datos académicos mínimos y válidos en los tests.
 *
 * Se centralizan aquí porque crear un "curso con nota X" a mano implica
 * tocar 4-5 tablas relacionadas (curso, módulo, tarea, calificación...),
 * y repetir eso en cada test lo haría ilegible. Cada método crea solo lo
 * estrictamente necesario para que el resto del sistema (relaciones,
 * cálculo de promedio, etc.) funcione igual que en producción.
 */
trait AcademicFixtures
{
    /**
     * Crea un curso válido. Ya NO incluye 'horas_requeridas': si alguna
     * prueba intentara pasarlo, Eloquent simplemente lo ignoraría porque
     * el campo no está en $fillable ni en la tabla.
     */
    protected function crearCurso(array $overrides = []): Curso
    {
        return Curso::create(array_merge([
            'codigo' => 'TST-' . uniqid(),
            'nombre' => 'Curso de prueba',
            'descripcion' => 'Curso generado por un test automatizado.',
            'periodo' => '2026-1',
            'estado' => true,
        ], $overrides));
    }

    /**
     * Crea un usuario + su registro de Estudiante asociado.
     *
     * @return array{0: User, 1: Estudiante}
     */
    protected function crearEstudianteConUsuario(array $overrides = []): array
    {
        $user = User::factory()->create();

        $estudiante = Estudiante::create(array_merge([
            'nombres' => 'Estudiante',
            'apellidos' => 'De Prueba',
            'cc' => (string) random_int(10000000, 99999999),
            'genero' => 'O',
            'telefono' => '3000000000',
            'direccion' => 'Calle Falsa 123',
            'user_id' => $user->id,
        ], $overrides));

        if (class_exists(Role::class)) {
            $role = Role::firstOrCreate(['name' => 'estudiante', 'guard_name' => 'web']);
            $user->assignRole($role);
        }

        return [$user, $estudiante];
    }

    /**
     * Crea un usuario + su registro de Profesor asociado, con el rol
     * 'profesor' asignado (lo exige el middleware de las rutas de
     * calificaciones del profesor).
     *
     * @return array{0: User, 1: Profesor}
     */
    protected function crearProfesorConUsuario(array $overrides = []): array
    {
        $user = User::factory()->create();

        $profesor = Profesor::create(array_merge([
            'nombres' => 'Profesor',
            'apellidos' => 'De Prueba',
            'telefono' => '3000000001',
            'user_id' => $user->id,
        ], $overrides));

        $role = Role::firstOrCreate(['name' => 'profesor', 'guard_name' => 'web']);
        $user->assignRole($role);

        return [$user, $profesor];
    }

    /**
     * Inscribe a un estudiante en un curso con un estado determinado
     * ('activo', 'aprobado', 'reprobado', 'retirado'). Esto reemplaza lo
     * que antes se controlaba con 'horas_realizadas'.
     */
    protected function inscribir(Estudiante $estudiante, Curso $curso, string $estado = 'activo'): void
    {
        DB::table('estudiante_curso')->updateOrInsert(
            ['estudiante_id' => $estudiante->id, 'curso_id' => $curso->id],
            [
                'fecha_inscripcion' => now(),
                'estado' => $estado,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    /**
     * Vincula un profesor a un curso (vía horario_profesor_curso), que es
     * lo que 'finalizarCurso' usa para comprobar que el profesor tiene
     * acceso a ese curso.
     */
    protected function asignarProfesorACurso(Profesor $profesor, Curso $curso): void
    {
        $horario = Horario::create([
            'dia' => 'lunes',
            'hora_inicio' => '08:00:00',
            'hora_fin' => '10:00:00',
            'profesor_id' => $profesor->id,
        ]);

        DB::table('horario_profesor_curso')->insert([
            'horario_id' => $horario->id,
            'curso_id' => $curso->id,
            'profesor_id' => $profesor->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Da al estudiante una única calificación publicada en el curso, de
     * forma que 'promedioPonderadoEstudianteCurso' devuelva exactamente
     * $nota (con un solo módulo y una sola categoría con peso 100%, el
     * cálculo ponderado se reduce a la nota tal cual).
     */
    protected function calificarConNota(Curso $curso, Estudiante $estudiante, float $nota): void
    {
        $modulo = Modulo::create([
            'curso_id' => $curso->id,
            'nombre' => 'Módulo único',
            'orden' => 1,
            'peso_tarea' => 100,
            'peso_quiz' => 0,
            'peso_examen' => 0,
            'peso_proyecto' => 0,
            'peso_foro' => 0,
        ]);

        // 'puntaje' en tareas es decimal(3,2) -> máx 9.99, así que se usa
        // un valor pequeño; no afecta el cálculo del promedio ponderado
        // (ese cálculo usa 'nota'/'nota_maxima' de la calificación, no
        // 'puntaje' de la tarea).
        $tarea = Tarea::create([
            'curso_id' => $curso->id,
            'modulo_id' => $modulo->id,
            'tipo' => 'tarea',
            'titulo_tarea' => 'Tarea única - ' . uniqid(),
            'fecha_entrega' => now()->subDay(),
            'puntaje' => 5,
            'visible' => true,
        ]);

        // 'calificacions.profesor_id' es una FK obligatoria (no nullable),
        // así que se crea un profesor mínimo solo para satisfacerla.
        $profesor = Profesor::create([
            'nombres' => 'Profesor',
            'apellidos' => 'Calificador',
            'telefono' => '3000000002',
            'user_id' => User::factory()->create()->id,
        ]);

        Calificacion::create([
            'estudiante_id' => $estudiante->id,
            'curso_id' => $curso->id,
            'profesor_id' => $profesor->id,
            'tarea_id' => $tarea->id,
            'concepto' => 'Tarea única - ' . uniqid(),
            'nota' => $nota,
            'nota_maxima' => 5,
            'tipo_evaluacion' => 'tarea',
            'periodo' => $curso->periodo,
            'fecha_calificacion' => now(),
            'publicada' => true,
        ]);
    }
}
