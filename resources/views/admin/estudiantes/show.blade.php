@extends('adminlte::page')

@section('title', 'Mostrar Estudiante')

@section('content_header')
    <h1>Detalle del Estudiante</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-graduate mr-1"></i>
                {{ $estudiante->nombres }} {{ $estudiante->apellidos }}
            </h3>
            <div class="card-tools">
                <a href="{{ route('admin.inscripciones.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Identificación (CC):</b> <span class="float-right">{{ $estudiante->cc ?? $estudiante->identificacion }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Teléfono:</b> <span class="float-right">{{ $estudiante->telefono }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Sexo:</b> <span class="float-right">{{ $estudiante->genero }}</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Dirección:</b> <span class="float-right">{{ $estudiante->direccion }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Contacto de Emergencia:</b> <span class="float-right">{{ $estudiante->contacto_emergencia }}</span>
                        </li>
                        <li class="list-group-item">
                            <b>Observaciones:</b> <span class="float-right">{{ $estudiante->observaciones }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@stop