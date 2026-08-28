                            
                            <?php
                                $documentosCurso = $curso->documentos ?? collect();
                            ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($documentosCurso->isNotEmpty()): ?>
                                <h4 class="mb-3">Recursos del curso</h4>

                                <h5 class="mt-4">Documentos</h5>
                                <div class="list-group mb-4">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $documentosCurso; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $documento): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <a href="<?php echo e(asset('storage/' . $documento->archivo)); ?>"
                                            target="_blank"
                                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-file-<?php echo e($documento->tipo === 'pdf' ? 'pdf text-danger' : ($documento->tipo === 'zip' ? 'archive text-primary' : 'alt text-success')); ?>"></i>
                                                <span class="ml-2"><?php echo e($documento->titulo); ?></span>
                                            </div>
                                        </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            
<?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/estudiante/partials/recursos.blade.php ENDPATH**/ ?>