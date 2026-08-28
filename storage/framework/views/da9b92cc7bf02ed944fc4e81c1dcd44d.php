

<?php $__env->startSection('title', 'Asistencia'); ?>

<?php $__env->startSection('content_header'); ?>
    <h2>Asistencia</h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Lista de Asistencias</h3>
                </div>
                <div class="card-body">
                    <form id="asistenciaForm" action="<?php echo e(route('admin.asistencias.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="table-responsive">
                            <table id="asistencias" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Estudiante</th>
                                        <th>Clase</th>
                                        <th>Fecha</th>
                                        <th class="text-center">Asistió</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $clases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $clase->estudiantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estudiante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $keyAsistencia = $clase->id . '-' . $estudiante->id;
                                                $asistencia = $asistencias[$keyAsistencia] ?? null;
                                                $asistio = $asistencia ? $asistencia->asistio : false;
                                            ?>
                                            <tr>
                                                <td><?php echo e($estudiante->nombre_completo ?? ($estudiante->nombres . ' ' . $estudiante->apellidos)); ?></td>
                                                <td><?php echo e($clase->titulo); ?></td>
                                                <td><?php echo e($clase->fecha_hora_inicio ? $clase->fecha_hora_inicio->format('d/m/Y H:i') : 'N/A'); ?></td>
                                                <td class="text-center">
                                                    <input type="hidden" name="clases[<?php echo e($clase->id); ?>][<?php echo e($estudiante->id); ?>][estudiante_id]" 
                                                           value="<?php echo e($estudiante->id); ?>">
                                                    <input type="checkbox" 
                                                           name="clases[<?php echo e($clase->id); ?>][<?php echo e($estudiante->id); ?>][asistio]" 
                                                           value="1" 
                                                           <?php echo e($asistio ? 'checked' : ''); ?> 
                                                           onchange="actualizarAsistencia(<?php echo e($clase->id); ?>, <?php echo e($estudiante->id); ?>, this.checked)">
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>  
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script> 
        $(document).ready(function() {
            $('#asistencias').DataTable({
                responsive: true,
                autoWidth: false,
                scrollX: true,
            });
        });

        function actualizarAsistencia(claseId, estudianteId, asistio) {
            const data = {
                _token: '<?php echo e(csrf_token()); ?>',
                clase_id: claseId,
                estudiante_id: estudianteId,
                asistio: asistio ? 1 : 0
            };

            fetch("<?php echo e(route('admin.asistencias.store')); ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) throw new Error('Error en la actualización');
                return response.json();
            })
            .then(data => {
                console.log('Asistencia actualizada correctamente');
            })
            .catch(error => {
                console.error('Hubo un problema con la actualización:', error);
            });
        }
    </script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/admin/asistencias/index.blade.php ENDPATH**/ ?>