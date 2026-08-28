<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    /** GET /notificaciones/unread — polling */
    public function unread()
    {
        // 1. Liberar el archivo de sesión para que el polling de JS no bloquee peticiones concurrentes
        session_write_close();

        $user = Auth::user();
        
        $unread = $user->unreadNotifications()->latest()->take(10)->get();
        
        // 2. Usar count() directo sobre la relación
        $readCount = $user->readNotifications()->count();

        $items = $unread->map(fn($n) => [
            'id'      => $n->id,
            'data'    => $n->data,
            'created' => $n->created_at->diffForHumans(),
        ]);

        return response()->json([
            'count'      => $unread->count(),
            'read_count' => $readCount,
            'items'      => $items,
        ]);
    }

    /** GET /notificaciones/{id}/detail — datos completos para el modal */
    public function detail(string $id)
    {
        session_write_close(); // Opcional pero recomendado en lecturas JSON

        $notif = Auth::user()->notifications()->where('id', $id)->firstOrFail();
        $data  = $notif->data;

        $extra = [];
        if (!empty($data['tarea_id'])) {
            $tarea = Tarea::with('documentos')->find($data['tarea_id']);
            if ($tarea) {
                $extra['descripcion'] = $tarea->descripcion ?? null;
                $extra['tipo']        = $tarea->tipo_tarea   ?? null;
                $extra['puntaje']     = $tarea->puntaje      ?? null;
                $extra['documentos']  = $tarea->documentos
                    ? $tarea->documentos->map(fn($d) => [
                        'nombre' => $d->nombre_original ?? $d->nombre ?? basename($d->ruta),
                        'url'    => asset('storage/' . $d->ruta),
                      ])->toArray()
                    : [];
            }
        }

        return response()->json(array_merge($data, $extra));
    }

    /** POST /notificaciones/{id}/read */
    public function markRead(string $id)
    {
        Auth::user()->unreadNotifications()->where('id', $id)->first()?->markAsRead();
        return response()->json(['ok' => true]);
    }

    /** POST /notificaciones/read-all */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    }
}