


<?php $__env->startSection('title', 'Mis Calificaciones'); ?>

<?php $__env->startSection('content_header'); ?>
<h1>Mis Calificaciones</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <div class="row">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $promedios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cursoId => $datos): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header <?php echo e($datos['aprobado'] ? 'bg-success' : 'bg-danger'); ?>">
                    <h3 class="card-title"><?php echo e($datos['curso']->nombre); ?></h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h2 class="display-4"><?php echo e(number_format($datos['promedio'], 2)); ?></h2>
                        <p class="text-muted">Promedio Final</p>
                    </div>

                    <div class="row text-center">
                        <div class="col-6">
                            <strong><?php echo e($datos['total_calificaciones']); ?></strong>
                            <p class="text-muted small mb-0">Evaluaciones</p>
                        </div>
                        <div class="col-6">
                            <strong><?php echo e($datos['aprobado'] ? 'Aprobado' : 'Reprobado'); ?></strong>
                            <p class="text-muted small mb-0">Estado</p>
                        </div>
                    </div>

                    <div class="progress mt-3" style="height: 20px;">
                        <div class="progress-bar <?php echo e($datos['aprobado'] ? 'bg-success' : 'bg-danger'); ?>"
                            style="width: <?php echo e(($datos['promedio'] / 5) * 100); ?>%">
                            <?php echo e(number_format(($datos['promedio'] / 5) * 100, 0)); ?>%
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    
                    <a href="<?php echo e(route('estudiante.calificaciones.por-curso', $cursoId)); ?>"
                        class="btn btn-primary btn-block">
                        <i class="fas fa-eye"></i> Ver Detalle
                    </a>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($datos['aprobado']): ?>
                    <a href="<?php echo e(route('certificate.download', $cursoId)); ?>"
                        class="btn btn-success btn-block mt-2"
                        target="_blank">
                        <i class="fas fa-file-pdf"></i> Descargar Certificado
                    </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                No tienes calificaciones publicadas aún.
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($calificaciones->count() > 0): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Historial de Calificaciones</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Curso</th>
                        <th>Concepto</th>
                        <th>Tipo</th>
                        <th class="text-center">Nota</th>
                        <th class="text-center">Módulo</th>
                        <th class="text-center">Aporte</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $calificaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $calif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($calif->fecha_calificacion->format('d/m/Y')); ?></td>
                        <td><?php echo e($calif->curso->nombre); ?></td>
                        <td>
                            <?php echo e($calif->concepto); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($calif->entrega): ?>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-link"></i> Entrega digital
                            </small>
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
                            <small class="text-muted">/ <?php echo e($calif->nota_maxima); ?></small>
                        </td>
                        <td class="text-center"><?php echo e($calif->tarea->modulo->nombre ?? '—'); ?></td>
                        <td class="text-center">
                            <?php echo e(number_format($calif->aporte_nota_final, 2)); ?>

                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($calif->observaciones): ?>
                            <button type="button"
                                class="btn btn-sm btn-info"
                                data-toggle="modal"
                                data-target="#observacionesModal<?php echo e($calif->id); ?>">
                                <i class="fas fa-comment"></i> Ver
                            </button>

                            
                            <div class="modal fade" id="observacionesModal<?php echo e($calif->id); ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Observaciones</h4>
                                            <button type="button" class="close" data-dismiss="modal">
                                                &times;
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <?php echo e($calif->observaciones); ?>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                Cerrar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <span class="text-muted">Sin observaciones</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
    .display-4 {
        font-weight: bold;
    }

    .card-header.bg-success,
    .card-header.bg-danger {
        color: white;
    }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/estudiante/index.blade.php ENDPATH**/ ?>