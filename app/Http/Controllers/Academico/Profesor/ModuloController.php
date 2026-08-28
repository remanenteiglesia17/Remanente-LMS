<?php

namespace App\Http\Controllers\Academico\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuloController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:admin.profesor.modulos.index')->only('index');
        $this->middleware('permission:admin.profesor.modulos.create')->only('store');
        $this->middleware('permission:admin.profesor.modulos.edit')->only('toggleFinalizado');
        $this->middleware('permission:admin.profesor.modulos.destroy')->only('destroy');
    }

    /**
     * Listar los módulos de todos los cursos del profesor.
     */
    public function index()
    {
        $profesor = Auth::user()->profesor;

        $cursos = $profesor->cursos()
            ->with(['modulos' => fn ($q) => $q->withCount('tareas')])
            ->distinct()
            ->get();

        return view('profesor.modulos.index', compact('cursos'));
    }

    /**
     * Crear un módulo nuevo para uno de los cursos del profesor.
     * Queda al final del orden (siguiente número disponible).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:1000',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'peso_tarea' => 'required|numeric|min:0|max:100',
            'peso_quiz' => 'required|numeric|min:0|max:100',
            'peso_examen' => 'required|numeric|min:0|max:100',
            'peso_proyecto' => 'required|numeric|min:0|max:100',
            'peso_foro' => 'required|numeric|min:0|max:100',
        ]);

        $sumaPesos = $data['peso_tarea'] + $data['peso_quiz'] + $data['peso_examen']
            + $data['peso_proyecto'] + $data['peso_foro'];

        if (abs($sumaPesos - 100) > 0.01) {
            return back()->withInput()->withErrors([
                'peso_categoria' => "La ponderación por categoría del módulo debe sumar 100%. Suma actual: {$sumaPesos}%.",
            ]);
        }

        $profesor = Auth::user()->profesor;
        abort_unless($profesor->cursos->contains($data['curso_id']), 403, 'Ese curso no es tuyo.');

        $siguienteOrden = Modulo::where('curso_id', $data['curso_id'])->max('orden') + 1;

        Modulo::create([
            'curso_id' => $data['curso_id'],
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'orden' => $siguienteOrden,
            'fecha_inicio' => $data['fecha_inicio'],
            'fecha_fin' => $data['fecha_fin'],
            'peso_tarea' => $data['peso_tarea'],
            'peso_quiz' => $data['peso_quiz'],
            'peso_examen' => $data['peso_examen'],
            'peso_proyecto' => $data['peso_proyecto'],
            'peso_foro' => $data['peso_foro'],
        ]);

        return back()->with(['info' => 'Módulo creado correctamente.', 'icon' => 'success']);
    }

    /**
     * Actualizar los datos de un módulo existente (fechas y ponderación
     * por categoría). El nombre/descripción también se pueden ajustar
     * aquí.
     */
    public function update(Request $request, Modulo $modulo)
    {
        $profesor = Auth::user()->profesor;
        abort_unless($profesor->cursos->contains($modulo->curso_id), 403, 'Ese curso no es tuyo.');

        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string|max:1000',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'peso_tarea' => 'required|numeric|min:0|max:100',
            'peso_quiz' => 'required|numeric|min:0|max:100',
            'peso_examen' => 'required|numeric|min:0|max:100',
            'peso_proyecto' => 'required|numeric|min:0|max:100',
            'peso_foro' => 'required|numeric|min:0|max:100',
        ]);

        $sumaPesos = $data['peso_tarea'] + $data['peso_quiz'] + $data['peso_examen']
            + $data['peso_proyecto'] + $data['peso_foro'];

        if (abs($sumaPesos - 100) > 0.01) {
            return back()->withInput()->withErrors([
                'peso_categoria' => "La ponderación por categoría del módulo debe sumar 100%. Suma actual: {$sumaPesos}%.",
            ]);
        }

        $modulo->update($data);

        return back()->with(['info' => 'Módulo actualizado correctamente.', 'icon' => 'success']);
    }

    /**
     * Marcar un módulo como finalizado (o reabrirlo). Al finalizarlo,
     * el siguiente módulo del curso queda disponible para todos los
     * estudiantes inscritos automáticamente.
     */
    public function toggleFinalizado(Modulo $modulo)
    {
        $profesor = Auth::user()->profesor;
        abort_unless($profesor->cursos->contains($modulo->curso_id), 403, 'Ese curso no es tuyo.');

        $modulo->finalizado = !$modulo->finalizado;
        $modulo->finalizado_at = $modulo->finalizado ? now() : null;
        $modulo->save();

        return back()->with([
            'info' => $modulo->finalizado
                ? 'Módulo marcado como finalizado. El siguiente módulo ya quedó disponible para tus estudiantes.'
                : 'Módulo reabierto. El siguiente módulo vuelve a estar bloqueado para tus estudiantes.',
            'icon' => 'success',
        ]);
    }

    public function destroy(Modulo $modulo)
    {
        $profesor = Auth::user()->profesor;
        abort_unless($profesor->cursos->contains($modulo->curso_id), 403, 'Ese curso no es tuyo.');

        $modulo->delete();

        return back()->with(['info' => 'Módulo eliminado.', 'icon' => 'success']);
    }
}
