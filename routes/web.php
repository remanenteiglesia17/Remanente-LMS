<?php

use App\Http\Controllers\Academico\EntregaController;
use App\Http\Controllers\Academico\Estudiante\ModuloController as EstudianteModuloController;
use App\Http\Controllers\Academico\Estudiante\TareaController;  
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\PerfilCompletarController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\Academico\Estudiante\CalificacionController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\EstudianteController;
use Illuminate\Support\Facades\Route;

Route::get('/admin', [HomeController::class, 'show'])->name('admin.home.show');

Route::middleware('auth')->group(function () {
    Route::get('/completar-perfil', [PerfilCompletarController::class, 'show'])->name('perfil.completar');
    Route::post('/completar-perfil', [PerfilCompletarController::class, 'store'])->name('perfil.completar.store');
});

/* LANDING  * */ Route::get('/', function () {return view('auth.login'); });
/* DASHBOARD * */ Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.home'); }); // ->group(function () {Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');});
/* REGISTER  * */ Route::get('/register', function () {return redirect('/'); });

// Route::get('/admin/cursos/partials', [CursoController::class, 'quemada'])->name('partials.quemada');

Route::get('/admin/cursos/contenido', function () {
    return view('admin.cursos.dynamic.contenido');
})->name('partials.contenido');

Route::resource('/estudiante/tareas', TareaController::class)->names('estudiante.tareas')->middleware(['auth', 'role:estudiante']);
Route::get('/estudiante/modulos', [EstudianteModuloController::class, 'index'])->name('estudiante.modulos.index')->middleware(['auth', 'role:estudiante']);
Route::resource('/estudiante/entregas/', EntregaController::class)->names('estudiante.entregas')->middleware(['auth', 'role:estudiante']);
Route::resource('asistencias', AsistenciaController::class)->only(['index', 'store']);

// ── Asistencias ───────────────────────────────────────────
Route::middleware('auth')->prefix('asistencias')->name('asistencias.')->group(function () {
    Route::get('/', [AsistenciaController::class, 'index'])->name('index');
    Route::post('/', [AsistenciaController::class, 'store'])->name('store');
    Route::post('{asistencia}/excusar', [AsistenciaController::class, 'excusar'])->name('excusar');
    Route::post('rapido', [AsistenciaController::class, 'registrarRapido'])->name('rapido');
    Route::get('estadisticas/{estudiante}', [AsistenciaController::class, 'estadisticas'])->name('estadisticas');
});



// Route::get(
//     'estudiante/tareas/{tarea}',
//     [TareaController::class, 'show']
// )->name('estudiante.tareas.show');

// ============================================
// RUTAS CALIFICACIONES ESTUDIANTES
// ============================================ 
Route::prefix('estudiante/calificaciones')->middleware(['auth', 'role:estudiante'])->name('estudiante.calificaciones.')->group(function () {
    Route::get('/', [CalificacionController::class, 'index'])->name('index');              // Ver todas las calificaciones de sus cursos

    Route::get('/curso/{curso}', [CalificacionController::class, 'porCurso'])->name('por-curso');// Ver calificaciones de un curso específico

    Route::get('/estudiante/{estudiante}/curso/{curso}', [CalificacionController::class, 'porEstudiante'])
                                                    ->name('por-estudiante');              // Ver calificaciones de un estudiante en un curso
    Route::post('/registrar', [CalificacionController::class, 'store'])->name('store');    // Crear/actualizar calificación
    Route::get('/{calificacion}/editar', [CalificacionController::class, 'edit'])->name('edit');
    Route::put('/{calificacion}', [CalificacionController::class, 'update'])->name('update');

    Route::post('/masiva', [CalificacionController::class, 'storeMasiva'])->name('store-masiva');    // Calificar múltiples estudiantes a la vez
    Route::patch('/{calificacion}/publicar', [CalificacionController::class, 'publicar'])->name('publicar');    // Publicar/Ocultar calificaciones para estudiantes
    Route::get('/exportar/curso/{curso}', [CalificacionController::class, 'exportar'])->name('exportar');    // Exportar calificaciones

    Route::get('/estadisticas/curso/{curso}', [CalificacionController::class, 'estadisticas'])->name('estadisticas');    // Estadísticas de calificaciones por curso
}); 

// ========== NOTIFICACIONES ==========
use App\Http\Controllers\NotificacionController;

Route::middleware('auth')->prefix('notificaciones')->name('notificaciones.')->group(function () {
    Route::get('/unread',          [NotificacionController::class, 'unread'])->name('unread');
    Route::get('/{id}/detail',     [NotificacionController::class, 'detail'])->name('detail');
    Route::post('/{id}/read',      [NotificacionController::class, 'markRead'])->name('read');
    Route::post('/read-all',       [NotificacionController::class, 'markAllRead'])->name('read-all');
});


// Ruta para descargar el Certificado PDF (disponible cuando el estudiante completó el curso)
Route::get('/certificado/descargar/{course}', [CertificateController::class, 'generate'])
    ->middleware(['auth', 'role:estudiante'])
    ->name('certificate.download');
