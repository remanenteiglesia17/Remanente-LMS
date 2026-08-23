@extends('adminlte::page')

@section('title', 'Mis Tareas')

@section('content')
    <div class="container-fluid">
        <div class="card card-primary card-outline mt-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tasks"></i> Listado de Tareas
                </h3>
                <div class="card-tools">
                    <a href="{{ route('admin.profesor.tareas.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Nueva Tarea
                    </a>
                </div>
            </div>

            <div class="card-body">
                {{-- Filtro por curso --}}
                <div class="form-group">
                    <label>Filtrar por curso:</label>
                    <select id="filtro_curso" class="form-control">
                        <option value="">Todos los cursos</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}">{{ $curso->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <table id="tareas-table" class="table table-striped table-bordered table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>Curso</th>
                            <th>Módulo</th>
                            <th>Título</th>
                            <th>Fecha Entrega</th>
                            <th>Puntaje Máx.</th>
                            <th>Peso (%)</th>
                            <th>Entregas</th>
                            <th>Ver</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tareas as $tarea)
                            <tr data-curso="{{ $tarea->curso_id }}">
                                <td>{{ $tarea->curso->nombre ?? 'N/A' }}</td>
                                <td>
                                    @if($tarea->modulo)
                                        <span class="badge badge-info">{{ $tarea->modulo->nombre }}</span>
                                    @else
                                        <span class="badge badge-secondary">Sin módulo</span>
                                    @endif
                                </td>
                                <td>{{ $tarea->titulo_tarea }}</td>
                                <td>{{ $tarea->fecha_entrega ? \Carbon\Carbon::parse($tarea->fecha_entrega)->format('d/m/Y H:i') : 'Sin fecha' }}
                                </td>
                                <td><span class="badge badge-light">{{ number_format($tarea->puntaje, 1) }}</span></td>
                                <td><span class="badge badge-primary">{{ number_format($tarea->peso, 0) }}%</span></td>
                                <td>
                                    <span class="badge badge-info">{{ $tarea->entregas->count() }} entregas</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.profesor.tareas.show', $tarea->id) }}" class="btn btn-sm btn-info"
                                        title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ route('admin.profesor.tareas.edit', $tarea->id) }}"
                                        class="btn btn-warning btn-sm mr-1" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @can('admin.profesor.tareas.destroy')
                                        <form id="delete-form-{{ $tarea->id }}"
                                            action="{{ route('admin.profesor.tareas.destroy', $tarea->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                data-id="{{ $tarea->id }}" data-text="¿Estás seguro de eliminar esta tarea?">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            var table = $('#tareas-table').DataTable({
                responsive: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                }
            });

            $('#filtro_curso').on('change', function () {
                var cursoId = $(this).val();

                if (cursoId) {
                    table.rows().every(function () {
                        var row = this.node();
                        if ($(row).data('curso') == cursoId) {
                            $(row).show();
                        } else {
                            $(row).hide();
                        }
                    });
                } else {
                    table.rows().every(function () {
                        $(this.node()).show();
                    });
                }

                table.draw();
            });
        });
    </script>
@endsection