

<?php $__env->startSection('title', 'Detalle de Calificaciones - ' . $curso->nombre); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="m-0 text-dark"><?php echo e($curso->nombre); ?></h1>
            <p class="text-muted small mb-0">
                Código: <?php echo e($curso->codigo); ?> | Período: <?php echo e($curso->periodo); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profesor): ?>
                    | Profesor: <?php echo e($profesor->nombres); ?> <?php echo e($profesor->apellidos); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
        </div>
        <a href="<?php echo e(route('estudiante.calificaciones.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Mis Cursos
        </a>
    </div>
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
        
        
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info shadow-sm">
                    <div class="inner">
                        <h3><?php echo e($estadisticas['total_evaluaciones']); ?></h3>
                        <p>Evaluaciones Calificadas</p>
                    </div>
                    <div class="icon"><i class="fas fa-clipboard-check"></i></div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box <?php echo e($estadisticas['promedio_ponderado'] >= 3.0 ? 'bg-success' : 'bg-danger'); ?> shadow-sm">
                    <div class="inner">
                        <h3><?php echo e(number_format($estadisticas['promedio_ponderado'], 2)); ?></h3>
                        <p>Promedio Ponderado</p>
                    </div>
                    <div class="icon"><i class="fas fa-trophy"></i></div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success shadow-sm">
                    <div class="inner">
                        <h3><?php echo e($estadisticas['aprobadas']); ?></h3>
                        <p>Notas Aprobadas (≥ 3.0)</p>
                    </div>
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger shadow-sm">
                    <div class="inner">
                        <h3><?php echo e($estadisticas['reprobadas']); ?></h3>
                        <p>Notas Reprobadas (&lt; 3.0)</p>
                    </div>
                    <div class="icon"><i class="fas fa-times-circle"></i></div>
                </div>
            </div>
        </div>

        
        <h4 class="mb-3 font-weight-bold text-secondary">
            <i class="fas fa-layer-group"></i> Resumen por Módulo
        </h4>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $porModulo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $modulo = $m['modulo']; ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-book"></i> <?php echo e($modulo->nombre); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modulo->fecha_inicio && $modulo->fecha_fin): ?>
                            <small class="ml-2">(<?php echo e($modulo->fecha_inicio->format('d/m/Y')); ?> - <?php echo e($modulo->fecha_fin->format('d/m/Y')); ?>)</small>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </h3>
                    <span class="badge badge-light ml-2">Promedio del módulo: <?php echo e(number_format($m['promedio_modulo'], 2)); ?></span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $m['por_tipo']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipo => $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $meta = $etiquetasTipo[$tipo] ?? ['label' => ucfirst($tipo), 'icon' => 'fa-star', 'color' => 'secondary'];
                            ?>
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm h-100 mb-0">
                                    <div class="card-header bg-<?php echo e($meta['color']); ?> text-white">
                                        <h3 class="card-title font-weight-bold">
                                            <i class="fas <?php echo e($meta['icon']); ?>"></i> <?php echo e($meta['label']); ?> (Vale <?php echo e(number_format($grupo['peso_categoria'], 1)); ?>% de este módulo)
                                        </h3>
                                    </div>
                                    <div class="card-body p-0 table-responsive">
                                        <table class="table table-sm table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Concepto</th>
                                                    <th class="text-center">Nota</th>
                                                    <th class="text-center">Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $grupo['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $calif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($calif->concepto); ?></td>
                                                        <td class="text-center font-weight-bold text-<?php echo e($calif->color); ?>">
                                                            <?php echo e(number_format($calif->nota, 2)); ?> / <?php echo e(number_format($calif->nota_maxima, 2)); ?>

                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge <?php echo e($calif->nota >= 3.0 ? 'badge-success' : 'badge-danger'); ?>">
                                                                <?php echo e($calif->nota >= 3.0 ? 'Aprobada' : 'Reprobada'); ?>

                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr>
                                                    <th class="text-right">Promedio:</th>
                                                    <th class="text-center font-weight-bold"><?php echo e(number_format($grupo['promedio'], 2)); ?></th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="alert alert-info">Todavía no hay calificaciones registradas en este curso.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h3 class="card-title font-weight-bold text-dark">
                    <i class="fas fa-list-alt text-primary"></i> Historial Completo de Calificaciones
                </h3>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Concepto</th>
                            <th>Tipo</th>
                            <th class="text-center">Nota / Máx</th>
                            <th>Módulo</th>
                            <th class="text-center">Aporte Final</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $calificaciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $calif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php 
                                $meta = $etiquetasTipo[$calif->tipo_evaluacion] ?? ['label' => ucfirst($calif->tipo_evaluacion), 'color' => 'secondary'];
                            ?>
                            <tr>
                                <td><?php echo e($calif->fecha_calificacion->format('d/m/Y')); ?></td>
                                <td>
                                    <strong><?php echo e($calif->concepto); ?></strong>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($calif->entrega): ?>
                                        <br>
                                        <a href="<?php echo e(route('estudiante.tareas.show', $calif->entrega->tarea_id)); ?>" 
                                           class="badge badge-primary">
                                            <i class="fas fa-external-link-alt"></i> Ver entrega
                                        </a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo e($meta['color']); ?>">
                                        <?php echo e($meta['label']); ?>

                                    </span>
                                </td>
                                <td class="text-center">
                                    <strong class="text-<?php echo e($calif->color); ?>" style="font-size: 1.1rem;">
                                        <?php echo e(number_format($calif->nota, 2)); ?>

                                    </strong> 
                                    <span class="text-muted">/ <?php echo e($calif->nota_maxima); ?></span>
                                </td>
                                <td><?php echo e($calif->tarea->modulo->nombre ?? '—'); ?></td>
                                <td class="text-center font-weight-bold">
                                    <?php echo e(number_format($calif->aporte_nota_final, 2)); ?>

                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($calif->observaciones): ?>
                                        <span title="<?php echo e($calif->observaciones); ?>">
                                            <?php echo e(Str::limit($calif->observaciones, 45)); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted italic">-</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                    No hay calificaciones publicadas para este curso.
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/estudiante/calificaciones/por-curso.blade.php ENDPATH**/ ?>