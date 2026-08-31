@extends('adminlte::page')

@section('title', 'Estadísticas de Cursos')

@section('content_header')
    <h1 class="text-primary">📊 Estadísticas de Cursos</h1>
@stop

@section('content')
@php
    // $cursosEstudiantes son objetos Estudiante con su relación 'cursos' (aprobados)
    // ya filtrada en el controlador por estado = 'aprobado'.
    $filas = collect($cursosEstudiantes ?? [])->flatMap(function ($estudiante) {
        return collect($estudiante->cursos)->map(function ($curso) use ($estudiante) {
            return (object) [
                'estudiante_nombre' => trim(($estudiante->nombres ?? '') . ' ' . ($estudiante->apellidos ?? '')),
                'curso_nombre' => $curso->nombre,
                'estado' => $curso->pivot->estado ?? 'aprobado',
            ];
        });
    })->values();

    $totalAprobados = $filas->count();
    $totalEstudiantes = collect($cursosEstudiantes ?? [])->count();

    // Cursos aprobados agrupados por estudiante (para el gráfico de barras)
    $porEstudianteLabels = $filas->groupBy('estudiante_nombre')->keys()->values();
    $porEstudianteData = $filas->groupBy('estudiante_nombre')->map(fn($g) => $g->count())->values();
@endphp

<div class="row">
    {{-- 1️⃣ Total de cursos aprobados --}}
    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">Cursos aprobados en total</h3>
            </div>
            <div class="card-body">
                <p class="display-4 text-success text-center mb-0">{{ $totalAprobados }}</p>
                <p class="text-muted text-center">de {{ $totalEstudiantes }} estudiantes revisados</p>
            </div>
        </div>
    </div>

    {{-- 2️⃣ Cursos aprobados por estudiante --}}
    <div class="col-md-6">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Cursos aprobados por estudiante</h3>
            </div>
            <div class="card-body">
                <canvas id="graficoPorEstudiante"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Tabla de referencia --}}
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title">Detalle de Cursos Aprobados</h3>
            </div>
            <div class="card-body">
                <table id="cursos" class="table table-striped table-bordered table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Estudiante</th>
                            <th>Curso</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($filas as $i => $fila)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $fila->estudiante_nombre }}</td>
                                <td>{{ $fila->curso_nombre }}</td>
                                <td><span class="badge badge-success">{{ ucfirst($fila->estado) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No hay cursos aprobados aún.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    new Chart(document.getElementById('graficoPorEstudiante'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($porEstudianteLabels) !!},
            datasets: [{
                label: 'Cursos aprobados',
                data: {!! json_encode($porEstudianteData) !!},
                backgroundColor: 'rgba(40, 167, 69, 0.6)',
                borderColor: '#28a745',
                borderWidth: 1
            }]
        },
        options: {
            plugins: { title: { display: true, text: 'Cursos aprobados por estudiante' } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    if (typeof $ !== 'undefined' && $.fn.dataTable) {
        $('#cursos').DataTable({
            responsive: true,
            scrollX: true,
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copyHtml5', text: 'Copiar' },
                { extend: 'excelHtml5', text: 'Excel' },
                { extend: 'pdfHtml5', text: 'PDF' },
                { extend: 'print', text: 'Imprimir' }
            ],
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                paginate: { previous: "Anterior", next: "Siguiente" }
            }
        });
    } else {
        try {
            if (typeof DataTable !== 'undefined') {
                new DataTable('#cursos', { responsive: true, dom: 'Bfrtip' });
            }
        } catch (e) {
            console.warn('DataTable no inicializado (falta librería) -', e.message);
        }
    }

});
</script>
@stop
