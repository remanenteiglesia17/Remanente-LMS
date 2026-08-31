<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    use HasFactory;
    protected $table = 'profesors';  

    protected $fillable = ['telefono', 'user_id',];

    // Necesario para que 'nombres'/'apellidos' (accessors) aparezcan en las
    // respuestas JSON (response()->json($profesor)) usadas por los modales.
    protected $appends = ['nombres', 'apellidos'];

    public function clases(){     return $this->hasMany(Clase::class);}  // Uno a muchos
    public function user(){       return $this->belongsTo(User::class);} // Muchos a uno

    /**
     * nombres/apellidos ya NO se guardan en esta tabla (se duplicaban con
     * 'users'). Se leen del User asociado.
     */
    public function getNombresAttribute()
    {
        return $this->user?->name;
    }

    public function getApellidosAttribute()
    {
        return $this->user?->lastname;
    }

    public function cursos()   // Muchos a Muchos inverso
    { return $this->belongsToMany(Curso::class, 'horario_profesor_curso', 'profesor_id', 'curso_id'); }

    public function horarios() // Muchos a Muchos inverso
    { return $this->belongsToMany(Horario::class, 'horario_profesor_curso', 'profesor_id', 'horario_id'); }

    /**
     * Solo profesores cuyo usuario TODAVÍA tiene asignado el rol
     * 'profesor'. Cuando a un usuario se le quita ese rol (p. ej. porque
     * pasó a ser 'estudiante'), su registro en 'profesors' se conserva
     * para no romper el historial (clases, calificaciones, horarios ya
     * dictados), pero no debe seguir apareciendo como disponible para
     * asignarle NUEVOS horarios/cursos.
     */
    public function scopeConRolVigente($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->role('profesor');
        });
    }
    // public function historial()
    // {
    //     return $this->hasMany(Historial::class);
    // }
    // public function pagos()
    // {
    //     return $this->hasMany(Pago::class);
    // }
}
