<?php

namespace App\Http\Controllers\Academico\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\CalendarioEvento;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarioController extends Controller
{
    /**
     * Vista del calendario semanal del estudiante (contenedor de FullCalendar).
     */
    public function index()
    {
        $estudiante = Auth::user()->estudiante;

        abort_unless($estudiante, 403, 'Tu usuario no tiene un perfil de estudiante asociado.');

        $cursos = $estudiante->cursos;

        abort_if($cursos->isEmpty(), 404, 'No estás inscrito en ningún curso.');

        return view('estudiante.calendario.index', compact('cursos'));
    }

    /**
     * Devuelve, en formato JSON compatible con FullCalendar, los eventos de la
     * semana (o rango) solicitada: las clases programadas del estudiante y los
     * eventos académicos (exámenes, entregas, parciales, festivos) de sus cursos.
     */
    public function eventos(Request $request)
    {
        $estudiante = Auth::user()->estudiante;

        if (! $estudiante) {
            return response()->json([]);
        }

        // FullCalendar envía automáticamente el rango visible (start/end) al navegar entre semanas.
        $desde = $request->filled('start') ? Carbon::parse($request->query('start')) : now()->startOfWeek();
        $hasta = $request->filled('end') ? Carbon::parse($request->query('end')) : now()->endOfWeek();

        $cursoIds = $estudiante->cursos->pluck('id');

        // 1) Clases reales programadas para el estudiante (fecha y hora concretas)
        $clases = $estudiante->clases()
            ->with(['profesor', 'curso'])
            ->whereBetween('fecha_hora_inicio', [$desde, $hasta])
            ->get()
            ->map(function ($clase) {
                return [
                    'id' => 'clase-'.$clase->id,
                    'title' => $clase->titulo ?: 'Clase',
                    'start' => optional($clase->fecha_hora_inicio)->toIso8601String(),
                    'end' => optional($clase->fecha_hora_fin)->toIso8601String(),
                    'color' => $clase->color ?: '#3490dc',
                    'extendedProps' => [
                        'tipo' => 'Clase',
                        'curso' => $clase->curso->nombre ?? null,
                        'profesor' => $clase->profesor
                            ? trim($clase->profesor->nombres.' '.$clase->profesor->apellidos)
                            : null,
                        'lugar' => $clase->modalidad === 'virtual'
                            ? ($clase->link_virtual ?: 'Virtual')
                            : ($clase->aula ?: 'Presencial'),
                    ],
                ];
            });

        // 2) Eventos académicos del curso: exámenes, entregas, parciales, festivos, otros
        $eventosAcademicos = CalendarioEvento::whereIn('curso_id', $cursoIds)
            ->whereBetween('fecha', [$desde->toDateString(), $hasta->toDateString()])
            ->get()
            ->map(function ($evento) {
                $esTodoElDia = is_null($evento->hora_inicio);
                $fecha = $evento->fecha->toDateString();

                return [
                    'id' => 'evento-'.$evento->id,
                    'title' => $evento->titulo,
                    'start' => $esTodoElDia ? $fecha : $fecha.'T'.substr($evento->hora_inicio, 0, 5),
                    'end' => $evento->hora_fin ? $fecha.'T'.substr($evento->hora_fin, 0, 5) : null,
                    'allDay' => $esTodoElDia,
                    'color' => $evento->color ?: '#dc3545',
                    'extendedProps' => [
                        'tipo' => ucfirst($evento->tipo),
                        'descripcion' => $evento->descripcion,
                    ],
                ];
            });

        return response()->json($clases->concat($eventosAcademicos)->values());
    }
}
