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
        'fecha_inicio',
        'fecha_fin',
        'peso_tarea',
        'peso_quiz',
        'peso_examen',
        'peso_proyecto',
        'peso_foro',
    ];

    protected $casts = [
        'finalizado' => 'boolean',
        'finalizado_at' => 'datetime',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'peso_tarea' => 'decimal:2',
        'peso_quiz' => 'decimal:2',
        'peso_examen' => 'decimal:2',
        'peso_proyecto' => 'decimal:2',
        'peso_foro' => 'decimal:2',
    ];

    /**
     * Ponderación por categoría configurada para este módulo, indexada por
     * el mismo valor que usa Tarea::tipo (tarea, quiz, examen, proyecto,
     * foro).
     */
    public function pesosPorCategoria(): array
    {
        return [
            'tarea'    => (float) $this->peso_tarea,
            'quiz'     => (float) $this->peso_quiz,
            'examen'   => (float) $this->peso_examen,
            'proyecto' => (float) $this->peso_proyecto,
            'foro'     => (float) $this->peso_foro,
        ];
    }

    /**
     * Los pesos de las 5 categorías deben sumar 100% para que el módulo
     * tenga una ponderación válida antes de empezar.
     */
    public function pesosCategoriaSuman100(): bool
    {
        return abs(array_sum($this->pesosPorCategoria()) - 100) < 0.01;
    }

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
