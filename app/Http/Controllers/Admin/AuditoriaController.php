<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:admin.auditorias.index')->only(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = Auditoria::with('user')->latest('id');

        if ($request->filled('usuario')) {
            $busqueda = $request->usuario;
            $query->where(function ($q) use ($busqueda) {
                $q->where('user_name', 'like', "%{$busqueda}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$busqueda}%"));
            });
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('modelo')) {
            $query->where('auditable_type', $request->modelo);
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $auditorias = $query->paginate(25)->withQueryString();

        $eventos = Auditoria::select('event')->distinct()->orderBy('event')->pluck('event');
        $modelos = Auditoria::whereNotNull('auditable_type')
            ->select('auditable_type')->distinct()->orderBy('auditable_type')->pluck('auditable_type');

        return view('admin.auditorias.index', compact('auditorias', 'eventos', 'modelos'));
    }

    public function show(Auditoria $auditoria)
    {
        return view('admin.auditorias.show', compact('auditoria'));
    }
}
