<?php $__env->startSection('title', 'Mis Calificaciones'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>
        <i class="fas fa-star"></i> Mis Calificaciones
    </h1>
<?php $__env->stopSection(); ?>

<?php
    $etiquetasTipo = [
        'tarea' => ['label' => 'Tareas', 'icon' => 'fa-clipboard-list', 'color' => 'info'],
        'quiz' => ['label' => 'Quizzes', 'icon' => 'fa-question-circle', 'color' => 'warning'],
        'parcial' => ['label' => 'Parciales', 'icon' => 'fa-file-alt', 'color' => 'primary'],
        'examen' => ['label' => 'Exámenes', 'icon' => 'fa-file-signature', 'color' => 'primary'],
        'proyecto' => ['label' => 'Proyecto', 'icon' => 'fa-project-diagram', 'color' => 'success'],
        'participacion' => ['label' => 'Participación', 'icon' => 'fa-comments', 'color' => 'secondary'],
        'asistencia' => ['label' => 'Asistencia', 'icon' => 'fa-calendar-check', 'color' => 'secondary'],
        'otro' => ['label' => 'Otros', 'icon' => 'fa-ellipsis-h', 'color' => 'secondary'],
    ];
?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $promedios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cursoId => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $curso = $data['curso']; ?>

            
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">
                        <i class="fas fa-book"></i> <?php echo e($curso->codigo); ?> - <?php echo e($curso->nombre); ?>

                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-light">Período: <?php echo e($curso->periodo); ?></span>
                        <a href="<?php echo e(route('estudiante.calificaciones.por-curso', $curso->id)); ?>" class="badge badge-light">
                            <i class="fas fa-eye"></i> Ver detalle
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6">
                            <div class="info-box <?php echo e($data['promedio'] >= 3.0 ? 'bg-success' : 'bg-danger'); ?>">
                                <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Mi Nota Final</span>
                                    <span class="info-box-number"><?php echo e(number_format($data['promedio'], 2)); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Promedio del Curso</span>
                                    <span class="info-box-number"><?php echo e(number_format($data['promedio_curso'], 2)); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-tasks"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Evaluaciones Calificadas</span>
                                    <span class="info-box-number"><?php echo e($data['total_calificaciones']); ?>/<?php echo e($data['total_tareas_curso']); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-6">
                            <div class="info-box bg-primary">
                                <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Porcentaje Completado</span>
                                    <span class="info-box-number"><?php echo e($data['porcentaje_completado']); ?>%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data['promedio'] > 0): ?>
                        <span class="badge <?php echo e($data['aprobado'] ? 'badge-success' : 'badge-danger'); ?> p-2">
                            <i class="fas <?php echo e($data['aprobado'] ? 'fa-check-circle' : 'fa-times-circle'); ?>"></i>
                            <?php echo e($data['aprobado'] ? 'Vas aprobando el curso' : 'Vas reprobando el curso'); ?>

                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="row">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_2 = true; $__currentLoopData = $data['por_tipo']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo => $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                    <?php $meta = $etiquetasTipo[$tipo] ?? ['label' => ucfirst($tipo), 'icon' => 'fa-star', 'color' => 'secondary']; ?>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-<?php echo e($meta['color']); ?>">
                                <h3 class="card-title">
                                    <i class="fas <?php echo e($meta['icon']); ?>"></i> <?php echo e($meta['label']); ?> (<?php echo e($grupo['peso_total']); ?>%)
                                </h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th><?php echo e($meta['label']); ?></th>
                                            <th width="100" class="text-center">Nota</th>
                                            <th width="120" class="text-center">Peso</th>
                                            <th width="110" class="text-center">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $grupo['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $calif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($calif->concepto); ?></td>
                                                <td class="text-center">
                                                    <strong class="text-<?php echo e($calif->color); ?>">
                                                        <?php echo e(number_format($calif->nota, 2)); ?>

                                                    </strong>
                                                </td>
                                                <td class="text-center"><?php echo e($calif->porcentaje); ?>%</td>
                                                <td class="text-center">
                                                    <span class="badge <?php echo e($calif->nota >= 3.0 ? 'badge-success' : 'badge-danger'); ?>">
                                                        <?php echo e($calif->nota >= 3.0 ? 'Calificada' : 'Reprobada'); ?>

                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <th>Promedio <?php echo e($meta['label']); ?></th>
                                            <th class="text-center"><?php echo e(number_format($grupo['promedio'], 2)); ?></th>
                                            <th class="text-center"><?php echo e($grupo['peso_total']); ?>%</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                Aún no tienes calificaciones publicadas. Cuando tu profesor califique y publique
                tus entregas, las verás reflejadas aquí automáticamente.
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/estudiante/calificaciones/index.blade.php ENDPATH**/ ?>