

<?php $__env->startSection('title', 'Revisar Entrega'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1><i class="fas fa-search"></i> Revisando: <?php echo e($entrega->tarea->titulo_tarea); ?></h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        
        <div class="col-md-8">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Estudiante: <strong><?php echo e($entrega->estudiante->user->name); ?></strong></h3>
                </div>
                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entrega->texto_entrega): ?>
                        <label>Respuesta de texto:</label>
                        <div class="p-3 bg-light border rounded mb-4">
                            <?php echo nl2br(e($entrega->texto_entrega)); ?>

                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entrega->archivo): ?>
                        <label>Archivo adjunto:</label>
                        <div class="attachment-block clearfix">
                            <i class="fas fa-file-pdf fa-3x text-danger float-left mr-3"></i>
                            <div class="attachment-pushed">
                                <h4 class="attachment-heading">
                                    <a href="<?php echo e(asset('storage/' . $entrega->archivo)); ?>" target="_blank">
                                        <?php echo e(basename($entrega->archivo)); ?>

                                    </a>
                                </h4>
                                <div class="attachment-text">
                                    <a href="<?php echo e(asset('storage/' . $entrega->archivo)); ?>" download class="btn btn-sm btn-default mt-2">
                                        <i class="fas fa-download"></i> Descargar para revisar
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Str::endsWith($entrega->archivo, '.pdf')): ?>
                            <iframe src="<?php echo e(asset('storage/' . $entrega->archivo)); ?>" width="100%" height="500px"></iframe>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">No se adjuntaron archivos.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-md-4">
            <div class="card card-primary shadow">
                <div class="card-header">
                    <h3 class="card-title">Calificar</h3>
                </div>
                <form action="<?php echo e(route('admin.profesor.calificaciones.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="entrega_id" value="<?php echo e($entrega->id); ?>">
                    
                    <div class="card-body">
                        <div class="form-group">
                            <label>Nota (Máx: <?php echo e($entrega->tarea->puntaje); ?>)</label>
                            <input type="number" name="nota" class="form-control form-control-lg" 
                                   step="0.1" min="0" max="<?php echo e($entrega->tarea->puntaje); ?>"
                                   value="<?php echo e(optional($entrega->calificacion)->nota); ?>" required autofocus>
                        </div>

                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea name="observaciones" class="form-control" rows="5"><?php echo e(optional($entrega->calificacion)->observaciones); ?></textarea>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-block">Guardar Calificación</button>
                        <a href="<?php echo e(route('admin.profesor.calificaciones.index', ['curso_id' => $entrega->tarea->curso_id])); ?>" 
                           class="btn btn-default btn-block">Volver al Libro</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/profesor/calificaciones/revision.blade.php ENDPATH**/ ?>