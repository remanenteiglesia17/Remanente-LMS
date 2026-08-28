


<?php $__env->startSection('title', $tarea->titulo_tarea); ?>

<?php $__env->startSection('content_header'); ?>
    <h1><?php echo e($tarea->titulo_tarea); ?></h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-outline mt-3">
                    <div class="card-header">
                        <a class="btn btn-link pl-0" href="<?php echo e(route('estudiante.tareas.index')); ?>">
                            <i class="fas fa-arrow-left"></i> Volver a tareas
                        </a>
                        <h3 class="card-title">
                            <?php echo e($tarea->titulo_tarea); ?>

                        </h3>
                        <div class="card-tools">
                            <span class="badge <?php echo e($tarea->badge_class); ?>">
                                <?php echo e(ucfirst($tarea->estado)); ?>

                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Fecha límite:</strong>
                                <?php echo e($tarea->fecha_entrega->format('d \d\e F \d\e Y')); ?>

                            </div>
                            <div class="col-md-6 text-right">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tarea->estado === 'pendiente'): ?>
                                    <span class="text-danger">
                                        <i class="far fa-clock"></i>
                                        Faltan <?php echo e($tarea->dias_restantes); ?>

                                        <?php echo e($tarea->dias_restantes == 1 ? 'día' : 'días'); ?>

                                    </span>
                                <?php elseif($tarea->estado === 'atrasado'): ?>
                                    <span class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> Atrasado
                                    </span>
                                <?php else: ?>
                                    <span class="text-success">
                                        <i class="fas fa-check-circle"></i> Entregado
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <hr>

                        <h5>Descripción de la tarea</h5>
                        <p><?php echo e($tarea->descripcion_tarea); ?></p>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tarea->requisitos): ?>
                            <h5 class="mt-4">Requisitos</h5>
                            <?php echo nl2br(e($tarea->requisitos)); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tarea->criterios_evaluacion): ?>
                            <h5 class="mt-4">Criterios de evaluación</h5>
                            <?php echo nl2br(e($tarea->criterios_evaluacion)); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tarea->documentos->count() > 0): ?>
                            <h5 class="mt-4">Recursos de apoyo</h5>
                            <div class="list-group mb-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tarea->documentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $documento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(asset('storage/'.$documento->archivo)); ?>" target="_blank"
                                        class="list-group-item list-group-item-action">
                                        <?php
                                            $extension = pathinfo($documento->ruta, PATHINFO_EXTENSION);
                                            $iconClass = match ($extension) {
                                                'pdf' => 'fas fa-file-pdf text-danger',
                                                'doc', 'docx' => 'fas fa-file-word text-primary', 
                                                default => 'fas fa-file text-secondary',
                                            };
                                        ?>
                                        <i class="<?php echo e($iconClass); ?>"></i> <?php echo e($documento->titulo); ?>

                                    </a>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php
                            $entrega = $tarea->entregas->first();
                            $puedeEditar = $entrega
                                && !$entrega->calificacion
                                && (!$tarea->fecha_entrega || !now()->gt($tarea->fecha_entrega) || $tarea->permite_entregas_tardias);
                        ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$entrega || $puedeEditar): ?>
                            <h5 class="mt-4"><?php echo e($entrega ? 'Editar tu entrega' : 'Entrega de la tarea'); ?></h5>

                            <form action="<?php echo e($entrega ? route('estudiante.entregas.update', $entrega) : route('estudiante.entregas.store', $tarea)); ?>"
                                method="POST" enctype="multipart/form-data" id="formEntrega">
                                <?php echo csrf_field(); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entrega): ?>
                                    <?php echo method_field('PUT'); ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <input type="hidden" name="tarea_id" value="<?php echo e($tarea->id); ?>">

                                <div class="form-group">
                                    <label><?php echo e($entrega ? 'Reemplazar archivo (opcional)' : 'Subir archivo'); ?></label>
                                    <div class="custom-file">
                                        <input type="file"
                                            class="custom-file-input <?php $__errorArgs = ['archivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="taskFile"
                                            name="archivo" accept=".docx,.pdf,.jpg,.jpeg,.png" <?php echo e($entrega ? '' : 'required'); ?>>
                                        <label class="custom-file-label" for="taskFile">
                                            Seleccionar archivo
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Formatos aceptados: .docx, .pdf, .jpg, .png (máx. 50MB)
                                    </small>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['archivo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback d-block"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label>Comentarios adicionales (opcional)</label>
                                    <textarea class="form-control <?php $__errorArgs = ['comentario'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="comentario" rows="4"
                                        placeholder="Agrega cualquier comentario o nota para el instructor..."><?php echo e(old('comentario', $entrega->comentario ?? '')); ?></textarea>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['comentario'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <span class="invalid-feedback"><?php echo e($message); ?></span>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-paper-plane"></i> <?php echo e($entrega ? 'Guardar cambios' : 'Enviar tarea'); ?>

                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <h5 class="mt-4">Tu entrega</h5>
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-1">
                                        <strong>Enviado:</strong>
                                        <?php echo e($entrega->fecha_entrega ? \Carbon\Carbon::parse($entrega->fecha_entrega)->format('d/m/Y H:i') : '—'); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entrega->entrega_tardia): ?>
                                            <span class="badge badge-warning ml-1">Entrega tardía</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </p>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entrega->archivo): ?>
                                        <p class="mb-1">
                                            <a href="<?php echo e(asset('storage/'.$entrega->archivo)); ?>" target="_blank">
                                                <i class="fas fa-file"></i> Ver archivo enviado
                                            </a>
                                        </p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entrega->comentario): ?>
                                        <p class="mb-1"><strong>Tu comentario:</strong> <?php echo e($entrega->comentario); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entrega->calificacion): ?>
                                        <hr>
                                        <p class="mb-1">
                                            <strong>Calificación:</strong>
                                            <?php echo e($entrega->calificacion->nota); ?>/<?php echo e($tarea->puntaje); ?>

                                        </p>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entrega->calificacion->observaciones): ?>
                                            <p class="mb-0"><strong>Comentario del profesor:</strong> <?php echo e($entrega->calificacion->observaciones); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php elseif(!$puedeEditar): ?>
                                        <p class="text-muted mb-0">La fecha límite ya pasó. Tu entrega quedó registrada y está pendiente de calificación.</p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                
            </div>
            
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        // Actualizar el nombre del archivo seleccionado
        document.getElementById('taskFile').addEventListener('change', function(e) {
            var fileName = e.target.files[0]?.name || 'Seleccionar archivo ZIP';
            e.target.nextElementSibling.textContent = fileName;
        });

        function saveDraft() {
            alert('Funcionalidad de guardar borrador - Por implementar');
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/estudiante/tareas/show.blade.php ENDPATH**/ ?>