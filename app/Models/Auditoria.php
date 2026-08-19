<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    /**
     * Los registros de auditoría son inmutables: solo interesa cuándo se crearon.
     */
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'event',
        'auditable_type',
        'auditable_id',
        'auditable_label',
        'old_values',
        'new_values',
        'url',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Nombre corto del modelo auditado (ej. "Curso" en vez de "App\Models\Curso").
     */
    public function getAuditableModelNameAttribute(): string
    {
        return $this->auditable_type ? class_basename($this->auditable_type) : '—';
    }

    /**
     * Etiqueta legible del tipo de evento (versión estática, útil para selects de filtros).
     */
    public static function eventoLabelPara(string $evento): string
    {
        return match ($evento) {
            'created' => 'Creación',
            'updated' => 'Actualización',
            'deleted' => 'Eliminación',
            'login' => 'Inicio de sesión',
            'logout' => 'Cierre de sesión',
            'login_failed' => 'Intento fallido de inicio de sesión',
            default => ucfirst($evento),
        };
    }

    /**
     * Etiqueta legible del tipo de evento.
     */
    public function getEventoLabelAttribute(): string
    {
        return static::eventoLabelPara($this->event);
    }

    /**
     * Clase de color (badge AdminLTE) según el tipo de evento.
     */
    public function getEventoColorAttribute(): string
    {
        return match ($this->event) {
            'created' => 'success',
            'updated' => 'warning',
            'deleted' => 'danger',
            'login' => 'info',
            'logout' => 'secondary',
            'login_failed' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Campos cuyo valor cambió entre old_values y new_values (solo aplica a "updated").
     */
    public function getCamposModificadosAttribute(): array
    {
        if ($this->event !== 'updated' || !$this->old_values || !$this->new_values) {
            return [];
        }

        $campos = [];
        foreach ($this->new_values as $campo => $valorNuevo) {
            $campos[$campo] = [
                'anterior' => $this->old_values[$campo] ?? null,
                'nuevo' => $valorNuevo,
            ];
        }

        return $campos;
    }
}
