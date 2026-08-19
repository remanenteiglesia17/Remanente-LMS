<?php $__env->startSection('title', 'Asistencia'); ?>

<?php $__env->startSection('content_header'); ?>
    <h2>Asistencia</h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Lista</h3>
                </div>
                <div class="card-body">
                    <form id="asistenciaForm" action="<?php echo e(route('admin.asistencias.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="table-responsive">
                            <table id="asistencias" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Estudiante</th>
                                        <th>Clase</th>
                                        <th>Fecha</th>
                                        <th>Asistió</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $clases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($clase->estudiante->nombres); ?></td>
                                            <td><?php echo e($clase->title); ?></td>
                                            <td><?php echo e($clase->start); ?></td>
                                            <td>
                                        <input type="hidden" name="claseos[<?php echo e($clase->id); ?>][estudiante_id]" 
                                                value="<?php echo e($clase->estudiante->id); ?>">
                                                <input type="checkbox" name="claseos[<?php echo e($clase->id); ?>][asistio]" 
                                                value="1" 
                                                <?php echo e(!empty($asistencias[$clase->id . '-' . $clase->estudiante->id]) && 
                                                    $asistencias[$clase->id . '-' . $clase->estudiante->id]->asistio ? 'checked' : ''); ?> 
                                                onchange="actualizarAsistencia(<?php echo e($clase->id); ?>, <?php echo e($clase->estudiante->id); ?>, this.checked)">
                                            </td>
                                        </tr>
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
        new DataTable('#asistencias', {responsive: true,autoWidth: false,scrollX:true,scrollX: true,});

        function actualizarAsistencia(claseoId, estudianteId, asistio) {

            const data = {                                              // Crear un objeto con los datos a enviar
                _token: '<?php echo e(csrf_token()); ?>',
                claseos: {[claseoId]: { estudiante_id: estudianteId, asistio: asistio ? 1 : 0}}
            };

            fetch("<?php echo e(route('admin.asistencias.store')); ?>", {            // Realizar la solicitud POST usando Fetch API
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
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

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Canvas-opA\resources\views/admin/asistencias/index.blade.php ENDPATH**/ ?>