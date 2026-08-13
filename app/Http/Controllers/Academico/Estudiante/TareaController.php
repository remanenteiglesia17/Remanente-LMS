<?php

namespace App\Http\Controllers\Academico\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Modulo;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TareaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:estudiante.cursos.index')->only('index');
        $this->middleware('permission:estudiante.cursos.show')->only('show');
    }

    /**
     * Listar tareas de un curso.
     */
    public function index(Request $request)
    {
        $estudiante = Auth::user()->estudiante;
        $curso = $estudiante->cursos()->first();      // Obtener el curso del estudiante (1 o ninguno)

        abort_unless($curso, 404, 'No estás inscrito en ningún curso.');

        $modulo = null;
        $tareasQuery = $curso->tareas();

        if ($request->filled('modulo_id')) {
            $modulo = Modulo::where('curso_id', $curso->id)->findOrFail($request->modulo_id);
            abort_unless($modulo->estaDesbloqueado(), 403, 'Este módulo todavía no está disponible. Tu profesor debe finalizar el módulo anterior primero.');
            $tareasQuery->where('modulo_id', $modulo->id);
        }

        $tareas = $tareasQuery
            ->with(['entregas' => fn($q) => $q->where('estudiante_id', $estudiante->id),])
            ->orderBy('fecha_entrega')
            ->get()
            ->map(function ($tarea) {
                // Calcular el estado de la tarea
                $entrega = $tarea->entregas->first();

                if ($entrega) {
                    $tarea->estado = 'entregado';
                    $tarea->badge_class = 'badge-success';
                    $tarea->progreso = 100;
                } else {
                    $diasRestantes = now()->diffInDays($tarea->fecha_entrega, false);

                    if ($diasRestantes < 0) {
                        $tarea->estado = 'atrasado';
                        $tarea->badge_class = 'badge-danger';
                    } else {
                        $tarea->estado = 'pendiente';
                        $tarea->badge_class = 'badge-warning';
                    }
                    $tarea->progreso = 0;
                    $tarea->dias_restantes = ceil($diasRestantes);
                }

                return $tarea;
            });
        // dump($tareas->toArray());
        return view('estudiante.tareas.index', compact('curso', 'tareas', 'modulo'));
    }

    /**
     * Ver detalle de una tarea.
     */
    public function show(Tarea $tarea)
    {
        $estudiante = Auth::user()->estudiante;

        abort_unless($estudiante->cursos->contains($tarea->curso_id),403,'No tienes acceso a esta tarea.');

        $tarea->load([
            'curso',
            'documentos',
            'entregas' => fn($q) => $q->where('estudiante_id', $estudiante->id)
                 ->orderBy('created_at', 'desc'),
        ]); 
        // Calcular estado y días restantes
        $entrega = $tarea->entregas->first();

        if ($entrega) {
            $tarea->estado = 'entregado';
            $tarea->badge_class = 'badge-success';
        } else {
            $diasRestantes = now()->diffInDays($tarea->fecha_entrega, false);

            if ($diasRestantes < 0) {
                $tarea->estado = 'atrasado';
                $tarea->badge_class = 'badge-danger';
            } else {
                $tarea->estado = 'pendiente';
                $tarea->badge_class = 'badge-warning';
            }
            $tarea->dias_restantes = ceil($diasRestantes);
        }

        return view('estudiante.tareas.show', compact('tarea'));
    }
}
