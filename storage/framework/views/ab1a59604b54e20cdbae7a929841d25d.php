<?php $__env->startSection('title', 'Auditoría'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>Bitácora de auditoría</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">Filtros</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo e(route('admin.auditorias.index')); ?>" class="row g-2">
            <div class="col-md-3 mb-2">
                <label class="small mb-1">Usuario</label>
                <input type="text" name="usuario" class="form-control" placeholder="Nombre del usuario"
                       value="<?php echo e(request('usuario')); ?>">
            </div>
            <div class="col-md-3 mb-2">
                <label class="small mb-1">Evento</label>
                <select name="event" class="form-control">
                    <option value="">Todos</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $eventos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $evento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($evento); ?>" <?php if(request('event') === $evento): echo 'selected'; endif; ?>>
                            <?php echo e(\App\Models\Auditoria::eventoLabelPara($evento)); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <label class="small mb-1">Modelo afectado</label>
                <select name="modelo" class="form-control">
                    <option value="">Todos</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $modelos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modelo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($modelo); ?>" <?php if(request('modelo') === $modelo): echo 'selected'; endif; ?>>
                            <?php echo e(class_basename($modelo)); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div class="col-md-3 mb-2"></div>

            <div class="col-md-3 mb-2">
                <label class="small mb-1">Desde</label>
                <input type="date" name="desde" class="form-control" value="<?php echo e(request('desde')); ?>">
            </div>
            <div class="col-md-3 mb-2">
                <label class="small mb-1">Hasta</label>
                <input type="date" name="hasta" class="form-control" value="<?php echo e(request('hasta')); ?>">
            </div>
            <div class="col-md-6 mb-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-filter"></i> Filtrar</button>
                <a href="<?php echo e(route('admin.auditorias.index')); ?>" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-outline card-secondary">
    <div class="card-header">
        <h3 class="card-title">Registros (<?php echo e($auditorias->total()); ?>)</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-bordered table-hover table-sm mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Evento</th>
                    <th>Modelo</th>
                    <th>Registro afectado</th>
                    <th>IP</th>
                    <th class="text-center">Detalle</th>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $auditorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $auditoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($auditoria->created_at?->format('d/m/Y H:i:s')); ?></td>
                        <td>
                            <?php echo e($auditoria->user_name ?? 'Sistema'); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($auditoria->user_role): ?>
                                <span class="badge badge-light"><?php echo e($auditoria->user_role); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td><span class="badge badge-<?php echo e($auditoria->evento_color); ?>"><?php echo e($auditoria->evento_label); ?></span></td>
                        <td><?php echo e($auditoria->auditable_model_name); ?></td>
                        <td><?php echo e($auditoria->auditable_label ?? '—'); ?></td>
                        <td><?php echo e($auditoria->ip_address); ?></td>
                        <td class="text-center">
                            <a href="<?php echo e(route('admin.auditorias.show', $auditoria)); ?>" class="btn btn-info btn-sm" title="Ver detalle">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay registros de auditoría con estos filtros.</td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <?php echo e($auditorias->onEachSide(1)->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Canvas-opA\resources\views/admin/auditorias/index.blade.php ENDPATH**/ ?>