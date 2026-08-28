

<?php $__env->startSection('title', 'Mostrar Estudiante'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>Detalle del Estudiante</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-graduate mr-1"></i>
                <?php echo e($estudiante->nombres); ?> <?php echo e($estudiante->apellidos); ?>

            </h3>
            <div class="card-tools">
                <a href="<?php echo e(route('admin.inscripciones.index')); ?>" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Identificación (CC):</b> <span class="float-right"><?php echo e($estudiante->cc ?? $estudiante->identificacion); ?></span>
                        </li>
                        <li class="list-group-item">
                            <b>Teléfono:</b> <span class="float-right"><?php echo e($estudiante->telefono); ?></span>
                        </li>
                        <li class="list-group-item">
                            <b>Sexo:</b> <span class="float-right"><?php echo e($estudiante->genero); ?></span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Dirección:</b> <span class="float-right"><?php echo e($estudiante->direccion); ?></span>
                        </li>
                        <li class="list-group-item">
                            <b>Contacto de Emergencia:</b> <span class="float-right"><?php echo e($estudiante->contacto_emergencia); ?></span>
                        </li>
                        <li class="list-group-item">
                            <b>Observaciones:</b> <span class="float-right"><?php echo e($estudiante->observaciones); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/admin/estudiantes/show.blade.php ENDPATH**/ ?>