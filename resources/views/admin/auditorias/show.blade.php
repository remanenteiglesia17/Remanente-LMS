@extends('adminlte::page')

@section('title', 'Detalle de auditoría')

@section('content_header')
    <h1>Detalle del registro de auditoría</h1>
@stop

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Información general</h3>
        <div class="card-tools">
            <a href="{{ route('admin.auditorias.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Fecha</dt>
            <dd class="col-sm-9">{{ $auditoria->created_at?->format('d/m/Y H:i:s') }}</dd>

            <dt class="col-sm-3">Usuario</dt>
            <dd class="col-sm-9">
                {{ $auditoria->user_name ?? 'Sistema' }}
                @if ($auditoria->user_role)
                    <span class="badge badge-light">{{ $auditoria->user_role }}</span>
                @endif
            </dd>

            <dt class="col-sm-3">Evento</dt>
            <dd class="col-sm-9">
                <span class="badge badge-{{ $auditoria->evento_color }}">{{ $auditoria->evento_label }}</span>
            </dd>

            <dt class="col-sm-3">Modelo</dt>
            <dd class="col-sm-9">{{ $auditoria->auditable_model_name }} @if($auditoria->auditable_id) (#{{ $auditoria->auditable_id }}) @endif</dd>

            <dt class="col-sm-3">Registro afectado</dt>
            <dd class="col-sm-9">{{ $auditoria->auditable_label ?? '—' }}</dd>

            <dt class="col-sm-3">URL</dt>
            <dd class="col-sm-9 text-break">{{ $auditoria->url ?? '—' }}</dd>

            <dt class="col-sm-3">Dirección IP</dt>
            <dd class="col-sm-9">{{ $auditoria->ip_address ?? '—' }}</dd>

            <dt class="col-sm-3">Navegador / Agente</dt>
            <dd class="col-sm-9 text-break">{{ $auditoria->user_agent ?? '—' }}</dd>
        </dl>
    </div>
</div>

@if ($auditoria->event === 'updated' && !empty($auditoria->campos_modificados))
    <div class="card card-outline card-warning">
        <div class="card-header">
            <h3 class="card-title">Campos modificados</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-bordered table-sm mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>Campo</th>
                        <th>Valor anterior</th>
                        <th>Valor nuevo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($auditoria->campos_modificados as $campo => $valores)
                        <tr>
                            <td>{{ $campo }}</td>
                            <td class="text-danger">{{ $valores['anterior'] ?? '—' }}</td>
                            <td class="text-success">{{ $valores['nuevo'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@elseif ($auditoria->event === 'created' && $auditoria->new_values)
    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">Datos creados</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-bordered table-sm mb-0">
                <tbody>
                    @foreach ($auditoria->new_values as $campo => $valor)
                        <tr>
                            <td class="font-weight-bold" style="width: 30%">{{ $campo }}</td>
                            <td>{{ is_scalar($valor) ? $valor : json_encode($valor) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@elseif ($auditoria->event === 'deleted' && $auditoria->old_values)
    <div class="card card-outline card-danger">
        <div class="card-header">
            <h3 class="card-title">Datos eliminados</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-bordered table-sm mb-0">
                <tbody>
                    @foreach ($auditoria->old_values as $campo => $valor)
                        <tr>
                            <td class="font-weight-bold" style="width: 30%">{{ $campo }}</td>
                            <td>{{ is_scalar($valor) ? $valor : json_encode($valor) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@stop
