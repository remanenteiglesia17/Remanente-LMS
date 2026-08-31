<?php

namespace App\Http\Controllers\Academico\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Tarea;
use App\Models\Entrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalificacionController extends Controller
{
    public function index(Request $request)
    {
        $profesor = Auth::user()->profesor;
        $cursos = $profesor->cursos()
            ->with(['modulos' => fn($q) => $q->withCount('tareas')])
            ->distinct()
            ->get();

        $cursoSeleccionado = null;
        $estudiantes = [];
        $tareasDelCurso = [];

        if ($request->filled('curso_id')) {
            // Verificar que el curso pertenezca al profesor
            $cursoSeleccionado = $cursos->firstWhere('id',$request->curso_id);

            if (!$cursoSeleccionado) {
                abort(403, 'No tienes acceso a este curso.');
            }


            $tareasDelCurso = Tarea::where('curso_id', $request->curso_id)
                ->with('modulo')
                ->orderBy('modulo_id', 'asc')
                ->orderBy('fecha_entrega', 'asc')
                ->get();

            $estudiantes = $cursoSeleccionado->estudiantes()
                ->with(['user', 'calificaciones' => function ($q) use ($request) {
                    $q->where('curso_id', $request->curso_id);
                }, 'entregas']) // <-- IMPORTANTE: Cargamos las entregas aquí
                ->get();
        }

        return view('profesor.calificaciones.index', compact('cursos', 'estudiantes', 'cursoSeleccionado', 'tareasDelCurso'));
    }

    /**
     * Store: Califica la tarea enviada.
     * Ya conocemos el tipo (tarea), nota máxima y concepto desde el modelo Tarea.
     */
    public function store(Request $request)
    {
        $request->validate([
            'entrega_id' => 'required|exists:entregas,id',
            'nota' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string'
        ]);

        $entrega = Entrega::with('tarea')->findOrFail($request->entrega_id);
        $tarea = $entrega->tarea;

        // Validar contra el puntaje configurado en la tarea
        if ($request->nota > $tarea->puntaje) {
            return back()->withErrors(['nota' => "La nota máxima permitida es {$tarea->puntaje}"]);
        }

        try {
            Calificacion::updateOrCreate(
                ['entrega_id' => $entrega->id],
                [
                    'estudiante_id'    => $entrega->estudiante_id,
                    'curso_id'         => $tarea->curso_id,
                    'tarea_id'         => $tarea->id,
                    'profesor_id'      => Auth::user()->profesor->id,
                    'concepto'         => $tarea->titulo_tarea,
                    'nota'             => $request->nota,
                    'nota_maxima'      => $tarea->puntaje,
                    'tipo_evaluacion'  => $tarea->tipo,
                    'fecha_calificacion' => now(),
                    'observaciones'    => $request->observaciones,
                    'publicada'        => true
                ]
            );
            return  back()->with(['swal' => 2, 'title' => 'Éxito', 'info' => 'Calificación guardada correctamente.', 'icon' => 'success']);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar: ' . $e->getMessage());
        }
    }
    /**
     * Guarda en bloque las notas capturadas en la Planilla (vista tipo hoja
     * de cálculo, un input por tarea y estudiante). A diferencia de store(),
     * aquí no hay una Entrega de por medio: el profesor puede calificar
     * directamente aunque el estudiante no haya subido nada.
     */
    public function guardarPlanilla(Request $request)
    {
        $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'notas' => 'required|array',
        ]);

        $profesor = Auth::user()->profesor;

        $curso = $profesor->cursos()->where('cursos.id', $request->curso_id)->first();
        abort_unless($curso, 403, 'No tienes acceso a este curso.');

        $tareas = Tarea::where('curso_id', $curso->id)->get()->keyBy('id');

        $guardadas = 0;
        $omitidas = 0;

        try {
            DB::beginTransaction();

            foreach ($request->notas as $estudianteId => $tareasNotas) {
                if (!is_array($tareasNotas)) {
                    continue;
                }

                foreach ($tareasNotas as $tareaId => $valor) {
                    if ($valor === null || $valor === '') {
                        continue;
                    }

                    $tarea = $tareas->get($tareaId);
                    if (!$tarea) {
                        $omitidas++;
                        continue;
                    }

                    if (!is_numeric($valor) || $valor < 0 || $valor > $tarea->puntaje) {
                        $omitidas++;
                        continue;
                    }

                    Calificacion::updateOrCreate(
                        [
                            'estudiante_id' => $estudianteId,
                            'curso_id'      => $curso->id,
                            'concepto'      => $tarea->titulo_tarea,
                            'periodo'       => $curso->periodo,
                        ],
                        [
                            'profesor_id'        => $profesor->id,
                            'tarea_id'           => $tarea->id,
                            'nota'               => $valor,
                            'nota_maxima'        => $tarea->puntaje,
                            'tipo_evaluacion'    => $tarea->tipo,
                            'fecha_calificacion' => now(),
                            'publicada'          => true,
                        ]
                    );

                    $guardadas++;
                }
            }

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'Notas guardadas correctamente.',
                'guardadas' => $guardadas,
                'omitidas'  => $omitidas,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function revision($id)
    {
        $entrega = Entrega::with(['estudiante.user', 'tarea', 'calificacion'])->findOrFail($id);
        // Debug opcional: Si esto lanza null, el problema es el dato en la DB
        if (!$entrega->estudiante) {
            return back()->with('error', 'Esta entrega no tiene un estudiante válido asociado.');
        }

        return view('profesor.calificaciones.revision', compact('entrega'));
    }
    /**
     * Muestra una calificación específica (opcional).
     */
    public function show(string $id)
    {
        $calificacion = Calificacion::with(['estudiante.user', 'entrega.tarea'])->findOrFail($id);
        return view('profesor.calificaciones.show', compact('calificacion'));
    }

    /**
     * Edición de una calificación manual desde el libro.
     */
    public function edit(string $id)
    {
        $calificacion = Calificacion::findOrFail($id);
        return view('profesor.calificaciones.edit', compact('calificacion'));
    }

    /**
     * Actualiza la calificación en la base de datos.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(['nota' => 'required|numeric|min:0']);

        $calificacion = Calificacion::findOrFail($id);
        $calificacion->update([
            'nota' => $request->nota,
            'observaciones' => $request->observaciones,
            'fecha_calificacion' => now()
        ]);

        return redirect()->route('profesor.calificaciones.index')->with('success', 'Nota actualizada.');
    }

    /**
     * Elimina una calificación.
     */
    public function destroy(string $id)
    {
        $calificacion = Calificacion::findOrFail($id);
        $calificacion->delete();

        return redirect()->route('profesor.calificaciones.index')->with('success', 'Calificación eliminada.');
    }

    /**
     * Método adicional para Guardado Masivo desde la tabla principal (Libro de Notas).
     */
    public function storeMasiva(Request $request)
    {
        $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'calificaciones' => 'required|array'
        ]);

        try {
            DB::beginTransaction();
            foreach ($request->calificaciones as $estudianteId => $notas) {
                foreach ($notas as $concepto => $valor) {
                    if ($valor !== null) {
                        Calificacion::updateOrCreate(
                            [
                                'estudiante_id' => $estudianteId,
                                'curso_id' => $request->curso_id,
                                'concepto' => $concepto,
                                'periodo' => '2026-1'
                            ],
                            [
                                'profesor_id' => Auth::user()->profesor->id,
                                'nota' => $valor,
                                'nota_maxima' => 5.0,
                                'tipo_evaluacion' => $this->detectarTipo($concepto),
                                'fecha_calificacion' => now(),
                                'publicada' => true
                            ]
                        );
                    }
                }
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Libro de calificaciones sincronizado.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function detectarTipo($concepto)
    {
        if (str_starts_with($concepto, 'T')) return 'tarea';
        if (str_starts_with($concepto, 'Q')) return 'quiz';
        if (str_starts_with($concepto, 'P')) return 'examen';
        return 'otro';
    }

    /**
     * Marcar todos los estudiantes de un curso como Aprobados (habilita certificado).
     */
    /**
     * Finalizar curso: evalúa a cada estudiante activo según su promedio
     * ponderado real y sus horas cumplidas, y marca su inscripción como
     * 'aprobado' o 'reprobado' en consecuencia (no aprueba en bloque).
     */
    public function finalizarCurso(Request $request)
    {
        $request->validate(['curso_id' => 'required|exists:cursos,id']);

        $profesor = Auth::user()->profesor;

        abort_unless(
            $profesor->cursos->contains($request->curso_id),
            403,
            'No tienes acceso a este curso.'
        );

        $curso = Curso::findOrFail($request->curso_id);

        $inscripciones = DB::table('estudiante_curso')
            ->where('curso_id', $curso->id)
            ->where('estado', 'activo')
            ->get();

        $aprobados = 0;
        $reprobados = 0;

        foreach ($inscripciones as $inscripcion) {
            $promedio = Calificacion::promedioPonderadoEstudianteCurso($inscripcion->estudiante_id, $curso->id);
            $aprobo = $promedio >= 3.0;

            DB::table('estudiante_curso')
                ->where('id', $inscripcion->id)
                ->update([
                    'estado' => $aprobo ? 'aprobado' : 'reprobado',
                    'updated_at' => now(),
                ]);

            $aprobo ? $aprobados++ : $reprobados++;
        }

        return back()->with([
            'swal' => 2,
            'title' => 'Curso finalizado',
            'info'  => "Se evaluaron {$inscripciones->count()} estudiantes: {$aprobados} aprobados, {$reprobados} reprobados (según su promedio ponderado).",
            'icon'  => 'success',
        ]);
    }
}
