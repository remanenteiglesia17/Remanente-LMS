<?php $__env->startSection('title', 'Cursos del Estudiante'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>Cursos de <?php echo e($estudiante->nombres); ?> <?php echo e($estudiante->apellidos); ?></h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Inscripciones</h3>
    </div>
    <div class="card-body">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cursos->isEmpty()): ?>
            <div class="alert alert-warning">
                Este estudiante no está inscrito en ningún curso.
            </div>
        <?php else: ?>
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Curso</th>
                        <th>Código</th>
                        <th>Periodo</th>
                        <th>Fecha de inscripción</th>
                        <th>Horas realizadas</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $curso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($curso->nombre); ?></td>
                            <td><?php echo e($curso->codigo ?? '-'); ?></td>
                            <td><?php echo e($curso->periodo ?? '-'); ?></td>
                            <td>
                                <?php echo e($curso->pivot->fecha_inscripcion 
                                    ? \Carbon\Carbon::parse($curso->pivot->fecha_inscripcion)->format('d/m/Y') 
                                    : '-'); ?>

                            </td>
                                <td class="text-center">
                                    <div class="progress" style="height: 20px;">
                                        <?php
                                            $porcentaje = $curso->horas_requeridas > 0 
                                                ? round(($curso->pivot->horas_realizadas / $curso->horas_requeridas) * 100, 2)
                                                : 0;
                                            $clase = $porcentaje >= 100 ? 'bg-success' : ($porcentaje >= 50 ? 'bg-info' : 'bg-warning');
                                        ?>
                                        <div class="progress-bar <?php echo e($clase); ?>" role="progressbar" 
                                             style="width: <?php echo e(min($porcentaje, 100)); ?>%">
                                            <?php echo e($curso->pivot->horas_realizadas ?? 0); ?>h / <?php echo e($curso->horas_requeridas); ?>h
                                        </div>
                                    </div>
                                    <small class="text-muted"><?php echo e($porcentaje); ?>%</small>
                                </td>
<td class="text-center">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($curso->pivot->estado)): ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($curso->pivot->estado):
                                            case ('activo'): ?>
                                                <span class="badge badge-primary">Activo</span>
                                                <?php break; ?>
                                            <?php case ('retirado'): ?>
                                                <span class="badge badge-warning">Retirado</span>
                                                <?php break; ?>
                                            <?php case ('aprobado'): ?>
                                                <span class="badge badge-success">Aprobado</span>
                                                <?php break; ?>
                                            <?php case ('reprobado'): ?>
                                                <span class="badge badge-danger">Reprobado</span>
                                                <?php break; ?>
                                            <?php default: ?>
                                                <span class="badge badge-secondary"><?php echo e(ucfirst($curso->pivot->estado)); ?></span>
                                        <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Activo</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="card-footer">
        <a href="<?php echo e(route('admin.inscripciones.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Inscripciones
        </a>
        <a href="<?php echo e(route('admin.estudiantes.show', $estudiante->id)); ?>" class="btn btn-info">
            <i class="fas fa-user"></i> Ver Perfil del Estudiante
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/admin/inscripciones/cursos.blade.php ENDPATH**/ ?>