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
                                    <th>Vigencia</th>
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
                                        <td>
                                            @if ($modulo->fecha_inicio && $modulo->fecha_fin)
                                                {{ $modulo->fecha_inicio->format('d/m/Y') }} - {{ $modulo->fecha_fin->format('d/m/Y') }}
                                            @else
                                                <span class="text-muted">Sin definir</span>
                                            @endif
                                        </td>
                                        <td>{{ $modulo->tareas_count }}</td>
                                        <td>
                                            @if ($modulo->finalizado)
                                                <span class="badge badge-success">Finalizado</span>
                                            @else
                                                <span class="badge badge-warning">En curso</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                data-target="#editModuloModal-{{ $modulo->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
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
                <div class="modal-dialog modal-lg" role="document">
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
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha inicio</label><b class="text-danger">*</b>
                                            <input type="date" name="fecha_inicio" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha fin</label><b class="text-danger">*</b>
                                            <input type="date" name="fecha_fin" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <label>Ponderación por categoría</label><b class="text-danger">*</b>
                                <p class="text-muted small mb-2">
                                    Qué % de la nota de este módulo aporta cada tipo de actividad. Debe sumar 100%.
                                </p>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Tareas</label>
                                        <div class="input-group">
                                            <input type="number" name="peso_tarea" class="form-control peso-categoria-{{ $curso->id }}"
                                                min="0" max="100" step="0.01" value="20" required>
                                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Quizzes</label>
                                        <div class="input-group">
                                            <input type="number" name="peso_quiz" class="form-control peso-categoria-{{ $curso->id }}"
                                                min="0" max="100" step="0.01" value="20" required>
                                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Exámenes</label>
                                        <div class="input-group">
                                            <input type="number" name="peso_examen" class="form-control peso-categoria-{{ $curso->id }}"
                                                min="0" max="100" step="0.01" value="30" required>
                                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Proyecto</label>
                                        <div class="input-group">
                                            <input type="number" name="peso_proyecto" class="form-control peso-categoria-{{ $curso->id }}"
                                                min="0" max="100" step="0.01" value="20" required>
                                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Foro</label>
                                        <div class="input-group">
                                            <input type="number" name="peso_foro" class="form-control peso-categoria-{{ $curso->id }}"
                                                min="0" max="100" step="0.01" value="10" required>
                                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <span id="peso-total-{{ $curso->id }}" class="badge badge-secondary d-block p-2 w-100">Suma: 100%</span>
                                    </div>
                                </div>
                                @error('peso_categoria')
                                    <small class="text-danger d-block mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Crear módulo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modales editar módulo --}}
            @foreach ($curso->modulos as $modulo)
                <div class="modal fade" id="editModuloModal-{{ $modulo->id }}" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar módulo — {{ $modulo->nombre }}</h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <form action="{{ route('admin.profesor.modulos.update', $modulo->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nombre del módulo</label><b class="text-danger">*</b>
                                        <input type="text" name="nombre" class="form-control" value="{{ $modulo->nombre }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <textarea name="descripcion" class="form-control" rows="2">{{ $modulo->descripcion }}</textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Fecha inicio</label><b class="text-danger">*</b>
                                                <input type="date" name="fecha_inicio" class="form-control"
                                                    value="{{ $modulo->fecha_inicio ? $modulo->fecha_inicio->format('Y-m-d') : '' }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Fecha fin</label><b class="text-danger">*</b>
                                                <input type="date" name="fecha_fin" class="form-control"
                                                    value="{{ $modulo->fecha_fin ? $modulo->fecha_fin->format('Y-m-d') : '' }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <label>Ponderación por categoría</label><b class="text-danger">*</b>
                                    <p class="text-muted small mb-2">
                                        Qué % de la nota de este módulo aporta cada tipo de actividad. Debe sumar 100%.
                                    </p>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Tareas</label>
                                            <div class="input-group">
                                                <input type="number" name="peso_tarea" class="form-control peso-categoria-edit-{{ $modulo->id }}"
                                                    min="0" max="100" step="0.01" value="{{ $modulo->peso_tarea }}" required>
                                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Quizzes</label>
                                            <div class="input-group">
                                                <input type="number" name="peso_quiz" class="form-control peso-categoria-edit-{{ $modulo->id }}"
                                                    min="0" max="100" step="0.01" value="{{ $modulo->peso_quiz }}" required>
                                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Exámenes</label>
                                            <div class="input-group">
                                                <input type="number" name="peso_examen" class="form-control peso-categoria-edit-{{ $modulo->id }}"
                                                    min="0" max="100" step="0.01" value="{{ $modulo->peso_examen }}" required>
                                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Proyecto</label>
                                            <div class="input-group">
                                                <input type="number" name="peso_proyecto" class="form-control peso-categoria-edit-{{ $modulo->id }}"
                                                    min="0" max="100" step="0.01" value="{{ $modulo->peso_proyecto }}" required>
                                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Foro</label>
                                            <div class="input-group">
                                                <input type="number" name="peso_foro" class="form-control peso-categoria-edit-{{ $modulo->id }}"
                                                    min="0" max="100" step="0.01" value="{{ $modulo->peso_foro }}" required>
                                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <span id="peso-total-edit-{{ $modulo->id }}" class="badge badge-secondary d-block p-2 w-100">Suma: 100%</span>
                                        </div>
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

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const inputs = document.querySelectorAll('.peso-categoria-edit-{{ $modulo->id }}');
                        const badge = document.getElementById('peso-total-edit-{{ $modulo->id }}');

                        function actualizarSuma() {
                            let total = 0;
                            inputs.forEach(input => total += parseFloat(input.value) || 0);
                            total = Math.round(total * 100) / 100;

                            badge.textContent = `Suma: ${total}%`;
                            badge.classList.remove('badge-secondary', 'badge-success', 'badge-danger');
                            badge.classList.add(Math.abs(total - 100) < 0.01 ? 'badge-success' : 'badge-danger');
                        }

                        inputs.forEach(input => input.addEventListener('input', actualizarSuma));
                        actualizarSuma();
                    });
                </script>
            @endforeach

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const inputs = document.querySelectorAll('.peso-categoria-{{ $curso->id }}');
                    const badge = document.getElementById('peso-total-{{ $curso->id }}');

                    function actualizarSuma() {
                        let total = 0;
                        inputs.forEach(input => total += parseFloat(input.value) || 0);
                        total = Math.round(total * 100) / 100;

                        badge.textContent = `Suma: ${total}%`;
                        badge.classList.remove('badge-secondary', 'badge-success', 'badge-danger');
                        badge.classList.add(Math.abs(total - 100) < 0.01 ? 'badge-success' : 'badge-danger');
                    }

                    inputs.forEach(input => input.addEventListener('input', actualizarSuma));
                    actualizarSuma();
                });
            </script>
        @endforeach
    </div>
@stop
