<?php $__env->startSection('title', 'Calificaciones - ' . $curso->nombre); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h1><?php echo e($curso->nombre); ?></h1>
        <a href="<?php echo e(route('estudiante.calificaciones.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo e($estadisticas['total_evaluaciones']); ?></h3>
                        <p>Evaluaciones</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box <?php echo e($estadisticas['promedio_ponderado'] >= 3.0 ? 'bg-success' : 'bg-danger'); ?>">
                    <div class="inner">
                        <h3><?php echo e(number_format($estadisticas['promedio_ponderado'], 2)); ?></h3>
                        <p>Promedio Final</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?php echo e($estadisticas['aprobadas']); ?></h3>
                        <p>Aprobadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?php echo e($estadisticas['reprobadas']); ?></h3>
                        <p>Reprobadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times"></i>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $porTipo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo => $califs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-graduation-cap"></i>
                                <?php echo e(ucfirst($tipo)); ?>

                            </h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Concepto</th>
                                        <th class="text-center">Nota</th>
                                        <th class="text-center">%</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $califs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $calif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($calif->concepto); ?></td>
                                            <td class="text-center">
                                                <span class="badge badge-<?php echo e($calif->color); ?>">
                                                    <?php echo e(number_format($calif->nota, 2)); ?>

                                                </span>
                                            </td>
                                            <td class="text-center"><?php echo e($calif->porcentaje); ?>%</td>
                                            <td><?php echo e($calif->fecha_calificacion->format('d/m/Y')); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detalle de Calificaciones</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Concepto</th>
                            <th>Tipo</th>
                            <th class="text-center">Nota</th>
                            <th class="text-center">Peso</th>
                            <th class="text-center">Aporte</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $calificaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $calif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($calif->fecha_calificacion->format('d/m/Y')); ?></td>
                                <td>
                                    <?php echo e($calif->concepto); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($calif->entrega): ?>
                                        <br>
                                        <a href="<?php echo e(route('estudiante.tareas.show', $calif->entrega->tarea_id)); ?>" 
                                           class="badge badge-primary">
                                            <i class="fas fa-file"></i> Ver entrega
                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        <?php echo e(ucfirst($calif->tipo_evaluacion)); ?>

                                    </span>
                                </td>
                                <td class="text-center">
                                    <strong class="text-<?php echo e($calif->color); ?>">
                                        <?php echo e(number_format($calif->nota, 2)); ?>

                                    </strong>
                                    / <?php echo e($calif->nota_maxima); ?>

                                </td>
                                <td class="text-center"><?php echo e($calif->porcentaje); ?>%</td>
                                <td class="text-center">
                                    <?php echo e(number_format($calif->aporte_nota_final, 2)); ?>

                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($calif->observaciones): ?>
                                        <?php echo e(Str::limit($calif->observaciones, 50)); ?>

                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    No hay calificaciones registradas
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Canvas-Church60\resources\views/estudiante/calificaciones/por-curso.blade.php ENDPATH**/ ?>