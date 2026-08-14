<?php $__env->startSection('title', 'Mis Módulos'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>Módulos de mis cursos</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('info')): ?>
            <div class="alert alert-<?php echo e(session('icon') === 'success' ? 'success' : 'info'); ?>">
                <?php echo e(session('info')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cursos->isEmpty()): ?>
            <div class="alert alert-info">No tienes cursos asignados todavía.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card card-outline card-primary mb-4">
                <div class="card-header">
                    <h3 class="card-title"><?php echo e($curso->nombre); ?></h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-secondary" data-toggle="modal"
                            data-target="#createModuloModal-<?php echo e($curso->id); ?>">
                            <i class="fas fa-plus-circle"></i> Nuevo módulo
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($curso->modulos->isEmpty()): ?>
                        <p class="text-muted p-3 mb-0">Aún no has creado módulos para este curso.</p>
                    <?php else: ?>
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Tareas</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $curso->modulos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modulo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($modulo->orden); ?></td>
                                        <td><?php echo e($modulo->nombre); ?></td>
                                        <td><?php echo e($modulo->tareas_count); ?></td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modulo->finalizado): ?>
                                                <span class="badge badge-success">Finalizado</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">En curso</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <form action="<?php echo e(route('admin.profesor.modulos.toggle-finalizado', $modulo->id)); ?>"
                                                method="POST" style="display:inline;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit"
                                                    class="btn btn-sm <?php echo e($modulo->finalizado ? 'btn-warning' : 'btn-success'); ?>">
                                                    <?php echo e($modulo->finalizado ? 'Reabrir' : 'Finalizar'); ?>

                                                </button>
                                            </form>
                                            <form action="<?php echo e(route('admin.profesor.modulos.destroy', $modulo->id)); ?>"
                                                method="POST" style="display:inline;"
                                                onsubmit="return confirm('¿Eliminar este módulo? Las tareas que tenga quedarán sin módulo asignado.');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="modal fade" id="createModuloModal-<?php echo e($curso->id); ?>" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Nuevo módulo — <?php echo e($curso->nombre); ?></h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <form action="<?php echo e(route('admin.profesor.modulos.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="modal-body">
                                <input type="hidden" name="curso_id" value="<?php echo e($curso->id); ?>">
                                <div class="form-group">
                                    <label>Nombre del módulo</label><b class="text-danger">*</b>
                                    <input type="text" name="nombre" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <textarea name="descripcion" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Crear módulo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Canvas-Church60\resources\views/profesor/modulos/index.blade.php ENDPATH**/ ?>