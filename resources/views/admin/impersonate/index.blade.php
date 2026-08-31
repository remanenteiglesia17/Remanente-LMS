@extends('adminlte::page')

@section('title', 'Ver como Rol')

@section('content_header')
    <h1>Cambiar Vista por Rol</h1>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        @foreach($roles as $rol)
            <div class="col-md-4">
                <div class="card card-outline card-info shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-shield fa-3x text-info"></i>
                        </div>
                        <h4 class="font-weight-bold text-capitalize">{{ $rol->name }}</h4>
                        <p class="text-muted small">Visualiza el panel, menús y permisos tal como los ve un usuario con este rol.</p>
                        
                        <a href="{{ route('admin.impersonate.rol', $rol->name) }}" class="btn btn-primary btn-block">
                            <i class="fas fa-eye mr-1"></i> Ver como {{ ucfirst($rol->name) }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection