<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class ImpersonateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:superAdmin|admin|root'])->only(['index', 'verComoRol']);
    }

    public function index()
    {
        $roles = Role::whereNotIn('name', ['superAdmin', 'root'])->get();
        return view('admin.impersonate.index', compact('roles'));
    }

    public function verComoRol(string $rolNombre)
    {
        if (session()->has('impersonator_id')) {
            return redirect()->back()->with([
                'swal' => '1',
                'info' => 'Ya estás viendo la plataforma como otro usuario. Vuelve a tu cuenta primero.',
                'icon' => 'warning',
            ]);
        }

        // 1. Buscar un usuario que tenga este rol asignado
        $usuarioConRol = User::role($rolNombre)->first();

        // Validar si el usuario existe
        if (!$usuarioConRol) {
            return redirect()->back()->with([
                'swal' => '1',
                'info' => "No existe ningún usuario registrado con el rol '{$rolNombre}'.",
                'icon' => 'warning',
            ]);
        }

        // 2. Guardar datos de la sesión del Administrador original
        session([
            'impersonator_id' => Auth::id(),
            'impersonator_name' => Auth::user()->name,
            'impersonated_role' => $rolNombre,
        ]);

        // 3. Iniciar sesión como el usuario con el rol objetivo
        Auth::login($usuarioConRol);

        // 3.1 IMPORTANTE: limpiar el hash de contraseña que guarda
        // Laravel\Jetstream\Http\Middleware\AuthenticateSession en la
        // sesión (password_hash_{guard}). Si no se limpia, en la
        // siguiente petición ese middleware detecta que el hash en
        // sesión (el del admin original) no coincide con el del usuario
        // actual (el impersonado) y cierra la sesión automáticamente,
        // rebotando al login.
        $this->sincronizarHashDeSesion();

        // 4. Redireccionar según el rol (evita rebotar al login si no tiene acceso a admin.home)
        $rutaDestino = route('admin.home'); // Ruta por defecto

        // Si es estudiante o un rol sin acceso al panel de admin, redirigir a su vista correspondiente
        if ($rolNombre === 'estudiante' || $rolNombre === 'Estudiante') {
            $rutaDestino = route('admin.home'); // Cambia por la ruta de tu panel de estudiante si es distinta (ej: route('estudiante.dashboard'))
        }

        return redirect($rutaDestino)->with([
            'swal' => '1',
            'info' => "Ahora estás viendo la plataforma con el rol de {$rolNombre} ({$usuarioConRol->name}).",
            'icon' => 'info',
        ]);
    }

    public function detener()
    {
        $impersonatorId = session('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('admin.home');
        }

        $admin = User::find($impersonatorId);

        session()->forget(['impersonator_id', 'impersonator_name', 'impersonated_role']);

        if ($admin) {
            Auth::login($admin);

            // Ver comentario en verComoRol(): limpiamos también aquí
            // al volver a la cuenta del administrador.
            $this->sincronizarHashDeSesion();
        }

        return redirect()->route('admin.home')->with([
            'swal' => '1',
            'info' => 'Volviste a tu cuenta de administrador.',
            'icon' => 'success',
        ]);
    }

    /**
     * Olvida cualquier hash de contraseña cacheado en sesión
     * (password_hash_web, password_hash_sanctum, etc.).
     *
     * Laravel\Jetstream\Http\Middleware\AuthenticateSession (usado en
     * config('jetstream.auth_session'), aplicado a la ruta 'admin.home')
     * guarda en sesión el hash de contraseña del usuario autenticado bajo
     * la clave 'password_hash_{guard}', y en cada petición lo compara
     * contra Auth::user()->getAuthPassword(); si no coincide, cierra la
     * sesión.
     *
     * El detalle importante es que el "guard" usado para esa clave NO es
     * siempre el mismo: cuando el middleware 'auth:sanctum' autentica con
     * éxito, Laravel ejecuta internamente Auth::shouldUse('sanctum'), lo
     * que cambia el guard "por defecto" a 'sanctum' para el resto de la
     * petición. Por eso, tras redirigir a admin.home, la clave revisada
     * es 'password_hash_sanctum' (no 'password_hash_web'), aunque
     * Auth::login() en este controlador se haga sobre el guard 'web'.
     *
     * Si solo actualizáramos 'password_hash_web' tras el Auth::login(),
     * la clave 'password_hash_sanctum' seguiría teniendo el hash del
     * usuario anterior (admin u original) y el middleware seguiría
     * cerrando la sesión al comparar contra el usuario recién logueado.
     *
     * La solución robusta -y agnóstica del nombre del guard- es olvidar
     * TODAS las claves 'password_hash_*': el propio middleware, al no
     * encontrar la clave, la vuelve a crear con el hash del usuario
     * actualmente autenticado antes de hacer cualquier comparación.
     */
    private function sincronizarHashDeSesion(): void
    {
        collect(session()->all())
            ->keys()
            ->filter(fn ($key) => str_starts_with($key, 'password_hash_'))
            ->each(fn ($key) => session()->forget($key));
    }
}