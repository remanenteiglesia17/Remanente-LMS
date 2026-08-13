<?php

namespace App\Http\Controllers\Academico\Estudiante;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ModuloController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:estudiante.cursos.index')->only('index');
    }

    public function index()
    {
        $estudiante = Auth::user()->estudiante;
        $curso = $estudiante->cursos()->first();

        abort_unless($curso, 404, 'No estás inscrito en ningún curso.');

        $modulos = $curso->modulos()->withCount('tareas')->get()
            ->map(function ($modulo) {
                $modulo->desbloqueado = $modulo->estaDesbloqueado();

                return $modulo;
            });

        return view('estudiante.modulos.index', compact('curso', 'modulos'));
    }
}
