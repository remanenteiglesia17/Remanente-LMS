<?php

namespace App\Listeners;

use App\Models\Auditoria;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class LogAuditoriaAuth
{
    public function handleLogin(Login $event): void
    {
        $this->registrar('login', $event->user);
    }

    public function handleLogout(Logout $event): void
    {
        $this->registrar('logout', $event->user);
    }

    public function handleFailed(Failed $event): void
    {
        $this->registrar('login_failed', $event->user, $event->credentials['email'] ?? null);
    }

    protected function registrar(string $evento, $user = null, ?string $emailIntentado = null): void
    {
        try {
            Auditoria::create([
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? $emailIntentado,
                'user_role' => $user && method_exists($user, 'getRoleNames') ? $user->getRoleNames()->first() : null,
                'event' => $evento,
                'auditable_type' => $user ? get_class($user) : null,
                'auditable_id' => $user?->id,
                'auditable_label' => $user?->email ?? $emailIntentado,
                'old_values' => null,
                'new_values' => null,
                'url' => request()->fullUrl(),
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            Log::warning('No se pudo registrar auditoría de autenticación: ' . $e->getMessage());
        }
    }
}
