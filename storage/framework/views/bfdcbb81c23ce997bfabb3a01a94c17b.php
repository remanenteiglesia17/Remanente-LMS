<div id="custom-tabs-three-objetivos" class="mt-4">
    <div class="row">
        <div class="col-12">
            <h4>Objetivos del curso</h4>

            
            <h5 class="mt-3">Objetivo General</h5>
            <div class="card border-primary content-card">
                <div class="card-body">
                    <?php
                        $objetivoGeneral = $curso->objetivos->where('tipo', 'general')->first();
                    ?>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($objetivoGeneral): ?>
                        <p class="mb-0"><?php echo e($objetivoGeneral->descripcion_obj); ?></p>
                    <?php else: ?>
                        <p class="text-muted mb-0">
                            <i class="fas fa-exclamation-triangle"></i>
                            No se ha definido un objetivo general.
                        </p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <hr>

            
            <h5>Objetivos Específicos</h5>
            
            <?php
                $objetivosEspecificos = $curso->objetivos->where('tipo', 'especifico');
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($objetivosEspecificos->count() > 0): ?>
                <ul class="list-group mb-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $objetivosEspecificos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $objetivo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="list-group-item">
                            <i class="fas fa-check-circle text-success mr-2"></i>
                            <?php echo e($objetivo->descripcion_obj); ?>

                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle"></i>
                    No se han definido objetivos específicos para este curso.
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <hr>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\Remanente\Canvas-Church6\resources\views/admin/cursos/content/objetivos-show.blade.php ENDPATH**/ ?>