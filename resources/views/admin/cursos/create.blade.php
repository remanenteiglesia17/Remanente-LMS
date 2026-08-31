<!-- Modal de Create -->
<div class="modal fade" id="createCursoModal" tabindex="-1" role="dialog" aria-labelledby="createCursoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCursoModalLabel">Crear Curso</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.cursos.store') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="row">
                        {{-- Periodo Académico Seleccionable --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="periodo">Periodo académico </label><b class="text-danger">*</b>
                                <select name="periodo" id="periodo" class="form-control" required>
                                    <option value="" selected disabled>Seleccione un periodo</option>
                                    @php
                                        $yearActual = date('Y');
                                    @endphp
                                    {{-- Genera opciones para el año anterior, actual y siguiente --}}
                                    @for ($i = $yearActual; $i <= $yearActual + 2; $i++)
                                        <option value="{{ $i }}-1" {{ old('periodo') == "$i-1" ? 'selected' : '' }}>{{ $i }}-1</option>
                                        <option value="{{ $i }}-2" {{ old('periodo') == "$i-2" ? 'selected' : '' }}>{{ $i }}-2</option>
                                    @endfor
                                </select>
                                @error('periodo')
                                    <small class="bg-danger text-white p-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- Código Automático / Solo Lectura --}}
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="codigo">Código</label>
                                <input type="text" class="form-control" name="codigo" id="codigo" 
                                    value="Autogenerado" readonly disabled style="background-color: #e9ecef;">
                                <small class="form-text text-muted">Se asignará automáticamente.</small>
                            </div>
                        </div>

                        {{-- Nombre del Curso --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="nombre">Nombre del curso </label><b class="text-danger">*</b>
                                <input type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required>
                                @error('nombre')
                                    <small class="bg-danger text-white p-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        {{-- Estado --}}
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="estado">Estado </label><b class="text-danger">*</b>
                                <select name="estado" id="estado" class="form-control" required>
                                    <option value="" selected disabled>Seleccione una opción</option>
                                    <option value="1" {{ old('estado') == '1' ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('estado') == '0' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('estado')
                                    <small class="bg-danger text-white p-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_inicio">Fecha de inicio del curso</label>
                                <input type="date" class="form-control" name="fecha_inicio" value="{{ old('fecha_inicio') }}">
                                <small class="form-text text-muted">La nota final solo promedia calificaciones dentro de este rango.</small>
                                @error('fecha_inicio')
                                    <small class="bg-danger text-white p-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_fin">Fecha de finalización del curso</label>
                                <input type="date" class="form-control" name="fecha_fin" value="{{ old('fecha_fin') }}">
                                @error('fecha_fin')
                                    <small class="bg-danger text-white p-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="descripcion">Descripción </label><b class="text-danger">*</b>
                                <textarea class="form-control" name="descripcion" rows="3" required>{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <small class="bg-danger text-white p-1">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Registrar curso</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>