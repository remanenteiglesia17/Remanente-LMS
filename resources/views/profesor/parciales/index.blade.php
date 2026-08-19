@extends('adminlte::page')

@section('title', 'Parciales y Nota Final')

@section('content_header')
    <h1><i class="fas fa-calendar-check"></i> Parciales y Nota Final</h1>
@stop

@section('content')
<div class="container-fluid">

    @if (session('info'))
        <div class="alert alert-{{ session('icon') === 'success' ? 'success' : 'info' }}">
            {{ session('info') }}
        </div>
    @endif

    @if ($cursos->isEmpty())
        <div class="alert alert-info">No tienes cursos asignados todavía.</div>
    @endif

    {{-- Gestión de parciales por curso --}}
    @foreach ($cursos as $curso)
        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    {{ $curso->codigo }} - {{ $curso->nombre }}
                    @if ($curso->fecha_inicio || $curso->fecha_fin)
                        <small class="text-muted">
                            ({{ optional($curso->fecha_inicio)->format('d/m/Y') ?? '—' }}
                            al
                            {{ optional($curso->fecha_fin)->format('d/m/Y') ?? '—' }})
                        </small>
                    @else
                        <small class="text-warning">
                            <i class="fas fa-exclamation-triangle"></i> Este curso no tiene fecha de inicio/fin definida (pídele al admin que la configure).
                        </small>
                    @endif
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.profesor.parciales.index', ['curso_id' => $curso->id]) }}" class="btn btn-sm btn-info">
                        <i class="fas fa-chart-bar"></i> Ver nota final
                    </a>
                    <button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#createParcialModal-{{ $curso->id }}">
                        <i class="fas fa-plus-circle"></i> Nuevo parcial
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                @if ($curso->parciales->isEmpty())
                    <p class="text-muted p-3 mb-0">Aún no has creado parciales para este curso.</p>
                @else
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Fechas</th>
                                <th>Peso en nota final</th>
                                <th>Tareas / quices</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($curso->parciales as $parcial)
                                <tr>
                                    <td>{{ $parcial->numero }}</td>
                                    <td>{{ $parcial->nombre }}</td>
                                    <td>
                                        @if ($parcial->fecha_inicio || $parcial->fecha_fin)
                                            {{ optional($parcial->fecha_inicio)->format('d/m/Y') ?? '—' }}
                                            al
                                            {{ optional($parcial->fecha_fin)->format('d/m/Y') ?? '—' }}
                                        @else
                                            <span class="text-muted">Sin definir</span>
                                        @endif
                                    </td>
                                    <td>{{ $parcial->porcentaje ? $parcial->porcentaje . '%' : 'Igual que los demás' }}</td>
                                    <td>{{ $parcial->tareas->count() }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editParcialModal-{{ $parcial->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.profesor.parciales.destroy', $parcial->id) }}" method="POST" style="display:inline;"
                                            onsubmit="return confirm('¿Eliminar este parcial? Sus tareas quedarán sin parcial asignado.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>

                                {{-- Modal editar parcial --}}
                                <div class="modal fade" id="editParcialModal-{{ $parcial->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar parcial</h5>
                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <form action="{{ route('admin.profesor.parciales.update', $parcial->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Nombre</label><b class="text-danger">*</b>
                                                        <input type="text" name="nombre" class="form-control" value="{{ $parcial->nombre }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Número / orden</label><b class="text-danger">*</b>
                                                        <input type="number" name="numero" class="form-control" min="1" value="{{ $parcial->numero }}" required>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-6">
                                                            <label>Fecha inicio</label>
                                                            <input type="date" name="fecha_inicio" class="form-control" value="{{ optional($parcial->fecha_inicio)->format('Y-m-d') }}">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label>Fecha fin</label>
                                                            <input type="date" name="fecha_fin" class="form-control" value="{{ optional($parcial->fecha_fin)->format('Y-m-d') }}">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Peso en la nota final (%)</label>
                                                        <input type="number" name="porcentaje" class="form-control" min="1" max="100" value="{{ $parcial->porcentaje }}"
                                                            placeholder="Déjalo vacío para pesar igual que los demás parciales">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- Modal crear parcial --}}
        <div class="modal fade" id="createParcialModal-{{ $curso->id }}" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo parcial — {{ $curso->nombre }}</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form action="{{ route('admin.profesor.parciales.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="curso_id" value="{{ $curso->id }}">
                            <div class="form-group">
                                <label>Nombre</label><b class="text-danger">*</b>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej: Primer Parcial" required>
                            </div>
                            <div class="form-group">
                                <label>Número / orden</label>
                                <input type="number" name="numero" class="form-control" min="1" placeholder="Se asigna automáticamente si se deja vacío">
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Fecha inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Fecha fin</label>
                                    <input type="date" name="fecha_fin" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Peso en la nota final (%)</label>
                                <input type="number" name="porcentaje" class="form-control" min="1" max="100"
                                    placeholder="Déjalo vacío para pesar igual que los demás parciales">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Crear parcial</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Nota final por estudiante del curso seleccionado --}}
    @if ($cursoSeleccionado)
        <div class="card shadow">
            <div class="card-header bg-dark">
                <h3 class="card-title">Nota final — {{ $cursoSeleccionado->nombre }}</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped m-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Estudiante</th>
                                @foreach ($cursoSeleccionado->parciales as $parcial)
                                    <th class="text-center">{{ $parcial->nombre }}</th>
                                @endforeach
                                <th class="text-center">Nota final</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cursoSeleccionado->estudiantes as $estudiante)
                                @php $resultado = $notasFinales[$estudiante->id] ?? null; @endphp
                                <tr>
                                    <td>{{ $estudiante->nombres }} {{ $estudiante->apellidos }}</td>
                                    @foreach ($cursoSeleccionado->parciales as $parcial)
                                        @php
                                            $notaParcial = collect($resultado['parciales'] ?? [])
                                                ->firstWhere('parcial.id', $parcial->id);
                                        @endphp
                                        <td class="text-center">{{ $notaParcial['nota'] ?? '—' }}</td>
                                    @endforeach
                                    <td class="text-center font-weight-bold">
                                        @if (!is_null($resultado['nota_final'] ?? null))
                                            <span class="badge badge-{{ $resultado['nota_final'] >= 3.0 ? 'success' : 'danger' }}">
                                                {{ $resultado['nota_final'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin calificar</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100" class="text-center text-muted py-3">Este curso no tiene estudiantes inscritos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="text-muted p-3 mb-0">
                    <i class="fas fa-info-circle"></i>
                    La nota final solo tiene en cuenta calificaciones registradas entre la fecha de inicio y fin del curso.
                    Cada parcial promedia sus propias tareas/quices; si le asignas un peso (%) a los parciales, la nota final
                    se calcula ponderada, si no, todos los parciales pesan igual.
                </p>
            </div>
        </div>
    @endif
</div>
@stop
