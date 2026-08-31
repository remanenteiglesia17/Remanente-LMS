<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Estudiante extends Model
{
    use HasRoles, HasFactory;

    protected $table = "estudiantes";

    protected $guard_name = 'web';

    // Necesario para que 'nombres'/'apellidos' (accessors) aparezcan en las
    // respuestas JSON (response()->json($estudiante)) usadas por los modales
    // de edición/detalle, ya que no son columnas reales de la tabla.
    protected $appends = ['nombres', 'apellidos'];
    // protected $guarded = ['created_at', 'updated_at'];
    protected $fillable = ['cc', 'genero', 'telefono', 'email', 'direccion', 'contacto_emergencia', 'observaciones', 'user_id',];


    public function user()
    {
        return $this->belongsTo(User::class);
    }  // Estudiante pertenece a un Usuario

    /**
     * nombres/apellidos ya NO se guardan en esta tabla (se duplicaban
     * con 'users'). Estos accessors los leen del User asociado para que
     * el resto del código ($estudiante->nombres, etc.) siga funcionando
     * igual que antes sin tener que tocar cada vista/controlador.
     */
    public function getNombresAttribute()
    {
        return $this->user?->name;
    }

    public function getApellidosAttribute()
    {
        return $this->user?->lastname;
    }
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'estudiante_curso')
            ->withPivot('fecha_inscripcion', 'estado');
    }
    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'estudiante_id');
    }
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'estudiante_id');
    }

    public function clases()
    {
        return $this->belongsToMany(Clase::class, 'clase_estudiante')
            ->withTimestamps();
    }
    public function entregas()
    {
        // Un estudiante tiene muchas entregas (una por cada tarea del curso)
        return $this->hasMany(Entrega::class, 'estudiante_id');
    }
    public function cursosEnProgreso()
    {
        return $this->cursos()
            ->wherePivot('estado', 'activo');
    }
    public function cursosCompletados()
    {
        return $this->cursos()
            ->wherePivot('estado', 'aprobado');
    }
    // Calcular porcentaje de asistencia
    public function porcentajeAsistencia($cursoId = null)
    {
        $query = $this->asistencias();

        if ($cursoId) {
            $query->whereHas('clase', function ($q) use ($cursoId) {
                $q->where('curso_id', $cursoId);
            });
        }

        $total = $query->count();
        $presentes = $query->where('estado', 'presente')->count();

        return $total > 0 ? round(($presentes / $total) * 100, 2) : 0;
    }
    /* =====================
     | ACCESOR
     ===================== */

    public function getNombreCompletoAttribute()
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }
}
