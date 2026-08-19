<?php

namespace App\Http\Controllers\Academico\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Parcial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParcialController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:admin.profesor.parciales.index')->only('index');
        $this->middleware('permission:admin.profesor.parciales.create')->only('store');
        $this->middleware('permission:admin.profesor.parciales.edit')->only('update');
        $this->middleware('permission:admin.profesor.parciales.destroy')->only('destroy');
    }

    /**
     * Listar los parciales de todos los cursos del profesor, con la nota
     * que lleva cada estudiante en cada parcial (n tareas/quices promediados).
     */
    public function index(Request $request)
    {
        $profesor = Auth::user()->profesor;

        $cursos = $profesor->cursos()
            ->with(['parciales.tareas', 'estudiantes.user'])
            ->distinct()
            ->get();

        $cursoSeleccionado = null;
        $notasFinales = [];

        if ($request->filled('curso_id')) {
            $cursoSeleccionado = $cursos->firstWhere('id', $request->curso_id);
            abort_unless($cursoSeleccionado, 403, 'No tienes acceso a este curso.');

            foreach ($cursoSeleccionado->estudiantes as $estudiante) {
                $notasFinales[$estudiante->id] = \App\Models\Calificacion::notaFinalEstudianteCurso(
                    $estudiante->id,
                    $cursoSeleccionado->id
                );
            }
        }

        return view('profesor.parciales.index', compact('cursos', 'cursoSeleccionado', 'notasFinales'));
    }

    /**
     * Crear un parcial nuevo para uno de los cursos del profesor.
     * Queda al final del orden (siguiente número disponible) si no se indica.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'nombre' => 'required|string|max:150',
            'numero' => 'nullable|integer|min:1',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'porcentaje' => 'nullable|integer|min:1|max:100',
        ]);

        $profesor = Auth::user()->profesor;
        abort_unless($profesor->cursos->contains($data['curso_id']), 403, 'Ese curso no es tuyo.');

        $data['numero'] = $data['numero'] ?? (Parcial::where('curso_id', $data['curso_id'])->max('numero') + 1);

        Parcial::create($data);

        return back()->with(['swal' => 1, 'info' => 'Parcial creado correctamente.', 'icon' => 'success']);
    }

    public function update(Request $request, Parcial $parcial)
    {
        $profesor = Auth::user()->profesor;
        abort_unless($profesor->cursos->contains($parcial->curso_id), 403, 'Ese curso no es tuyo.');

        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'numero' => 'required|integer|min:1',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'porcentaje' => 'nullable|integer|min:1|max:100',
        ]);

        $parcial->update($data);

        return back()->with(['swal' => 1, 'info' => 'Parcial actualizado.', 'icon' => 'success']);
    }

    public function destroy(Parcial $parcial)
    {
        $profesor = Auth::user()->profesor;
        abort_unless($profesor->cursos->contains($parcial->curso_id), 403, 'Ese curso no es tuyo.');

        $parcial->delete();

        return back()->with(['swal' => 1, 'info' => 'Parcial eliminado. Sus tareas quedaron sin parcial asignado.', 'icon' => 'success']);
    }
}
