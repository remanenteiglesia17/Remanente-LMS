<?php

namespace App\Providers;

use App\Models\Asistencia;
use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\Entrega;
use App\Models\Estudiante;
use App\Models\Modulo;
use App\Models\Profesor;
use App\Models\Tarea;
use App\Models\User;
use App\Observers\AuditoriaObserver;
use App\Observers\TareaObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Tarea::observe(TareaObserver::class);

        // Módulo de auditoría: deja un rastro de creación/edición/eliminación
        // en los modelos académicos y administrativos más sensibles.
        Curso::observe(AuditoriaObserver::class);
        Estudiante::observe(AuditoriaObserver::class);
        Profesor::observe(AuditoriaObserver::class);
        Calificacion::observe(AuditoriaObserver::class);
        Asistencia::observe(AuditoriaObserver::class);
        Tarea::observe(AuditoriaObserver::class);
        Entrega::observe(AuditoriaObserver::class);
        Modulo::observe(AuditoriaObserver::class);
        User::observe(AuditoriaObserver::class);
    }
}
