<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

// class User extends Authenticatable implements MustVerifyEmail //AL MOMENTO DE UN USUARIO NUEVO INICIAR SESSION LE PEDIRIA VALIDAR EL CORREO
class User extends Authenticatable // AQUI ESTA DESACTIVADO
{
    use HasRoles;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = ['name', 'apellido', 'email', 'password'];

    protected $hidden = ['password', 'remember_token', 'two_factor_recovery_codes', 'two_factor_secret'];

    protected $casts = ['email_verified_at' => 'datetime'];

    protected $appends = ['profile_photo_url']; // ADMINLTE

    public function clases()
    {
        return $this->hasMany(Clase::class);
    }  // Uno a Muchos

    public function estudiante()
    {
        return $this->hasOne(Estudiante::class);
    } // Uno a Uno

    public function profesor()
    {
        return $this->hasOne(Profesor::class, 'user_id');
    } // Uno a Uno

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }// Uno a Muchos

    public function secretaria()
    {
        return $this->hasOne(Secretaria::class);
    }// Uno a Uno

    public function adminlte_image()
    {
        return url($this->profile_photo_url);
    } // USER PICTURE

    public function adminlte_profile_url()
    {
        return url('user/profile');
    }

    public function adminlte_desc()
    {
        return $this->roles->pluck('name')->implode(', ');
    } // RETURN ROLE

    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'user_id');
    }

    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'estudiante_curso')
            ->withPivot([
                'fecha_inscripcion',
                'horas_realizadas',
            ])
            ->withTimestamps();
    }

    /**
     * Determina si el usuario completó un curso.
     * Condición: el estado del pivot es 'aprobado' O (nota >= 3.0 Y horas completas).
     * El profesor puede marcar como aprobado desde Inscripciones.
     */
    public function hasCompletedCourse(\App\Models\Curso $course): bool
    {
        $estudiante = $this->estudiante;
        if (!$estudiante) return false;

        $pivot = $estudiante->cursos()
            ->where('cursos.id', $course->id)
            ->withPivot('horas_realizadas', 'estado')
            ->first();

        if (!$pivot) return false;

        // Si el profesor marcó explícitamente como aprobado → directo
        if ($pivot->pivot->estado === 'aprobado') return true;

        // Si fue marcado como retirado o reprobado → no
        if (in_array($pivot->pivot->estado, ['retirado', 'reprobado'])) return false;

        // Condición principal: nota ponderada >= 3.0
        $promedio = \App\Models\Calificacion::promedioPonderadoEstudianteCurso(
            $estudiante->id,
            $course->id
        );

        return $promedio >= 3.0;
    }
}
