<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table = 'modulos';

    protected $fillable = [
        'curso_id',
        'nombre',
        'descripcion',
        'orden',
        'finalizado',
        'finalizado_at',
    ];

    protected $casts = [
        'finalizado' => 'boolean',
        'finalizado_at' => 'datetime',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }

    /**
     * El módulo 1 (el de menor orden) siempre está desbloqueado.
     * Cualquier otro módulo se desbloquea solo cuando el módulo
     * anterior (en orden) fue marcado como finalizado por el profesor.
     */
    public function estaDesbloqueado(): bool
    {
        $anterior = Modulo::where('curso_id', $this->curso_id)
            ->where('orden', '<', $this->orden)
            ->orderByDesc('orden')
            ->first();

        return $anterior === null || $anterior->finalizado;
    }
}
