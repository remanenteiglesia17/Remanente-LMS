<?php

namespace App\Observers;

use App\Models\Tarea;
use App\Notifications\NuevaTareaNotification;

class TareaObserver
{
    public function created(Tarea $tarea): void
    {
        $tarea->load('curso.estudiantes.user');

        foreach ($tarea->curso->estudiantes as $estudiante) {
            $estudiante->user?->notify(new NuevaTareaNotification($tarea));
        }
    }
}
