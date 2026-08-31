<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Secretaria extends Model
{
    use  HasRoles, HasFactory;
    protected $guarded = ['created_at', 'updated_at',];

    // Necesario para que 'nombres'/'apellidos' (accessors) aparezcan en las
    // respuestas JSON (response()->json($secretaria)) usadas por los modales.
    protected $appends = ['nombres', 'apellidos'];

    public function user(){ return $this->belongsTo(User::class); } // Muchos a uno inversa

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
}
