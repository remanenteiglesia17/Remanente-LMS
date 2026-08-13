<?php $__env->startSection('title', 'Mis Notificaciones'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-bell mr-2"></i>Mis Notificaciones</h1>
        <form method="POST" action="<?php echo e(route('notificaciones.read-all')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-check-double mr-1"></i>Marcar todas como leídas
            </button>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body p-0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $data = $notif->data; ?>
            <div class="d-flex align-items-start p-3 border-bottom <?php echo e(is_null($notif->read_at) ? 'bg-light' : ''); ?>">
                <div class="mr-3 mt-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_null($notif->read_at)): ?>
                        <span class="badge badge-primary" style="width:10px;height:10px;border-radius:50%;display:inline-block;padding:0"></span>
                    <?php else: ?>
                        <span class="badge badge-secondary" style="width:10px;height:10px;border-radius:50%;display:inline-block;padding:0"></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="flex-grow-1">
                    <div class="font-weight-<?php echo e(is_null($notif->read_at) ? 'bold' : 'normal'); ?>">
                        <i class="fas fa-clipboard-list text-primary mr-1"></i>
                        <?php echo e($data['titulo'] ?? 'Notificación'); ?>

                    </div>
                    <small class="text-muted">
                        <?php echo e($data['curso'] ?? ''); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($data['fecha_entrega'])): ?>
                            · Entrega: <?php echo e($data['fecha_entrega']); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        · <?php echo e($notif->created_at->diffForHumans()); ?>

                    </small>
                </div>
                <div class="ml-3 d-flex gap-2" style="gap:8px">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($data['url'])): ?>
                    <a href="<?php echo e($data['url']); ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i>
                    </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_null($notif->read_at)): ?>
                    <a href="<?php echo e(route('admin.notifications.read', $notif->id)); ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-check"></i>
                    </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-4 text-center text-muted">
                <i class="fas fa-bell-slash fa-2x mb-2 d-block"></i>
                No tienes notificaciones.
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($notifications->hasPages()): ?>
        <div class="card-footer">
            <?php echo e($notifications->links()); ?>

        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/admin/notifications/index.blade.php ENDPATH**/ ?>