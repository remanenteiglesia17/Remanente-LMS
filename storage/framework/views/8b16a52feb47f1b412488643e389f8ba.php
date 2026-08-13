<div id="custom-tabs-three-politicas" class="mt-4">
    <div class="row">
        <div class="col-12">
            <h4>Políticas del curso</h4>
            <p class="text-muted">Reglas y políticas establecidas para este curso.</p>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($curso->politicas->count() > 0): ?>
                <div class="accordion" id="accordionPoliticas">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $curso->politicas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $politica): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="card mb-2">
                            <div class="card-header bg-light" id="heading<?php echo e($loop->index); ?>">
                                <h5 class="mb-0">
                                    <button class="btn btn-link btn-block text-left collapsed" 
                                            type="button" 
                                            data-toggle="collapse" 
                                            data-target="#collapse<?php echo e($loop->index); ?>" 
                                            aria-expanded="<?php echo e($loop->first ? 'true' : 'false'); ?>" 
                                            aria-controls="collapse<?php echo e($loop->index); ?>">
                                        <i class="fas fa-gavel text-primary mr-2"></i>
                                        <strong><?php echo e($politica->titulo_politica); ?></strong>
                                        <i class="fas fa-chevron-down float-right mt-1"></i>
                                    </button>
                                </h5>
                            </div>

                            <div id="collapse<?php echo e($loop->index); ?>" 
                                 class="collapse <?php echo e($loop->first ? 'show' : ''); ?>" 
                                 aria-labelledby="heading<?php echo e($loop->index); ?>" 
                                 data-parent="#accordionPoliticas">
                                <div class="card-body">
                                    <p class="mb-0" style="white-space: pre-wrap;"><?php echo e($politica->contenido); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    No se han definido políticas para este curso.
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>
    </div>
</div><?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/admin/cursos/content/politicas-show.blade.php ENDPATH**/ ?>