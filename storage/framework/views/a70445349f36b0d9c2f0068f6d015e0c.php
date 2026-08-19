<?php $__env->startSection('title', 'Parciales y Nota Final'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1><i class="fas fa-calendar-check"></i> Parciales y Nota Final</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('info')): ?>
        <div class="alert alert-<?php echo e(session('icon') === 'success' ? 'success' : 'info'); ?>">
            <?php echo e(session('info')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cursos->isEmpty()): ?>
        <div class="alert alert-info">No tienes cursos asignados todavía.</div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card card-outline card-primary mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <?php echo e($curso->codigo); ?> - <?php echo e($curso->nombre); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($curso->fecha_inicio || $curso->fecha_fin): ?>
                        <small class="text-muted">
                            (<?php echo e(optional($curso->fecha_inicio)->format('d/m/Y') ?? '—'); ?>

                            al
                            <?php echo e(optional($curso->fecha_fin)->format('d/m/Y') ?? '—'); ?>)
                        </small>
                    <?php else: ?>
                        <small class="text-warning">
                            <i class="fas fa-exclamation-triangle"></i> Este curso no tiene fecha de inicio/fin definida (pídele al admin que la configure).
                        </small>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </h3>
                <div class="card-tools">
                    <a href="<?php echo e(route('admin.profesor.parciales.index', ['curso_id' => $curso->id])); ?>" class="btn btn-sm btn-info">
                        <i class="fas fa-chart-bar"></i> Ver nota final
                    </a>
                    <button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#createParcialModal-<?php echo e($curso->id); ?>">
                        <i class="fas fa-plus-circle"></i> Nuevo parcial
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($curso->parciales->isEmpty()): ?>
                    <p class="text-muted p-3 mb-0">Aún no has creado parciales para este curso.</p>
                <?php else: ?>
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Fechas</th>
                                <th>Peso en nota final</th>
                                <th>Tareas / quices</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $curso->parciales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parcial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($parcial->numero); ?></td>
                                    <td><?php echo e($parcial->nombre); ?></td>
                                    <td>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($parcial->fecha_inicio || $parcial->fecha_fin): ?>
                                            <?php echo e(optional($parcial->fecha_inicio)->format('d/m/Y') ?? '—'); ?>

                                            al
                                            <?php echo e(optional($parcial->fecha_fin)->format('d/m/Y') ?? '—'); ?>

                                        <?php else: ?>
                                            <span class="text-muted">Sin definir</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td><?php echo e($parcial->porcentaje ? $parcial->porcentaje . '%' : 'Igual que los demás'); ?></td>
                                    <td><?php echo e($parcial->tareas->count()); ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editParcialModal-<?php echo e($parcial->id); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="<?php echo e(route('admin.profesor.parciales.destroy', $parcial->id)); ?>" method="POST" style="display:inline;"
                                            onsubmit="return confirm('¿Eliminar este parcial? Sus tareas quedarán sin parcial asignado.');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>

                                
                                <div class="modal fade" id="editParcialModal-<?php echo e($parcial->id); ?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar parcial</h5>
                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <form action="<?php echo e(route('admin.profesor.parciales.update', $parcial->id)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PUT'); ?>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Nombre</label><b class="text-danger">*</b>
                                                        <input type="text" name="nombre" class="form-control" value="<?php echo e($parcial->nombre); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Número / orden</label><b class="text-danger">*</b>
                                                        <input type="number" name="numero" class="form-control" min="1" value="<?php echo e($parcial->numero); ?>" required>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-6">
                                                            <label>Fecha inicio</label>
                                                            <input type="date" name="fecha_inicio" class="form-control" value="<?php echo e(optional($parcial->fecha_inicio)->format('Y-m-d')); ?>">
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label>Fecha fin</label>
                                                            <input type="date" name="fecha_fin" class="form-control" value="<?php echo e(optional($parcial->fecha_fin)->format('Y-m-d')); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Peso en la nota final (%)</label>
                                                        <input type="number" name="porcentaje" class="form-control" min="1" max="100" value="<?php echo e($parcial->porcentaje); ?>"
                                                            placeholder="Déjalo vacío para pesar igual que los demás parciales">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="modal fade" id="createParcialModal-<?php echo e($curso->id); ?>" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nuevo parcial — <?php echo e($curso->nombre); ?></h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form action="<?php echo e(route('admin.profesor.parciales.store')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="modal-body">
                            <input type="hidden" name="curso_id" value="<?php echo e($curso->id); ?>">
                            <div class="form-group">
                                <label>Nombre</label><b class="text-danger">*</b>
                                <input type="text" name="nombre" class="form-control" placeholder="Ej: Primer Parcial" required>
                            </div>
                            <div class="form-group">
                                <label>Número / orden</label>
                                <input type="number" name="numero" class="form-control" min="1" placeholder="Se asigna automáticamente si se deja vacío">
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Fecha inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Fecha fin</label>
                                    <input type="date" name="fecha_fin" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Peso en la nota final (%)</label>
                                <input type="number" name="porcentaje" class="form-control" min="1" max="100"
                                    placeholder="Déjalo vacío para pesar igual que los demás parciales">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Crear parcial</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cursoSeleccionado): ?>
        <div class="card shadow">
            <div class="card-header bg-dark">
                <h3 class="card-title">Nota final — <?php echo e($cursoSeleccionado->nombre); ?></h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped m-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>Estudiante</th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cursoSeleccionado->parciales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parcial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th class="text-center"><?php echo e($parcial->nombre); ?></th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <th class="text-center">Nota final</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $cursoSeleccionado->estudiantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estudiante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php $resultado = $notasFinales[$estudiante->id] ?? null; ?>
                                <tr>
                                    <td><?php echo e($estudiante->nombres); ?> <?php echo e($estudiante->apellidos); ?></td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cursoSeleccionado->parciales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parcial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $notaParcial = collect($resultado['parciales'] ?? [])
                                                ->firstWhere('parcial.id', $parcial->id);
                                        ?>
                                        <td class="text-center"><?php echo e($notaParcial['nota'] ?? '—'); ?></td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <td class="text-center font-weight-bold">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!is_null($resultado['nota_final'] ?? null)): ?>
                                            <span class="badge badge-<?php echo e($resultado['nota_final'] >= 3.0 ? 'success' : 'danger'); ?>">
                                                <?php echo e($resultado['nota_final']); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">Sin calificar</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="100" class="text-center text-muted py-3">Este curso no tiene estudiantes inscritos.</td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <p class="text-muted p-3 mb-0">
                    <i class="fas fa-info-circle"></i>
                    La nota final solo tiene en cuenta calificaciones registradas entre la fecha de inicio y fin del curso.
                    Cada parcial promedia sus propias tareas/quices; si le asignas un peso (%) a los parciales, la nota final
                    se calcula ponderada, si no, todos los parciales pesan igual.
                </p>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Canvas-opA\resources\views/profesor/parciales/index.blade.php ENDPATH**/ ?>