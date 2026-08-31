<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class EstudianteController extends Controller
{
    public function __construct()
    {  // Solo los que tengan el permiso pueden acceder a estas acciones
        $this->middleware('can:admin.estudiantes.index')->only('index');
        $this->middleware('can:admin.estudiantes.create')->only('create', 'store');
        $this->middleware('can:admin.estudiantes.edit')->only('edit', 'update');
        $this->middleware('can:admin.estudiantes.destroy')->only('destroy');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // nombres/apellidos ya no viven en 'estudiantes' (se movieron a
            // 'users' para no duplicar el dato). Se hace JOIN y se alían las
            // columnas para que Yajra DataTables pueda seguir ordenando y
            // buscando por 'nombres'/'apellidos' como columnas SQL reales.
            $query = Estudiante::query()
                ->with('user')
                ->join('users', 'users.id', '=', 'estudiantes.user_id')
                ->select('estudiantes.*', 'users.name as nombres', 'users.lastname as apellidos');

            return DataTables::eloquent($query)->addColumn('action', function ($estudiante) { // Puedes devolver botones (renderizar un partial)
                return view('admin.estudiantes.partials.actions', compact('estudiante'))->render();
            })->toJson();
        }

        $cursos = Curso::all(); // si los necesitas en la vista

        return view('admin.estudiantes.index', compact('cursos'));
    }

    // public function create(){// $cursos = Curso::all();// return view('admin.estudiantes.create', compact('cursos'));}
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombres' => 'required',
            'apellidos' => 'required',
            'cc' => 'required|max:11|unique:estudiantes,cc',
            'genero' => 'required',
            'telefono' => 'required|max:11',
            'correo' => 'required|email|max:250|unique:users,email',
            'direccion' => 'required',
            'contacto_emergencia' => 'required|max:11',
        ]);
        try {
            DB::beginTransaction();  // ⬅️ Comienza la transacción

            $usuario = User::create(['name' => $request->nombres, 'lastname' => $request->apellidos, 'email' => $request->correo, 'password' => Hash::make($request->password ?? $request->cc)]);
            $usuario->assignRole('estudiante');

            // nombres/apellidos ya viven en 'users' (arriba); no se duplican
            // en 'estudiantes', así que se quitan antes de crear el registro.
            unset($validatedData['nombres'], $validatedData['apellidos'], $validatedData['correo']);
            $validatedData['user_id'] = $usuario->id;                            // Asociar el user_id al estudiante
            $validatedData['observaciones'] = $request->observaciones;

            $estudiante = Estudiante::create($validatedData);                          // Crear estudiante
            $usuarioId = $usuario->id;

            if ($request->has('cursos') && is_array($request->cursos)) {          // Asignar cursos si existen
                foreach ($request->cursos as $cursoId) {
                    $estudiante->cursos()->attach($cursoId);
                }
            }
            if (!isset($estudiante)) { // $clase->delete();
                DB::rollBack();                                                   // Revertir todo si algo falla
                DB::table('users')->where('id', $usuarioId)->delete(); // Definir $ultimoId tomando el máximo ID de la tabla
            }
            DB::commit();  // ⬅️ Si todo salió bien, guarda en la base de datos

            return redirect()->route('admin.estudiantes.index')
                ->with(['title' => 'Éxito', 'info' => 'Se registró al Estudiante de forma correcta', 'icon' => 'success']);
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Error de base de datos al registrar estudiante: '.$e->getMessage());

            return back()->withErrors(['error' => 'Error en la base de datos.'])->withInput();
        } catch (\Exception $e) {
            DB::rollBack();  // ⬅️ Si falla, revierte todo
            \Log::error('Error inesperado al registrar estudiante: '.$e->getMessage());

            return back()->withErrors(['error' => 'Ocurrió un error inesperado.'])->withInput();
        }
    }

    public function show(Estudiante $estudiante)
    {
        $estudiante->load(['user', 'cursos', 'entregas.tarea', 'asistencias']);
        return view('admin.estudiantes.show', compact('estudiante'));
    }
    public function edit(Estudiante $estudiante)
    {
        $cursos = Curso::all();
        $estudiante->load('user'); // Cargar todos los cursos disponibles y usuario del estudiante
        $cursosSeleccionados = $estudiante->cursos->pluck('id')->toArray(); // Obtener los cursos ya asignados al estudiante

        // return view('admin.estudiantes.edit', compact('estudiante', 'cursos', 'cursosSeleccionados'));
        return response()->json(['estudiante' => $estudiante, 'cursos' => $cursos, 'cursosSeleccionados' => $cursosSeleccionados]);
    }

    public function update(Request $request, Estudiante $estudiante)
    {   // \Log::info('estudiante',[$request->all()]);
        $validatedData = $request->validate([
            'nombres' => 'required',
            'apellidos' => 'required',
            'cc' => 'required|unique:estudiantes,cc,'.$estudiante->id,
            'genero' => 'required',
            'telefono' => 'required',
            'email' => 'required|email|max:250|unique:users,email,'.$estudiante->user_id, // validar en users
            'direccion' => 'required',
            'contacto_emergencia' => 'required',
        ]);

        // Actualizar datos del usuario (nombre, apellido, email)
        $usuario = User::findOrFail($estudiante->user_id);
        $usuario->name = $request->nombres;
        $usuario->lastname = $request->apellidos;
        $usuario->email = $request->email;

        if ($request->has('reset_password')) {
            $usuario->password = Hash::make($request->cc);
        }
        $usuario->save();

        // nombres/apellidos/email ya no existen como columnas en 'estudiantes'
        unset($validatedData['email'], $validatedData['nombres'], $validatedData['apellidos']);
        $estudiante->observaciones = $request->observaciones;

        $estudiante->update($validatedData);                 // Actualizar Estudiante

        $estudiante->cursos()->sync($request->cursos ?? []); // Sincronizar cursos

        return redirect()->route('admin.estudiantes.index')
            ->with(['toast' => 2, 'title' => 'Exito!', 'info' => 'Estudiante actualizado correctamente.', 'icon' => 'success']);
    }

    public function toggleStatus($id) // DEACTIVATE
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();

        return redirect()->back()->with(['toast' => 2, 'title' => 'Exito!', 'info' => 'Estado del usuario actualizado.', 'icon' => 'success']);
    }

    public function destroy(Estudiante $Estudiante)
    {
        if ($Estudiante->user) {$Estudiante->user->delete();} // Si existe un usuario asociado, eliminarlo
        $Estudiante->delete();// Eliminar el Estudiante
        return redirect()->route('admin.estudiantes.index')->with(['title'=> 'Exito', 'info'=>'El Estudiante se eliminó con éxito', 'icon', 'success']);
    }
    /**
     * Mostrar el curso actual del estudiante autenticado.
     */
    public function miCurso()
    {
        // Obtener el estudiante del usuario autenticado
        $estudiante = Estudiante::where('user_id', Auth::id())->first();

        if (!$estudiante) {
            return redirect()->route('dashboard')
                ->with('error', 'No se encontró información de estudiante para tu usuario.');
        }

        // Obtener el curso activo actual del estudiante
        // Asumiendo que tiene relación belongsToMany con pivot estudiante_curso
        $curso = $estudiante->cursos()
            ->where('cursos.estado', true) // Solo cursos activos
            ->where('estudiante_curso.estado', 'activo') // Solo inscripciones activas (si tienes este campo)
            ->with([
                'objetivos',
                'bibliografias',
                'documentos',
                'politicas',
                'tareas' => function ($query) {
                    $query->where('visible', true)
                          ->orderBy('fecha_entrega', 'asc');
                },
                'profesores',
            ])
            ->first(); // Solo el primero (curso actual)

        if (!$curso) {
            return view('estudiante.sin-curso');
        }

        return view('estudiante.mi-curso', compact('curso', 'estudiante'));
    }

    /**
     * Mostrar propósitos/objetivos del curso.
     */
    public function miCursoPropositos()
    {
        $estudiante = Estudiante::where('user_id', Auth::id())->first();

        if (!$estudiante) {
            return redirect()->route('dashboard')
                ->with('error', 'No se encontró información de estudiante.');
        }

        $curso = $estudiante->cursos()
            ->where('cursos.estado', true)
            ->with(['objetivos', 'politicas'])
            ->first();

        if (!$curso) {
            return view('estudiante.sin-curso');
        }

        return view('estudiante.curso.propositos', compact('curso', 'estudiante'));
    }

    /**
     * Mostrar contenido del curso.
     */
    public function miCursoContenido()
    {
        $estudiante = Estudiante::where('user_id', Auth::id())->first();

        if (!$estudiante) {
            return redirect()->route('dashboard')
                ->with('error', 'No se encontró información de estudiante.');
        }

        $curso = $estudiante->cursos()
            ->where('cursos.estado', true)
            ->with([
                'objetivos',
                'bibliografias',
                'documentos',
                'politicas',
                'tareas' => function ($query) {
                    $query->where('visible', true)
                          ->orderBy('fecha_entrega', 'asc');
                },
            ])
            ->first();

        if (!$curso) {
            return view('estudiante.sin-curso');
        }

        return view('estudiante.curso.contenido', compact('curso', 'estudiante'));
    }

    /**
     * Mostrar tareas del curso.
     */
    public function misTareas()
    {
        $estudiante = Estudiante::where('user_id', Auth::id())->first();

        if (!$estudiante) {
            return redirect()->route('dashboard')
                ->with('error', 'No se encontró información de estudiante.');
        }

        $curso = $estudiante->cursos()
            ->where('cursos.estado', true)
            ->first();

        if (!$curso) {
            return view('estudiante.sin-curso');
        }

        // Obtener tareas con sus entregas
        $tareas = $curso->tareas()
            ->where('visible', true)
            ->with(['entregas' => function ($query) use ($estudiante) {
                $query->where('estudiante_id', $estudiante->id);
            }])
            ->orderBy('fecha_entrega', 'asc')
            ->get();

        return view('estudiante.curso.tareas', compact('curso', 'estudiante', 'tareas'));
    }

/**
     * Ver mis calificaciones.
     */
    public function misCalificaciones()
    {
        $estudiante = Estudiante::where('user_id', Auth::id())
            ->with(['calificaciones', 'cursos'])
            ->first();

        if (!$estudiante) {
            return redirect()->route('dashboard')
                ->with('error', 'No se encontró información de estudiante.');
        }

        // Cargar todos los cursos activos del estudiante
        $cursos = $estudiante->cursos()
            ->where('cursos.estado', true)
            ->with(['tareas'])
            ->get();

        if ($cursos->isEmpty()) {
            return view('estudiante.sin-curso');
        }

        $promedios = [];

        foreach ($cursos as $curso) {
            // Tareas pertenecientes al curso
            $tareasDelCurso = $curso->tareas;
            $totalTareasCurso = $tareasDelCurso->count();
            $titulosTareas = $tareasDelCurso->pluck('titulo_tarea')->toArray();

            // Filtrar las calificaciones del estudiante que correspondan a las tareas de este curso
            $calificacionesCurso = $estudiante->calificaciones
                ->whereIn('concepto', $titulosTareas)
                ->whereNotNull('nota');

            // 1. Promedio Ponderado / Acumulada del Estudiante (Únicamente sobre tareas calificadas)
            $promedioEstudiante = $calificacionesCurso->avg('nota') ?? 0;

            // 2. Promedio General del Grupo (Opcional, promediando todos los estudiantes)
            $promedioCurso = DB::table('calificaciones')
                ->whereIn('concepto', $titulosTareas)
                ->whereNotNull('nota')
                ->avg('nota') ?? 0;

            // Totales de control
            $totalCalificadas = $calificacionesCurso->count();
            $porcentajeCompletado = $totalTareasCurso > 0 
                ? round(($totalCalificadas / $totalTareasCurso) * 100) 
                : 0;

            $aprobado = $promedioEstudiante >= 3.0;

            $promedios[$curso->id] = [
                'curso' => $curso,
                'promedio' => $promedioEstudiante,
                'promedio_curso' => $promedioCurso,
                'total_calificaciones' => $totalCalificadas,
                'total_tareas_curso' => $totalTareasCurso,
                'porcentaje_completado' => $porcentajeCompletado,
                'aprobado' => $aprobado,
                'puede_descargar' => $aprobado && ($porcentajeCompletado == 100),
                'razon_bloqueo' => !$aprobado 
                    ? 'Tu promedio actual es inferior a 3.0' 
                    : 'Aún tienes tareas pendientes por evaluar'
            ];
        }

        return view('estudiante.index', compact('promedios', 'estudiante'));
    }
}
