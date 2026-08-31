<?php

use App\Http\Controllers\Academico\CursoController;
use App\Http\Controllers\Academico\InscripcionController;
use App\Http\Controllers\Academico\Profesor\CalificacionController;
use App\Http\Controllers\Academico\Profesor\ModuloController as ProfesorModuloController;
use App\Http\Controllers\Academico\Profesor\TareaController;
use App\Http\Controllers\Admin\AuditoriaController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\HorarioController;
use App\Http\Controllers\Admin\ImpersonateController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserProfileController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\SecretariaController;
use Illuminate\Support\Facades\Route;

// RUTAS TOGGLE ACTIVATE / DEACTIVATE
Route::patch('/estudiantes/{id}/toggle-status', [EstudianteController::class, 'toggleStatus'])->name('estudiantes.toggleStatus');
Route::patch('/programador/{id}/toggle-status', [SecretariaController::class, 'toggleStatus'])->name('secretarias.toggleStatus');
Route::patch('/profesor/{id}/toggle-status', [ProfesorController::class, 'toggleStatus'])->name('profesors.toggleStatus');
Route::patch('/curso/{id}/toggle-status', [CursoController::class, 'toggleStatus'])->name('cursos.toggleStatus');

// RUTAS HOME
Route::get('/', [HomeController::class, 'index'])->name('index')->middleware('auth');

// RUTAS CONFIGURACIONES Y PERFIL
Route::resource('/config', ConfigController::class)->names('config');
Route::get('/user/profile', [UserProfileController::class, 'index'])->name('profile.index');
Route::put('/user/profile-information', [UserProfileController::class, 'update'])->name('user-profile-information.update');
Route::put('/user/profile-password', [UserProfileController::class, 'updatePassword'])->name('user-profile-password.updatePassword');

// RUTAS SECRETARIAS
Route::resource('/secretarias', SecretariaController::class)->names('secretarias');

// RUTAS PROFESORES
Route::resource('/profesores', ProfesorController::class)->names('profesores')->parameters(['profesores' => 'profesor']);

// RUTAS ESTUDIANTES
Route::resource('/estudiantes', EstudianteController::class)->names('estudiantes');

// RUTAS CURSOS
Route::get('/cursos/completados', [CursoController::class, 'completados'])->name('cursos.completados');
Route::get('/cursos/mi-curso', function () {
    $user = auth()->user();

    abort_if(!$user->estudiante, 404);

    $curso = $user->estudiante->cursos()->first();

    abort_if(!$curso, 404);

    return redirect()->route('admin.cursos.show', $curso->id);
})->name('mi-curso');

Route::resource('/cursos', CursoController::class)->names('cursos');

// ============================================
// MÓDULO DE ASISTENCIAS (UNIFICADO Y CORREGIDO)
// ============================================
Route::prefix('asistencias')->name('asistencias.')->middleware('auth')->group(function () {
    Route::get('/', [AsistenciaController::class, 'index'])->name('index');
    Route::post('/', [AsistenciaController::class, 'store'])->name('store');
    Route::post('/excusar/{asistenciaId}', [AsistenciaController::class, 'excusar'])->name('excusar');
    Route::get('/estadisticas/{estudianteId}', [AsistenciaController::class, 'estadisticas'])->name('estadisticas');
});

// RUTAS HORARIOS ADMIN Y ESTUDIANTE
Route::resource('/horarios', HorarioController::class)->names('horarios');
Route::get('/horarios/curso/{id}', [HorarioController::class, 'show_datos_cursos'])->name('horarios.show_datos_cursos');
Route::get('/horarios/curso/{id}/datos', [HorarioController::class, 'show_datos_por_curso'])->name('horarios.show_datos_por_curso');
Route::get('/horarios/show_reserva_profesores', [HomeController::class, 'show_reserva_profesores'])->name('horarios.show_reserva_profesores');

// RUTAS CLASES
Route::post('/clases', [ClaseController::class, 'store'])->name('clases.store');
Route::get('/clases/{clase}/edit', [ClaseController::class, 'edit'])->name('clases.edit');
Route::put('/clases/{clase}', [ClaseController::class, 'update'])->name('clases.update');
Route::delete('/clases/{clase}', [ClaseController::class, 'destroy'])->name('clases.destroy');
Route::get('horarios/getCurso/{id}', [HorarioController::class, 'getCursosPorProfesor'])->name('horarios.getCurso');
Route::get('inscripciones/get-profesores/{cursoId}', [InscripcionController::class, 'getProfesoresPorCurso'])->name('inscripciones.get_profesores');
Route::get('/show_reservas/{id}', [HomeController::class, 'show_reservas'])->name('show_reservas');

// ============================================
// MÓDULO DE INSCRIPCIONES
// ============================================
Route::resource('inscripciones', InscripcionController::class)->except(['show']);

Route::prefix('inscripciones')->name('inscripciones.')->group(function () {
    Route::post('multiple', [InscripcionController::class, 'storeMultiple'])->name('store-multiple');
    Route::patch('{id}/estado', [InscripcionController::class, 'cambiarEstado'])->name('cambiar-estado');
    Route::get('curso/{curso}', [InscripcionController::class, 'estudiantesPorCurso'])->name('estudiantes');
    Route::get('estudiante/{estudiante}', [InscripcionController::class, 'cursosPorEstudiante'])->name('cursos');
    Route::delete('{id}', [InscripcionController::class, 'destroy'])->name('destroy');
});

// RUTAS DE APOYO/SELECTS DINÁMICOS
Route::get('/profesores/evento/{cursoId}', [ProfesorController::class, 'obtenerProfesores'])->name('obtenerProfesores');
Route::get('/cursos/evento/{estudianteId}', [CursoController::class, 'obtenerCursos'])->name('obtenerCursos');
Route::get('/estudiantes/{estudiante}/inscripciones', [InscripcionController::class, 'cursosPorEstudiante'])->name('estudiantes.inscripciones');

// ============================================
// MÓDULO PROFESOR (Tareas, Módulos, Calificaciones)
// ============================================
Route::prefix('profesor/tareas')->middleware(['auth', 'role:profesor'])->name('profesor.tareas.')->group(function () {
    Route::get('/', [TareaController::class, 'index'])->name('index');
    Route::get('/create', [TareaController::class, 'create'])->name('create');
    Route::post('/', [TareaController::class, 'store'])->name('store');
    Route::get('/{tarea}', [TareaController::class, 'show'])->name('show');
    Route::get('/{tarea}/edit', [TareaController::class, 'edit'])->name('edit');
    Route::put('/{tarea}', [TareaController::class, 'update'])->name('update');
    Route::delete('/{tarea}', [TareaController::class, 'destroy'])->name('destroy');
});

Route::prefix('profesor/modulos')->middleware(['auth', 'role:profesor'])->name('profesor.modulos.')->group(function () {
    Route::get('/', [ProfesorModuloController::class, 'index'])->name('index');
    Route::post('/', [ProfesorModuloController::class, 'store'])->name('store');
    Route::put('/{modulo}', [ProfesorModuloController::class, 'update'])->name('update');
    Route::patch('/{modulo}/toggle-finalizado', [ProfesorModuloController::class, 'toggleFinalizado'])->name('toggle-finalizado');
    Route::delete('/{modulo}', [ProfesorModuloController::class, 'destroy'])->name('destroy');
});

Route::get('profesor/calificaciones/visual', function () {
    return view('profesor.calificaciones.visual');
})->name('profesor.calificaciones.visual');

Route::post('/profesor/calificaciones/finalizar-curso', [CalificacionController::class, 'finalizarCurso'])
    ->middleware(['auth', 'role:profesor'])
    ->name('profesor.calificaciones.finalizar-curso');

Route::prefix('profesor/calificaciones')->middleware(['auth', 'role:profesor'])->name('profesor.calificaciones.')->group(function () {
    Route::get('/', [CalificacionController::class, 'index'])->name('index');
    Route::post('/registrar', [CalificacionController::class, 'store'])->name('store');
    Route::post('/planilla', [CalificacionController::class, 'guardarPlanilla'])->name('planilla');
    Route::get('/{entrega}/revision', [CalificacionController::class, 'revision'])->name('revision');
    Route::get('/estadisticas/curso/{curso}', [CalificacionController::class, 'estadisticas'])->name('estadisticas');
});

// ============================================
// MÓDULO ADMINISTRACIÓN Y AUDITORÍA
// ============================================
Route::middleware('auth')->group(function () {
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/permissions/{id}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    Route::resource('roles', RoleController::class)->names('roles');
    Route::resource('/users', UserController::class)->names('users');
    Route::patch('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

    Route::get('/auditorias', [AuditoriaController::class, 'index'])->name('auditorias.index');
    Route::get('/auditorias/{auditoria}', [AuditoriaController::class, 'show'])->name('auditorias.show');
});

// NOTIFICACIONES
Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

// IMPERSONACIÓN
Route::get('/impersonate', [ImpersonateController::class, 'index'])->name('impersonate.index');
Route::get('/impersonate/rol/{rol}', [ImpersonateController::class, 'verComoRol'])->name('impersonate.rol');
Route::get('/impersonate/detener', [ImpersonateController::class, 'detener'])->name('impersonate.detener');