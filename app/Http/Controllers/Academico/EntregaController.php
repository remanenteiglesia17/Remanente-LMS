<?php

namespace App\Http\Controllers\Academico;

use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\Entrega;
use App\Models\Tarea;
use Illuminate\Http\Request;

class EntregaController extends Controller
{
    public function index(Tarea $tarea)
    {
        // Ver todas las entregas de una tarea
        $entregas = $tarea->entregas()->with(['estudiante.user', 'calificacion', 'archivos'])->get();

        return view('profesor.entregas.index', compact('tarea', 'entregas'));
    }

    public function store(Request $request)
    {
        $tarea = Tarea::findOrFail($request->tarea_id);

        $request->validate([
            'archivo' => 'required|file|mimes:docx,pdf,jpg,jpeg,png|max:51200',
            'comentario' => 'nullable|string',
        ]);

        $estudianteId = auth()->user()->estudiante->id;

        // Evitar doble entrega (respaldo a la restricción unique)
        if ($tarea->entregas()->where('estudiante_id', $estudianteId)->exists()) {
            return back()->withErrors([
                'archivo' => 'Ya enviaste esta tarea. Si necesitas cambiar el archivo, edita tu entrega.',
            ]);
        }

        if ($tarea->fecha_entrega && now()->gt($tarea->fecha_entrega) && !$tarea->permite_entregas_tardias) {
            return back()->withErrors([
                'archivo' => 'La fecha límite de esta tarea ya pasó y no admite entregas tardías.',
            ]);
        }

        // Guardar archivo
        $rutaArchivo = $request->file('archivo')
            ->store('entregas/tarea_' . $tarea->id, 'public');

        Entrega::create([
            'tarea_id'       => $tarea->id,
            'estudiante_id'  => $estudianteId,
            'comentario'     => $request->comentario,
            'archivo'        => $rutaArchivo,
            'fecha_entrega'  => now(),
            'entrega_tardia' => $tarea->fecha_entrega ? now()->gt($tarea->fecha_entrega) : false,
            'estado'         => ($tarea->fecha_entrega && now()->gt($tarea->fecha_entrega)) ? 'tardia' : 'pendiente',
        ]);

        return back()->with('success', 'Entrega enviada correctamente');
    }

    /**
     * El estudiante reemplaza el archivo/comentario de su propia entrega,
     * siempre que aún no haya sido calificada y (si la tarea no admite
     * entregas tardías) que la fecha límite no haya pasado.
     */
    public function update(Request $request, Entrega $entrega)
    {
        abort_unless($entrega->estudiante_id === auth()->user()->estudiante->id, 403);

        $tarea = $entrega->tarea;

        if ($entrega->calificacion) {
            return back()->withErrors(['archivo' => 'Esta entrega ya fue calificada y no se puede modificar.']);
        }

        if ($tarea->fecha_entrega && now()->gt($tarea->fecha_entrega) && !$tarea->permite_entregas_tardias) {
            return back()->withErrors(['archivo' => 'La fecha límite ya pasó, no puedes editar tu entrega.']);
        }

        $request->validate([
            'archivo' => 'nullable|file|mimes:docx,pdf,jpg,jpeg,png|max:51200',
            'comentario' => 'nullable|string',
        ]);

        $data = [
            'comentario' => $request->comentario,
            'fecha_entrega' => now(),
            'entrega_tardia' => $tarea->fecha_entrega ? now()->gt($tarea->fecha_entrega) : false,
            'estado' => ($tarea->fecha_entrega && now()->gt($tarea->fecha_entrega)) ? 'tardia' : 'pendiente',
        ];

        if ($request->hasFile('archivo')) {
            if ($entrega->archivo) {
                Storage::disk('public')->delete($entrega->archivo);
            }
            $data['archivo'] = $request->file('archivo')->store('entregas/tarea_' . $tarea->id, 'public');
        }

        $entrega->update($data);

        return back()->with('success', 'Entrega actualizada correctamente');
    }


    public function calificar(Request $request, Entrega $entrega)
    {
        $request->validate([
            'calificacion' => 'required|numeric|min:0',
            'comentario_profesor' => 'nullable|string',
        ]);

        // Validación lógica: no pasar del puntaje de la tarea
        if ($request->calificacion > $entrega->tarea->puntaje) {
            return back()->withErrors([
                'calificacion' => 'La calificación supera el puntaje máximo',
            ]);
        }

        $entrega->update([
            'calificacion' => $request->calificacion,
            'comentario_profesor' => $request->comentario_profesor,
        ]);

        return back()->with('success', 'Entrega calificada');
    }
}
