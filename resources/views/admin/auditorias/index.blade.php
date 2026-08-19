@extends('adminlte::page')

@section('title', 'Auditoría')

@section('content_header')
    <h1>Bitácora de auditoría</h1>
@stop

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.auditorias.index') }}" class="row g-2">
            <div class="col-md-3 mb-2">
                <label class="small mb-1">Usuario</label>
                <input type="text" name="usuario" class="form-control" placeholder="Nombre del usuario"
                       value="{{ request('usuario') }}">
            </div>
            <div class="col-md-3 mb-2">
                <label class="small mb-1">Evento</label>
                <select name="event" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($eventos as $evento)
                        <option value="{{ $evento }}" @selected(request('event') === $evento)>
                            {{ \App\Models\Auditoria::eventoLabelPara($evento) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <label class="small mb-1">Modelo afectado</label>
                <select name="modelo" class="form-control">
                    <option value="">Todos</option>
                    @foreach ($modelos as $modelo)
                        <option value="{{ $modelo }}" @selected(request('modelo') === $modelo)>
                            {{ class_basename($modelo) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2"></div>

            <div class="col-md-3 mb-2">
                <label class="small mb-1">Desde</label>
                <input type="date" name="desde" class="form-control" value="{{ request('desde') }}">
            </div>
            <div class="col-md-3 mb-2">
                <label class="small mb-1">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="{{ request('hasta') }}">
            </div>
            <div class="col-md-6 mb-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-filter"></i> Filtrar</button>
                <a href="{{ route('admin.auditorias.index') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-outline card-secondary">
    <div class="card-header">
        <h3 class="card-title">Registros ({{ $auditorias->total() }})</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-bordered table-hover table-sm mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Evento</th>
                    <th>Modelo</th>
                    <th>Registro afectado</th>
                    <th>IP</th>
                    <th class="text-center">Detalle</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($auditorias as $auditoria)
                    <tr>
                        <td>{{ $auditoria->created_at?->format('d/m/Y H:i:s') }}</td>
                        <td>
                            {{ $auditoria->user_name ?? 'Sistema' }}
                            @if ($auditoria->user_role)
                                <span class="badge badge-light">{{ $auditoria->user_role }}</span>
                            @endif
                        </td>
                        <td><span class="badge badge-{{ $auditoria->evento_color }}">{{ $auditoria->evento_label }}</span></td>
                        <td>{{ $auditoria->auditable_model_name }}</td>
                        <td>{{ $auditoria->auditable_label ?? '—' }}</td>
                        <td>{{ $auditoria->ip_address }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.auditorias.show', $auditoria) }}" class="btn btn-info btn-sm" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay registros de auditoría con estos filtros.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $auditorias->onEachSide(1)->links() }}
    </div>
</div>
@stop
