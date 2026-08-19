<?php

namespace App\Observers;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Observer reutilizable: se adjunta a cualquier modelo (ver AppServiceProvider::boot())
 * y deja un rastro en la tabla "auditorias" cada vez que se crea, edita o elimina un registro.
 */
class AuditoriaObserver
{
    /**
     * Atributos que nunca deben quedar guardados en la auditoría.
     */
    protected array $ocultos = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'created_at',
        'updated_at',
    ];

    public function created($model): void
    {
        $this->registrar('created', $model, null, $this->filtrar($model->getAttributes()));
    }

    public function updated($model): void
    {
        $cambios = $this->filtrar($model->getChanges());

        if (empty($cambios)) {
            return; // No hubo cambios reales (ej. solo se tocó updated_at)
        }

        $anteriores = $this->filtrar(
            collect($model->getOriginal())->only(array_keys($cambios))->toArray()
        );

        $this->registrar('updated', $model, $anteriores, $cambios);
    }

    public function deleted($model): void
    {
        $this->registrar('deleted', $model, $this->filtrar($model->getOriginal()), null);
    }

    protected function filtrar(array $atributos): array
    {
        return collect($atributos)->except($this->ocultos)->toArray();
    }

    protected function registrar(string $evento, $model, ?array $anteriores, ?array $nuevos): void
    {
        try {
            Auditoria::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::check() ? trim((string) Auth::user()->name) : null,
                'user_role' => Auth::check() ? Auth::user()->getRoleNames()->first() : null,
                'event' => $evento,
                'auditable_type' => get_class($model),
                'auditable_id' => $model->getKey(),
                'auditable_label' => $this->etiqueta($model),
                'old_values' => $anteriores,
                'new_values' => $nuevos,
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            // La auditoría nunca debe romper la operación principal del usuario.
            Log::warning('No se pudo registrar auditoría: ' . $e->getMessage());
        }
    }

    /**
     * Intenta obtener una etiqueta legible del registro auditado (nombre, título, etc.).
     */
    protected function etiqueta($model): ?string
    {
        foreach (['nombre', 'name', 'titulo', 'titulo_tarea', 'concepto', 'email'] as $campo) {
            if (isset($model->{$campo}) && is_string($model->{$campo})) {
                return $model->{$campo};
            }
        }

        // Ej. Estudiante/Profesor con nombres y apellidos por separado
        if (isset($model->nombres) || isset($model->apellidos)) {
            return trim(($model->nombres ?? '') . ' ' . ($model->apellidos ?? ''));
        }

        return null;
    }
}
