


<?php $__env->startSection('title', 'Módulos - ' . $curso->nombre); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><?php echo e($curso->nombre); ?></h1>
            <small class="text-muted">Módulos del curso</small>
        </div>
        <div>
            <span class="badge badge-primary">Estudiante</span>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modulos->isEmpty()): ?>
            <div class="alert alert-info">
                Tu profesor todavía no ha creado módulos para este curso.
            </div>
        <?php else: ?>
            <div class="row">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $modulos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modulo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4 mb-4">
                        <div class="card <?php echo e($modulo->desbloqueado ? 'card-outline card-success' : 'card-outline card-secondary'); ?>">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$modulo->desbloqueado): ?>
                                        <i class="fas fa-lock text-secondary mr-1"></i>
                                    <?php elseif($modulo->finalizado): ?>
                                        <i class="fas fa-check-circle text-success mr-1"></i>
                                    <?php else: ?>
                                        <i class="fas fa-unlock text-success mr-1"></i>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    Módulo <?php echo e($modulo->orden); ?>: <?php echo e($modulo->nombre); ?>

                                </h3>
                            </div>
                            <div class="card-body">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modulo->descripcion): ?>
                                    <p><?php echo e($modulo->descripcion); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <p class="text-muted mb-0"><?php echo e($modulo->tareas_count); ?> tarea(s)</p>
                            </div>
                            <div class="card-footer">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modulo->desbloqueado): ?>
                                    <a href="<?php echo e(route('estudiante.tareas.index', ['modulo_id' => $modulo->id])); ?>"
                                        class="btn btn-primary btn-block">
                                        Ver tareas <i class="fas fa-arrow-circle-right"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-block" disabled>
                                        Bloqueado — termina el módulo anterior primero
                                    </button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-UPDATE\resources\views/estudiante/modulos/index.blade.php ENDPATH**/ ?>