<?php $__env->startSection('title', 'Mis Tareas - ' . $curso->nombre); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><?php echo e($curso->nombre); ?></h1>
            <small class="text-muted">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modulo): ?>
                    Tareas del Módulo <?php echo e($modulo->orden); ?>: <?php echo e($modulo->nombre); ?>

                <?php else: ?>
                    Tareas del curso
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </small>
        </div>
        <div class="text-right">
            <span class="badge badge-primary d-block mb-1">Estudiante</span>
            <a href="<?php echo e(route('estudiante.modulos.index')); ?>" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a módulos
            </a>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline card-outline-tabs"> 
                    <div class="card-body">
                        <div class="row" id="main-content">
                            
                            <div class="col-lg-8" id="tasks-column">
                                <div id="tasks-list">
                                    <h4 class="mb-3">Tareas del Módulo</h4>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tareas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tarea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <div class="card card-primary card-outline mb-3">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h5><?php echo e($tarea->titulo_tarea); ?></h5>
                                                        <p class="text-muted"><?php echo e($tarea->descripcion_tarea); ?></p>
                                                    </div>
                                                    <span class="badge <?php echo e($tarea->badge_class); ?>">
                                                        <?php echo e(ucfirst($tarea->estado)); ?>

                                                    </span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mt-2">
                                                    <small class="text-muted">
                                                        <i class="far fa-calendar"></i> 
                                                        Fecha límite: <?php echo e($tarea->fecha_entrega->format('d M Y')); ?>

                                                    </small>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tarea->estado === 'pendiente'): ?>
                                                        <small class="text-danger font-weight-bold">
                                                            Faltan <?php echo e($tarea->dias_restantes); ?> 
                                                            <?php echo e($tarea->dias_restantes == 1 ? 'día' : 'días'); ?>

                                                        </small>
                                                    <?php elseif($tarea->estado === 'atrasado'): ?>
                                                        <small class="text-danger font-weight-bold">
                                                            Atrasado
                                                        </small>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                </div>
                                                <div class="progress mt-2" style="height: 6px;">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: <?php echo e($tarea->progreso); ?>%"></div>
                                                </div>
                                                <div class="mt-2">
                                                    <a class="btn btn-sm btn-primary"
                                                        href="<?php echo e(route('estudiante.tareas.show', $tarea->id)); ?>">
                                                        <i class="fas fa-eye"></i> Ver detalles y entregar
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> 
                                            No hay tareas disponibles en este momento.
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div> 
                            
                            <div class="col-lg-4">
                                
                                <?php echo $__env->make('estudiante.partials.recursos', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Remanente\Canvas-Church5\resources\views/estudiante/tareas/index.blade.php ENDPATH**/ ?>