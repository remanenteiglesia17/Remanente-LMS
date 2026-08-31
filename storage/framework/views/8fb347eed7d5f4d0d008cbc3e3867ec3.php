<div class="btn-group" role="group" aria-label="Acciones">
    <a href="#" class="btn btn-warning btn-sm mr-1" data-id="<?php echo e($estudiante->id); ?>" data-toggle="modal"
        data-target="#editEstudianteModal" title="Editar">
        <i class="fas fa-edit"></i>
    </a>

    <form action="<?php echo e(route('admin.estudiantes.toggleStatus', $estudiante->user->id)); ?>" method="POST"
        style="display:inline;">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PATCH'); ?>
        <button type="submit" class="btn <?php echo e($estudiante->user->status ? 'btn-success' : 'btn-danger'); ?>">
            <?php echo $estudiante->user->status
                ? '<i class="fa-solid fa-square-check"></i>'
                : '<i class="fa-solid fa-circle-xmark"></i>'; ?>

        </button>
    </form>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasAnyRole(['superAdmin', 'admin', 'root']) && $estudiante->user): ?>
        <a href="<?php echo e(route('admin.impersonate.estudiante', $estudiante->id)); ?>" class="btn btn-info btn-sm"
            title="Ver la plataforma como este estudiante">
            <i class="fas fa-user-graduate"></i>
        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(Auth::user()->hasRole('root')): ?>
        <form id="delete-form-<?php echo e($estudiante->id); ?>" action="<?php echo e(route('admin.estudiantes.destroy', $estudiante->id)); ?>"
            method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
    <button class="btn btn-danger btn-delete">
        <i class="fas fa-trash"></i>
    </button>
        </form>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/admin/estudiantes/partials/actions.blade.php ENDPATH**/ ?>