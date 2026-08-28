<?php $layoutHelper = app('JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper'); ?>
<?php $preloaderHelper = app('JeroenNoten\LaravelAdminLte\Helpers\preloaderHelper'); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($layoutHelper->isLayoutTopnavEnabled()): ?>
    <?php ( $def_container_class = 'container' ); ?>
<?php else: ?>
    <?php ( $def_container_class = 'container-fluid' ); ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<div class="<?php echo e($layoutHelper->makeContentWrapperClasses()); ?>">

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($preloaderHelper->isPreloaderEnabled('cwrapper')): ?>
        <?php echo $__env->make('adminlte::partials.common.preloader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('impersonator_id')): ?>
        <div class="alert alert-warning text-center mb-0 rounded-0" style="border: none;">
            <i class="fas fa-user-secret"></i>
            Estás viendo la plataforma como <strong><?php echo e(auth()->user()->name); ?></strong> (estudiante de ejemplo).
            <a href="<?php echo e(route('admin.impersonate.detener')); ?>" class="btn btn-sm btn-dark ml-2">
                <i class="fas fa-arrow-left"></i> Volver a mi cuenta
            </a>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if (! empty(trim($__env->yieldContent('content_header')))): ?>
        <div class="content-header">
            <div class="<?php echo e(config('adminlte.classes_content_header') ?: $def_container_class); ?>">
                <?php echo $__env->yieldContent('content_header'); ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="content">
        <div class="<?php echo e(config('adminlte.classes_content') ?: $def_container_class); ?>">
            <?php echo $__env->yieldPushContent('content'); ?>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>

</div>
<?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/vendor/adminlte/partials/cwrapper/cwrapper-default.blade.php ENDPATH**/ ?>