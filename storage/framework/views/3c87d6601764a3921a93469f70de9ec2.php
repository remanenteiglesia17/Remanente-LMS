<?php $__env->startSection('title', 'Estudiantes del Curso'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>Estudiantes de <?php echo e($curso->nombre); ?></h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Inscritos</h3>
        <div class="card-tools">
            <span class="badge badge-primary"><?php echo e($estudiantes->count()); ?> estudiantes</span>
        </div>
    </div>
    <div class="card-body">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($estudiantes->isEmpty()): ?>
            <div class="alert alert-warning">
                No hay estudiantes inscritos en este curso.
            </div>
        <?php else: ?>
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Estudiante</th>
                        <th>Cédula</th>
                        <th>Fecha de inscripción</th>
                        <th>Horas realizadas</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $estudiantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $estudiante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($key + 1); ?></td>
                            <td><?php echo e($estudiante->nombres); ?> <?php echo e($estudiante->apellidos); ?></td>
                            <td><?php echo e($estudiante->cc); ?></td>
                            <td>
                                <?php echo e($estudiante->fecha_inscripcion 
                                    ? \Carbon\Carbon::parse($estudiante->fecha_inscripcion)->format('d/m/Y') 
                                    : '-'); ?>

                            </td>
                            <td><?php echo e($estudiante->horas_realizadas ?? 0); ?>h</td>
                            <td>
                                <span class="badge badge-success">Activo</span>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.estudiantes.show', $estudiante->id)); ?>" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="card-footer">
        <a href="<?php echo e(route('admin.inscripciones.index')); ?>" class="btn btn-secondary">
            Volver
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Remanente\Canvas-Church5\resources\views/admin/inscripciones/estudiantes.blade.php ENDPATH**/ ?>