

<?php $__env->startSection('title', 'Mis Calificaciones'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1 class="m-0 text-dark">
        <i class="fas fa-graduation-cap text-primary"></i> Mis Calificaciones
    </h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-3">
        <div class="row">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $promedios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cursoId => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $curso = $data['curso']; ?>
                <div class="col-lg-6 col-12 mb-4">
                    <div class="card card-outline <?php echo e($data['aprobado'] ? 'card-success' : 'card-danger'); ?> h-100 shadow-sm">
                        
                        
                        <div class="card-header bg-white border-0 pt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge badge-light border">Período: <?php echo e($curso->periodo); ?></span>
                                <span class="badge <?php echo e($data['aprobado'] ? 'badge-success' : 'badge-danger'); ?> px-3 py-2">
                                    <i class="fas <?php echo e($data['aprobado'] ? 'fa-check-circle' : 'fa-exclamation-triangle'); ?>"></i>
                                    <?php echo e($data['aprobado'] ? 'Aprobando' : 'En riesgo'); ?>

                                </span>
                            </div>
                            <h4 class="card-title font-weight-bold mt-2 text-primary" style="font-size: 1.25rem;">
                                <?php echo e($curso->codigo); ?> - <?php echo e($curso->nombre); ?>

                            </h4>
                        </div>

                        
                        <div class="card-body">
                            <div class="row text-center mb-3">
                                <div class="col-6 border-right">
                                    <span class="text-muted d-block small">Mi promedio ponderado</span>
                                    <h2 class="font-weight-bold my-0 <?php echo e($data['promedio'] >= 3.0 ? 'text-success' : 'text-danger'); ?>">
                                        <?php echo e(number_format($data['promedio'], 2)); ?>

                                    </h2>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted d-block small">Promedio del grupo (todos los estudiantes)</span>
                                    <h2 class="font-weight-bold my-0 text-info">
                                        <?php echo e(number_format($data['promedio_curso'], 2)); ?>

                                    </h2>
                                </div>
                            </div>

                            
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Progreso de Evaluaciones</span>
                                    <span><?php echo e($data['total_calificaciones']); ?> de <?php echo e($data['total_tareas_curso']); ?> (<?php echo e($data['porcentaje_completado']); ?>%)</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: <?php echo e($data['porcentaje_completado']); ?>%" 
                                         aria-valuenow="<?php echo e($data['porcentaje_completado']); ?>" 
                                         aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="card-footer bg-light border-0 d-flex justify-content-between align-items-center">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data['puede_descargar']): ?>
                                <a href="<?php echo e(route('certificate.download', $curso->id)); ?>" 
                                   class="btn btn-sm btn-outline-success" 
                                   title="Descargar Certificado">
                                    <i class="fas fa-certificate"></i> Certificado
                                </a>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline-secondary" disabled 
                                        title="<?php echo e($data['razon_bloqueo']); ?>">
                                    <i class="fas fa-lock"></i> Certificado
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <a href="<?php echo e(route('estudiante.calificaciones.por-curso', $curso->id)); ?>" 
                               class="btn btn-sm btn-primary px-3">
                                Ver Detalle Completo <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12">
                    <div class="alert alert-info shadow-sm">
                        <i class="fas fa-info-circle"></i> Aún no tienes calificaciones registradas en tus cursos.
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/estudiante/calificaciones/index.blade.php ENDPATH**/ ?>