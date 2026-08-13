<?php $__env->startSection('title', 'Inscripciones'); ?>

<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h1>Gestión de Inscripciones</h1>
        <a href="<?php echo e(route('admin.inscripciones.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Inscripción
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle"></i> <?php echo e(session('error')); ?>

                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        

        
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-hover table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th width="60">#</th>
                            <th>Estudiante</th>
                            <th>Cédula</th>
                            <th>Curso</th>
                            <th>Período</th>
                            <th>Horas</th>
                            <th>Estado</th>
                            <th>Fecha Inscripción</th>
                            <th width="150" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $inscripciones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inscripcion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($inscripcion->id); ?></td>
                                <td>
                                    <a href="<?php echo e(route('admin.inscripciones.cursos', $inscripcion->estudiante_id)); ?>">
                                        <?php echo e($inscripcion->nombres); ?> <?php echo e($inscripcion->apellidos); ?>

                                    </a>
                                </td>
                                <td><?php echo e($inscripcion->cc); ?></td>
                                <td>
                                    <a href="<?php echo e(route('admin.inscripciones.estudiantes', $inscripcion->curso_id)); ?>">
                                        <?php echo e($inscripcion->curso_nombre); ?>

                                    </a>
                                    <br>
                                    <small class="text-muted"><?php echo e($inscripcion->codigo); ?></small>
                                </td>
                                <td><?php echo e($inscripcion->periodo); ?></td>
                                <td><?php echo e($inscripcion->horas_realizadas); ?>h</td>

                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php switch($inscripcion->estado):
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
                                    <?php endswitch; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e(\Carbon\Carbon::parse($inscripcion->fecha_inscripcion)->format('d/m/Y')); ?>

                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        
                                        <a href="<?php echo e(route('admin.estudiantes.show', $inscripcion->estudiante_id)); ?>"
                                           class="btn btn-sm btn-secondary" title="Ver perfil estudiante">
                                            <i class="fas fa-user"></i>
                                        </a>

                                        
                                        <a href="<?php echo e(route('admin.cursos.show', $inscripcion->curso_id)); ?>"
                                           class="btn btn-sm btn-info" title="Ver curso">
                                            <i class="fas fa-book"></i>
                                        </a>

                                        
                                        <a href="<?php echo e(route('admin.inscripciones.edit', $inscripcion->id)); ?>"
                                           class="btn btn-sm btn-warning" title="Editar inscripción">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-toggle="modal" 
                                                data-target="#modalEstado<?php echo e($inscripcion->id); ?>"
                                                title="Cambiar estado">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                        
                                        
                                        <form action="<?php echo e(route('admin.inscripciones.destroy', $inscripcion->id)); ?>" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('¿Eliminar esta inscripción?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    
                                    <?php echo $__env->make('admin.inscripciones.partials.modal-estado', ['inscripcion' => $inscripcion], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>No hay inscripciones registradas</p>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inscripciones->hasPages()): ?>
                <div class="card-footer">
                    <?php echo e($inscripciones->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/admin/inscripciones/index.blade.php ENDPATH**/ ?>