<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcial extends Model
{
    use HasFactory;

    protected $table = 'parciales';

    protected $fillable = [
        'curso_id',
        'nombre',
        'numero',
        'fecha_inicio',
        'fecha_fin',
        'porcentaje',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'numero' => 'integer',
        'porcentaje' => 'integer',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * Tareas y quices que componen este parcial (n tareas por parcial).
     */
    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }

    /**
     * Nota del parcial para un estudiante: promedio simple de sus
     * calificaciones publicadas dentro de este parcial (las n tareas/quices).
     */
    public function notaEstudiante(int $estudianteId): ?float
    {
        $promedio = $this->calificaciones()
            ->where('estudiante_id', $estudianteId)
            ->where('publicada', true)
            ->avg('nota');

        return $promedio !== null ? round((float) $promedio, 2) : null;
    }
}
