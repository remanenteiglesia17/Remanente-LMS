

<?php $__env->startSection('title', 'Ver como Rol'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>Cambiar Vista por Rol</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rol): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-4">
                <div class="card card-outline card-info shadow-sm">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-shield fa-3x text-info"></i>
                        </div>
                        <h4 class="font-weight-bold text-capitalize"><?php echo e($rol->name); ?></h4>
                        <p class="text-muted small">Visualiza el panel, menús y permisos tal como los ve un usuario con este rol.</p>
                        
                        <a href="<?php echo e(route('admin.impersonate.rol', $rol->name)); ?>" class="btn btn-primary btn-block">
                            <i class="fas fa-eye mr-1"></i> Ver como <?php echo e(ucfirst($rol->name)); ?>

                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/admin/impersonate/index.blade.php ENDPATH**/ ?>