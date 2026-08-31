<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\Support\AcademicFixtures;
use Tests\TestCase;

/**
 * Verifica que:
 *  1. Las columnas de horas ya no existen en la base de datos (migración).
 *  2. Ya no es obligatorio ingresar horas al crear un curso.
 *  3. Un curso se considera "completado"/"aprobado" según el ESTADO de la
 *     inscripción (que a su vez depende de tareas, exámenes y asistencia),
 *     y no según horas.
 *  4. El flujo real de "el profesor finaliza el curso" aprueba o reprueba
 *     solo con base en el promedio ponderado.
 *  5. El historial de curso solo se puede completar si el estado es
 *     'aprobado'.
 */
class HorasRequeridasEliminadasTest extends TestCase
{
    use RefreshDatabase;
    use AcademicFixtures;

    /**
     * Si esta prueba falla, significa que la migración
     * '2026_08_28_000001_drop_horas_columns_from_cursos_and_pivot' no se
     * corrió (o no se incluyó) en la base de datos de pruebas.
     */
    public function test_las_columnas_de_horas_ya_no_existen_en_la_base_de_datos(): void
    {
        $this->assertFalse(
            Schema::hasColumn('cursos', 'horas_requeridas'),
            "La columna 'horas_requeridas' todavía existe en 'cursos'."
        );

        $this->assertFalse(
            Schema::hasColumn('estudiante_curso', 'horas_realizadas'),
            "La columna 'horas_realizadas' todavía existe en 'estudiante_curso'."
        );
    }

    /**
     * Antes, crear un curso sin 'horas_requeridas' fallaba la validación
     * (era 'required|integer|min:1'). Ahora debe crearse sin problema.
     */
    public function test_se_puede_crear_un_curso_sin_indicar_horas(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.cursos.store'), [
            'codigo' => 'MAT-101',
            'nombre' => 'Matemáticas I',
            'periodo' => '2026-1',
            'estado' => '1',
            'descripcion' => 'Curso introductorio de matemáticas.',
        ]);

        $response->assertSessionDoesntHaveErrors(['horas_requeridas']);
        $this->assertDatabaseHas('cursos', ['codigo' => 'MAT-101']);
    }

    /**
     * 'cursosCompletados()' debe devolver solo los cursos cuya inscripción
     * tiene estado = 'aprobado', sin importar nada relacionado con horas
     * (esa columna ni siquiera existe ya).
     */
    public function test_cursos_completados_del_estudiante_se_basa_en_estado_aprobado(): void
    {
        [, $estudiante] = $this->crearEstudianteConUsuario();

        $cursoAprobado = $this->crearCurso(['codigo' => 'C-APROBADO']);
        $cursoActivo = $this->crearCurso(['codigo' => 'C-ACTIVO']);
        $cursoReprobado = $this->crearCurso(['codigo' => 'C-REPROBADO']);

        $this->inscribir($estudiante, $cursoAprobado, 'aprobado');
        $this->inscribir($estudiante, $cursoActivo, 'activo');
        $this->inscribir($estudiante, $cursoReprobado, 'reprobado');

        $completados = $estudiante->cursosCompletados()->pluck('codigo');

        $this->assertTrue($completados->contains('C-APROBADO'));
        $this->assertFalse($completados->contains('C-ACTIVO'));
        $this->assertFalse($completados->contains('C-REPROBADO'));
    }

    /**
     * Simétrico al anterior: 'cursosEnProgreso()' solo trae los cursos con
     * estado 'activo'.
     */
    public function test_cursos_en_progreso_del_estudiante_se_basa_en_estado_activo(): void
    {
        [, $estudiante] = $this->crearEstudianteConUsuario();

        $cursoActivo = $this->crearCurso(['codigo' => 'C-ACTIVO-2']);
        $cursoAprobado = $this->crearCurso(['codigo' => 'C-APROBADO-2']);

        $this->inscribir($estudiante, $cursoActivo, 'activo');
        $this->inscribir($estudiante, $cursoAprobado, 'aprobado');

        $enProgreso = $estudiante->cursosEnProgreso()->pluck('codigo');

        $this->assertTrue($enProgreso->contains('C-ACTIVO-2'));
        $this->assertFalse($enProgreso->contains('C-APROBADO-2'));
    }

    /**
     * Flujo real de negocio: el profesor da clic en "Finalizar curso".
     * Un estudiante con promedio >= 3.0 debe quedar 'aprobado' y otro con
     * promedio < 3.0 debe quedar 'reprobado' — sin que las horas influyan
     * en nada (ya ni se le puede asignar horas al curso).
     */
    public function test_finalizar_curso_aprueba_o_reprueba_solo_segun_el_promedio(): void
    {
        [$userProfesor, $profesor] = $this->crearProfesorConUsuario();
        [, $estudianteBueno] = $this->crearEstudianteConUsuario(['cc' => '111']);
        [, $estudianteMalo] = $this->crearEstudianteConUsuario(['cc' => '222']);

        $curso = $this->crearCurso(['codigo' => 'FIN-001']);
        $this->asignarProfesorACurso($profesor, $curso);

        $this->inscribir($estudianteBueno, $curso, 'activo');
        $this->inscribir($estudianteMalo, $curso, 'activo');

        $this->calificarConNota($curso, $estudianteBueno, 4.0); // aprueba
        $this->calificarConNota($curso, $estudianteMalo, 2.0);  // reprueba

        $response = $this->actingAs($userProfesor)->post(
            route('admin.profesor.calificaciones.finalizar-curso'),
            ['curso_id' => $curso->id]
        );

        $response->assertSessionHas('swal');

        $this->assertDatabaseHas('estudiante_curso', [
            'estudiante_id' => $estudianteBueno->id,
            'curso_id' => $curso->id,
            'estado' => 'aprobado',
        ]);

        $this->assertDatabaseHas('estudiante_curso', [
            'estudiante_id' => $estudianteMalo->id,
            'curso_id' => $curso->id,
            'estado' => 'reprobado',
        ]);
    }

    /**
     * El historial de curso (usado para certificados/constancias) solo
     * debe dejar completar el registro si el estado de la inscripción es
     * 'aprobado'. Antes se exigía además tener las horas cumplidas.
     *
     * Nota: en este repositorio 'HistorialCursoController' todavía no
     * tiene una ruta registrada (routes/*.php), así que se invoca el
     * controlador directamente en vez de hacer una petición HTTP. Esto no
     * tiene relación con el cambio de horas_requeridas; es una pieza que
     * ya estaba incompleta antes de este parche.
     */
    public function test_no_se_puede_completar_el_historial_si_el_curso_no_esta_aprobado(): void
    {
        [, $estudiante] = $this->crearEstudianteConUsuario();
        $curso = $this->crearCurso(['codigo' => 'HIST-001']);
        $this->inscribir($estudiante, $curso, 'activo');

        $controller = app(\App\Http\Controllers\HistorialCursoController::class);
        $response = $controller->completarCurso($estudiante->id, $curso->id);

        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * Este caso (estado 'aprobado' -> se puede completar) requiere además
     * una tabla 'historial_cursos', que tampoco existe todavía en las
     * migraciones de este repositorio (solo existe el modelo
     * App\Models\HistorialCurso). Se deja el test ya escrito y solo se
     * omite hasta que exista esa migración, para no reportar un fallo que
     * no tiene que ver con 'horas_requeridas'.
     */
    public function test_se_puede_completar_el_historial_cuando_el_curso_esta_aprobado(): void
    {
        if (!Schema::hasTable('historial_cursos')) {
            $this->markTestSkipped(
                "Falta la migración de 'historial_cursos' (pendiente desde antes de este cambio)."
            );
        }

        [, $estudiante] = $this->crearEstudianteConUsuario();
        $curso = $this->crearCurso(['codigo' => 'HIST-002']);
        $this->inscribir($estudiante, $curso, 'aprobado');

        $controller = app(\App\Http\Controllers\HistorialCursoController::class);
        $response = $controller->completarCurso($estudiante->id, $curso->id);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertDatabaseHas('historial_cursos', [
            'estudiante_id' => $estudiante->id,
            'curso_id' => $curso->id,
        ]);
    }

    /**
     * User::hasCompletedCourse() debe confiar primero en el estado
     * ('aprobado' marcado por el profesor) sin importar el promedio.
     */
    public function test_has_completed_course_prioriza_el_estado_aprobado(): void
    {
        [$userEstudiante, $estudiante] = $this->crearEstudianteConUsuario();
        $curso = $this->crearCurso(['codigo' => 'HCC-001']);

        $this->inscribir($estudiante, $curso, 'aprobado');
        $this->calificarConNota($curso, $estudiante, 1.0); // nota mala, no importa

        $this->assertTrue($userEstudiante->hasCompletedCourse($curso));
    }

    /**
     * Si el profesor no ha marcado el estado explícitamente ('activo'),
     * hasCompletedCourse() debe calcular el promedio ponderado real.
     */
    public function test_has_completed_course_usa_el_promedio_si_no_hay_estado_marcado(): void
    {
        [$userEstudiante, $estudiante] = $this->crearEstudianteConUsuario();
        $curso = $this->crearCurso(['codigo' => 'HCC-002']);

        $this->inscribir($estudiante, $curso, 'activo');
        $this->calificarConNota($curso, $estudiante, 4.0);

        $this->assertTrue($userEstudiante->hasCompletedCourse($curso));
    }
}
