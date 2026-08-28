@extends('adminlte::page')

@section('title', 'Asistencia')

@section('content_header')
    <h2>Asistencia</h2>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Lista de Asistencias</h3>
                </div>
                <div class="card-body">
                    <form id="asistenciaForm" action="{{ route('admin.asistencias.store') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table id="asistencias" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Estudiante</th>
                                        <th>Clase</th>
                                        <th>Fecha</th>
                                        <th class="text-center">Asistió</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($clases as $clase)
                                        @foreach ($clase->estudiantes as $estudiante)
                                            @php
                                                $keyAsistencia = $clase->id . '-' . $estudiante->id;
                                                $asistencia = $asistencias[$keyAsistencia] ?? null;
                                                $asistio = $asistencia ? $asistencia->asistio : false;
                                            @endphp
                                            <tr>
                                                <td>{{ $estudiante->nombre_completo ?? ($estudiante->nombres . ' ' . $estudiante->apellidos) }}</td>
                                                <td>{{ $clase->titulo }}</td>
                                                <td>{{ $clase->fecha_hora_inicio ? $clase->fecha_hora_inicio->format('d/m/Y H:i') : 'N/A' }}</td>
                                                <td class="text-center">
                                                    <input type="hidden" name="clases[{{ $clase->id }}][{{ $estudiante->id }}][estudiante_id]" 
                                                           value="{{ $estudiante->id }}">
                                                    <input type="checkbox" 
                                                           name="clases[{{ $clase->id }}][{{ $estudiante->id }}][asistio]" 
                                                           value="1" 
                                                           {{ $asistio ? 'checked' : '' }} 
                                                           onchange="actualizarAsistencia({{ $clase->id }}, {{ $estudiante->id }}, this.checked)">
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>  
@endsection

@section('js')
    <script> 
        $(document).ready(function() {
            $('#asistencias').DataTable({
                responsive: true,
                autoWidth: false,
                scrollX: true,
            });
        });

        function actualizarAsistencia(claseId, estudianteId, asistio) {
            const data = {
                _token: '{{ csrf_token() }}',
                clase_id: claseId,
                estudiante_id: estudianteId,
                asistio: asistio ? 1 : 0
            };

            fetch("{{ route('admin.asistencias.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) throw new Error('Error en la actualización');
                return response.json();
            })
            .then(data => {
                console.log('Asistencia actualizada correctamente');
            })
            .catch(error => {
                console.error('Hubo un problema con la actualización:', error);
            });
        }
    </script>
@endsection 