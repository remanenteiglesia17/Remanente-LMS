<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Solo administradores pueden iniciar la impersonación. Detenerla no
        // requiere el rol porque, mientras se impersona, el usuario
        // autenticado es el estudiante (sin permisos de administrador).
        $this->middleware(['role:superAdmin|admin|root'])->only('verComoEstudiante');
    }

    /**
     * Inicia sesión "como" el usuario del estudiante indicado, guardando el
     * id del administrador original en sesión para poder volver después.
     */
    public function verComoEstudiante(Estudiante $estudiante)
    {
        if (!$estudiante->user) {
            return redirect()->back()->with([
                'swal' => '1',
                'info' => 'Este estudiante no tiene una cuenta de usuario asociada.',
                'icon' => 'warning',
            ]);
        }

        // Evitar anidar impersonaciones.
        if (session()->has('impersonator_id')) {
            return redirect()->back()->with([
                'swal' => '1',
                'info' => 'Ya estás viendo la plataforma como otro usuario. Vuelve a tu cuenta primero.',
                'icon' => 'warning',
            ]);
        }

        session([
            'impersonator_id' => Auth::id(),
            'impersonator_name' => Auth::user()->name,
        ]);

        Auth::login($estudiante->user);

        return redirect()->route('admin.home')->with([
            'swal' => '1',
            'info' => 'Ahora estás viendo la plataforma como ' . $estudiante->nombres . ' ' . $estudiante->apellidos . '.',
            'icon' => 'info',
        ]);
    }

    /**
     * Termina la impersonación y regresa al administrador a su propia cuenta.
     */
    public function detener()
    {
        $impersonatorId = session('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('admin.home');
        }

        $admin = \App\Models\User::find($impersonatorId);

        session()->forget(['impersonator_id', 'impersonator_name']);

        if ($admin) {
            Auth::login($admin);
        }

        return redirect()->route('admin.home')->with([
            'swal' => '1',
            'info' => 'Volviste a tu cuenta de administrador.',
            'icon' => 'success',
        ]);
    }
}
