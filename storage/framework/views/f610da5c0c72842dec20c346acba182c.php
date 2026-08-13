<?php $__env->startSection('title', 'Detalle de tarea'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <h3><?php echo e($tarea->titulo); ?></h3>

    <p><?php echo e($tarea->descripcion_tarea); ?></p>

    <p>
        <strong>Fecha límite:</strong> <?php echo e($tarea->fecha_entrega ?? 'No definida'); ?> <br>
        <strong>Puntaje máximo:</strong> <?php echo e($tarea->puntaje); ?>

    </p>

    
    <h5>Documentos</h5>
    <ul>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tarea->documentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <a href="<?php echo e(asset('storage/'.$doc->archivo)); ?>" target="_blank">
                    <?php echo e($doc->titulo); ?>

                </a>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </ul>

    <hr>

    
    <h4>Entregas de estudiantes</h4>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>Fecha entrega</th>
                <th>Calificación</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tarea->entregas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entrega): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($entrega->estudiante->name); ?></td>
                <td><?php echo e($entrega->fecha_entrega); ?></td>
                <td><?php echo e($entrega->calificacion ?? 'Sin calificar'); ?></td>
                <td>
                    <button class="btn btn-sm btn-primary"
                        data-toggle="modal"
                        data-target="#calificar<?php echo e($entrega->id); ?>">
                        Calificar
                    </button>
                </td>
            </tr>

            
            <div class="modal fade" id="calificar<?php echo e($entrega->id); ?>">
                <div class="modal-dialog">
                    <form method="POST"
                        action="<?php echo e(route('profesor.entregas.calificar', $entrega)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>

                        <div class="modal-content">
                            <div class="modal-header">
                                <h5>Calificar entrega</h5>
                            </div>

                            <div class="modal-body">
                                <div class="form-group">
                                    <label>Calificación</label>
                                    <input type="number"
                                        name="calificacion"
                                        class="form-control"
                                        max="<?php echo e($tarea->puntaje); ?>"
                                        value="<?php echo e($entrega->calificacion); ?>">
                                </div>

                                <div class="form-group">
                                    <label>Comentario</label>
                                    <textarea name="comentario_profesor"
                                        class="form-control"
                                        rows="3"><?php echo e($entrega->comentario_profesor); ?></textarea>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-success">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="4" class="text-center text-muted">
                    No hay entregas aún
                </td>
            </tr>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/admin/profesor/tareas/show.blade.php ENDPATH**/ ?>