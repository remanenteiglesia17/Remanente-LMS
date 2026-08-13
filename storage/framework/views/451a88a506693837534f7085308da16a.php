<?php $__env->startSection('title', 'Libro de Calificaciones'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1><i class="fas fa-book"></i> Gestión de Calificaciones</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-body">
                <form action="<?php echo e(route('admin.profesor.calificaciones.index')); ?>" method="GET">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Cursos en su Horario:</label>
                            <select name="curso_id" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Seleccione un curso --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($curso->id); ?>"
                                        <?php echo e(request('curso_id') == $curso->id ? 'selected' : ''); ?>>
                                        <?php echo e($curso->codigo); ?> - <?php echo e($curso->nombre); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cursoSeleccionado): ?>
            <div class="card shadow">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Planilla: <?php echo e($cursoSeleccionado->nombre); ?></h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped m-0" id="tabla-notas">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50" class="text-center">#</th>
                                    <th>Estudiante</th>
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tareasDelCurso; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tarea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="text-center">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tarea->modulo): ?>
                                                <span class="badge badge-secondary d-block mb-1" style="font-size:10px">
                                                    <i class="fas fa-layer-group"></i> <?php echo e($tarea->modulo->nombre); ?>

                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php echo e($tarea->titulo_tarea); ?> <br>
                                            <small class="badge badge-info"><?php echo e($tarea->porcentaje); ?>%</small>
                                        </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <th class="text-center bg-gray-light">FINAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $estudiantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $estudiante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="text-center align-middle"><?php echo e($index + 1); ?></td>
                                        <td class="align-middle">
                                            <strong><?php echo e($estudiante->user->name); ?></strong>
                                        </td>

                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tareasDelCurso; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tarea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td class="text-center p-1" style="min-width: 130px;">
                                                <?php
                                                    // Buscamos la única entrega que vincula a este alumno con esta tarea
                                                    $entrega = $estudiante->entregas->firstWhere(
                                                        'tarea_id',
                                                        $tarea->id,
                                                    );

                                                    $calificacion = $estudiante->calificaciones
                                                        ->where('concepto', $tarea->titulo_tarea)
                                                        ->first();
                                                ?>

                                                
                                                <input type="number"
                                                    name="notas[<?php echo e($estudiante->id); ?>][<?php echo e($tarea->id); ?>]"
                                                    class="form-control form-control-sm text-center input-nota mb-1"
                                                    step="0.1" min="0" max="<?php echo e($tarea->puntaje); ?>"
                                                    value="<?php echo e($calificacion->nota ?? ''); ?>"
                                                    data-peso="<?php echo e($tarea->porcentaje); ?>" data-max="<?php echo e($tarea->puntaje); ?>">

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entrega): ?>
                                                    
                                                    <a href="<?php echo e(route('admin.profesor.calificaciones.revision', $entrega->id)); ?>"
                                                        class="btn btn-xs btn-primary btn-block shadow-sm">
                                                        <i class="fas fa-file-alt"></i> Ver Entrega
                                                    </a>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entrega->entrega_tardia): ?>
                                                        <small class="text-danger" style="font-size: 0.6rem;">
                                                            <i class="fas fa-clock"></i> Entregada tarde
                                                        </small>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary disabled"
                                                        style="font-size: 0.65rem; opacity: 0.6;">
                                                        Sin actividad
                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <td class="text-center align-middle">
                                            <span class="badge badge-primary nota-final"
                                                id="final-<?php echo e($estudiante->id); ?>">0.0</span>
                                        </td>

                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="<?php echo e(count($tareasDelCurso) + 4); ?>" class="text-center">No hay
                                            estudiantes.</td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-success float-right" onclick="guardarTodo()">
                        <i class="fas fa-save"></i> Guardar Planilla
                    </button>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        $(document).ready(function() {
            calcularFinales();
            $('.input-nota').on('input', calcularFinales);
        });

        function calcularFinales() {
            $('#tabla-notas tbody tr').each(function() {
                let row = $(this);
                let totalPonderado = 0;
                let sumaPesos = 0;

                row.find('.input-nota').each(function() {
                    let nota = parseFloat($(this).val()) || 0;
                    let peso = parseFloat($(this).data('peso')) || 0;
                    let max = parseFloat($(this).data('max')) || 100;

                    // Normalizamos la nota a base 5.0 para el cálculo final si es necesario
                    // O simplemente sumamos (nota * (peso/100))
                    totalPonderado += (nota * (peso / 100));
                    sumaPesos += peso;
                });

                let badge = row.find('.nota-final');
                badge.text(totalPonderado.toFixed(2));

                if (totalPonderado >= 3.0) {
                    badge.removeClass('badge-danger').addClass('badge-success');
                } else {
                    badge.removeClass('badge-success').addClass('badge-danger');
                }
            });
        }

        function guardarTodo() {
            // ... lógica de AJAX que ya tienes ...
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/profesor/calificaciones/index.blade.php ENDPATH**/ ?>