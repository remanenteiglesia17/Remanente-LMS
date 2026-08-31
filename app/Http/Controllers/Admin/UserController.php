<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organismo;
use App\Models\Profesor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash; // <-- Agregar esta línea
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.users.index')->only('index');
        $this->middleware('can:admin.users.create')->only('store');
        $this->middleware('can:admin.users.edit')->only(['edit', 'update']);
        $this->middleware('can:admin.users.destroy')->only('toggleStatus');
    }
    public function index()
    {
       if (auth()->user()->hasRole('root')) {
            $users = User::where('id', '!=', Auth::id())
                ->get();
        } else {
            $users = User::with('roles')->whereDoesntHave('roles', function ($q) {
                    $q->whereIn('name', ['root', 'superAdmin']);})
                ->where('id', '!=', Auth::id())
                ->get();
        }

        $roles = Auth::user()->hasRole('root')
            ? Role::all()
            : Role::whereNotIn('name', ['root', 'superAdmin'])->get();

        return view('admin.users.index', compact('users', 'roles'));
    }
    // public function create() {  return view('admin.users.create'); }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:250',
            'apellido' => 'required|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8|max:250|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $actor = Auth::user();

        // Misma regla que en update(): solo 'root' puede otorgar root/superAdmin.
        $rolesPermitidos = $actor->hasRole('root')
            ? Role::pluck('id')
            : Role::whereNotIn('name', ['root', 'superAdmin'])->pluck('id');

        $rolesSolicitados = collect($request->roles ?? []);
        $rolesNoPermitidos = $rolesSolicitados->diff($rolesPermitidos);

        if ($rolesNoPermitidos->isNotEmpty()) {
            abort(403, 'No tiene permiso para asignar uno o más de los roles seleccionados.');
        }

        $usuario = new User();
        $usuario->name = $request->name;
        $usuario->lastname = $request->apellido;
        $usuario->email = $request->email;
        $usuario->password = Hash::make($request->password);
        $usuario->save();

        if ($rolesSolicitados->isNotEmpty()) {
            $usuario->roles()->sync($rolesSolicitados);
        }

        $aviso = $this->sincronizarRegistroDeRol($usuario, $rolesSolicitados);

        return redirect()->route('admin.users.index')->with(array_merge(
            ['toast'=>2,'title' => 'Exito','info' => 'Se registro al usuario de forma correcta','icon'=>'success'],
            $aviso
        ));
    }

    /**
     * Cuando desde este formulario genérico se asigna el rol 'profesor' a un
     * usuario, garantizamos que exista su registro en la tabla 'profesors'
     * (varias pantallas del panel de profesor asumen que Auth::user()->profesor
     * no es null). El teléfono no se pide aquí, así que queda vacío y se avisa
     * al admin para que lo complete desde "Gestión de Profesores".
     *
     * Los roles 'estudiante' y 'secretaria' NO se auto-crean aquí porque sus
     * tablas exigen datos que este formulario no captura (cédula única,
     * dirección, fecha de nacimiento, etc.). Para esos roles se debe usar el
     * módulo dedicado (Estudiantes / Secretarias), y aquí solo se advierte.
     */
    private function sincronizarRegistroDeRol(User $usuario, $rolesSolicitadosIds): array
    {
        if ($rolesSolicitadosIds->isEmpty()) {
            return [];
        }

        $nombresRoles = Role::whereIn('id', $rolesSolicitadosIds)->pluck('name');

        if ($nombresRoles->contains('profesor') && !$usuario->fresh()->profesor) {
            Profesor::create([
                'telefono'  => '',
                'user_id'   => $usuario->id,
            ]);

            return ['warning' => 'Se creó el registro de profesor, pero falta el teléfono: complétalo en "Gestión de Profesores".'];
        }

        if (($nombresRoles->contains('estudiante') || $nombresRoles->contains('secretaria')) && !$usuario->fresh()->estudiante && !$usuario->fresh()->secretaria) {
            return ['warning' => 'Este rol requiere datos adicionales (cédula, dirección, etc.) que este formulario no pide. Completa el registro desde el módulo de Estudiantes o Secretarias.'];
        }

        return [];
    }
    // public function show($id) {}
    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);

        $roles = Auth::user()->hasRole('root')
            ? Role::all()
            : Role::whereNotIn('name', ['root', 'superAdmin'])->get();

        return response()->json(['user' => $user, 'roles' => $roles]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|max:250',
            'apellido' => 'nullable|max:250',
            'email' => 'required|email|max:250|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|max:250|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        $actor = Auth::user();

        // Solo 'root' puede otorgar (o mantener) los roles root/superAdmin.
        $rolesPermitidos = $actor->hasRole('root')
            ? Role::pluck('id')
            : Role::whereNotIn('name', ['root', 'superAdmin'])->pluck('id');

        $rolesSolicitados = collect($request->roles ?? []);
        $rolesNoPermitidos = $rolesSolicitados->diff($rolesPermitidos);

        if ($rolesNoPermitidos->isNotEmpty()) {
            abort(403, 'No tiene permiso para asignar uno o más de los roles seleccionados.');
        }

        $user->name = $request->name;
        $user->lastname = $request->apellido;
        $user->email = $request->email;

        // La contraseña solo se cambia si se escribió una nueva.
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        $user->roles()->sync($rolesSolicitados);

        $aviso = $this->sincronizarRegistroDeRol($user, $rolesSolicitados);

        return redirect()->route('admin.users.index', $user)->with(array_merge(
            ['toast'=>2,'title' => 'Exito','info' => 'Se actualizo el usuario correctamente','icon' => 'success'],
            $aviso
        ));
    }

    public function toggleStatus($id) //DEACTIVATE
    {
        $user = User::findOrFail($id); $user->status = !$user->status;  $user->save();
        return redirect()->back()->with(['toast'=>2,'title' => 'Exito','info' => 'Estado del usuario actualizado.','icon' => 'success']);
    }
}
