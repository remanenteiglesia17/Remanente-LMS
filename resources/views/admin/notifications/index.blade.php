@extends('adminlte::page')

@section('title', 'Mis Notificaciones')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-bell mr-2"></i>Mis Notificaciones</h1>
        <form method="POST" action="{{ route('notificaciones.read-all') }}">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-check-double mr-1"></i>Marcar todas como leídas
            </button>
        </form>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body p-0">
            @forelse($notifications as $notif)
            @php $data = $notif->data; @endphp
            <div class="d-flex align-items-start p-3 border-bottom {{ is_null($notif->read_at) ? 'bg-light' : '' }}">
                <div class="mr-3 mt-1">
                    @if(is_null($notif->read_at))
                        <span class="badge badge-primary" style="width:10px;height:10px;border-radius:50%;display:inline-block;padding:0"></span>
                    @else
                        <span class="badge badge-secondary" style="width:10px;height:10px;border-radius:50%;display:inline-block;padding:0"></span>
                    @endif
                </div>
                <div class="flex-grow-1">
                    <div class="font-weight-{{ is_null($notif->read_at) ? 'bold' : 'normal' }}">
                        <i class="fas fa-clipboard-list text-primary mr-1"></i>
                        {{ $data['titulo'] ?? 'Notificación' }}
                    </div>
                    <small class="text-muted">
                        {{ $data['curso'] ?? '' }}
                        @if(!empty($data['fecha_entrega']))
                            · Entrega: {{ $data['fecha_entrega'] }}
                        @endif
                        · {{ $notif->created_at->diffForHumans() }}
                    </small>
                </div>
                <div class="ml-3 d-flex gap-2" style="gap:8px">
                    @if(!empty($data['url']))
                    <a href="{{ $data['url'] }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i>
                    </a>
                    @endif
                    @if(is_null($notif->read_at))
                    <a href="{{ route('admin.notifications.read', $notif->id) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-check"></i>
                    </a>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="fas fa-bell-slash fa-2x mb-2 d-block"></i>
                No tienes notificaciones.
            </div>
            @endforelse
        </div>
        @if($notifications->hasPages())
        <div class="card-footer">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</div>
@stop
