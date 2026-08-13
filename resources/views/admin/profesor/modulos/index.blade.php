@extends('adminlte::page')

@section('title', 'Mis Módulos')

@section('content_header')
    <h1>Módulos de mis cursos</h1>
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

        @foreach ($cursos as $curso)
            <div class="card card-outline card-primary mb-4">
                <div class="card-header">
                    <h3 class="card-title">{{ $curso->nombre }}</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-secondary" data-toggle="modal"
                            data-target="#createModuloModal-{{ $curso->id }}">
                            <i class="fas fa-plus-circle"></i> Nuevo módulo
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if ($curso->modulos->isEmpty())
                        <p class="text-muted p-3 mb-0">Aún no has creado módulos para este curso.</p>
                    @else
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Tareas</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($curso->modulos as $modulo)
                                    <tr>
                                        <td>{{ $modulo->orden }}</td>
                                        <td>{{ $modulo->nombre }}</td>
                                        <td>{{ $modulo->tareas_count }}</td>
                                        <td>
                                            @if ($modulo->finalizado)
                                                <span class="badge badge-success">Finalizado</span>
                                            @else
                                                <span class="badge badge-warning">En curso</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.profesor.modulos.toggle-finalizado', $modulo->id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="btn btn-sm {{ $modulo->finalizado ? 'btn-warning' : 'btn-success' }}">
                                                    {{ $modulo->finalizado ? 'Reabrir' : 'Finalizar' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.profesor.modulos.destroy', $modulo->id) }}"
                                                method="POST" style="display:inline;"
                                                onsubmit="return confirm('¿Eliminar este módulo? Las tareas que tenga quedarán sin módulo asignado.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- Modal crear módulo --}}
            <div class="modal fade" id="createModuloModal-{{ $curso->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Nuevo módulo — {{ $curso->nombre }}</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <form action="{{ route('admin.profesor.modulos.store') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="curso_id" value="{{ $curso->id }}">
                                <div class="form-group">
                                    <label>Nombre del módulo</label><b class="text-danger">*</b>
                                    <input type="text" name="nombre" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <textarea name="descripcion" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Crear módulo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@stop
