<?php

namespace App\Notifications;

use App\Models\Tarea;
use Illuminate\Notifications\Notification;

class NuevaTareaNotification extends Notification
{
    public function __construct(public Tarea $tarea) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'tarea_id'      => $this->tarea->id,
            'titulo'        => $this->tarea->titulo_tarea,
            'curso'         => $this->tarea->curso->nombre ?? 'Curso',
            'tipo'          => $this->tarea->tipo_tarea    ?? null,
            'puntaje'       => $this->tarea->puntaje       ?? null,
            'fecha_entrega' => $this->tarea->fecha_entrega
                                ? \Carbon\Carbon::parse($this->tarea->fecha_entrega)->format('d/m/Y')
                                : null,
            'url' => route('estudiante.tareas.show', $this->tarea->id),
        ];
    }
}
