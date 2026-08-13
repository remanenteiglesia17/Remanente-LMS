<div id="custom-tabs-three-bibliografia">
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <h4>Bibliografía</h4>

            <ul class="list-group" id="bibliografias-container">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $curso->bibliografias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="list-group-item">
                        
                        <input type="hidden" name="bibliografias[<?php echo e($i); ?>][id]" value="<?php echo e($item->id); ?>">

                        <div class="row">
                            <div class="col-md">
                                <label>Título</label>
                                <input type="text" name="bibliografias[<?php echo e($i); ?>][titulo]" class="form-control"
                                    value="<?php echo e(old("bibliografias.$i.titulo", $item->titulo)); ?>" required>
                            </div>

                            <div class="col-md">
                                <label>Autor</label>
                                <input type="text" name="bibliografias[<?php echo e($i); ?>][autor]" class="form-control"
                                    value="<?php echo e(old("bibliografias.$i.autor", $item->autor)); ?>">
                            </div>

                            <div class="col-md-2">
                                <label>Tipo</label>
                                <select name="bibliografias[<?php echo e($i); ?>][tipo]" class="form-control">
                                    <option value="libro" <?php if($item->tipo == 'libro'): echo 'selected'; endif; ?>>Libro</option>
                                    <option value="articulo" <?php if($item->tipo == 'articulo'): echo 'selected'; endif; ?>>Artículo</option>
                                    <option value="web" <?php if($item->tipo == 'web'): echo 'selected'; endif; ?>>Web</option>
                                </select>
                            </div>

                            <div class="col-md">
                                <label>URL</label>
                                <input type="url" name="bibliografias[<?php echo e($i); ?>][url]" class="form-control"
                                    value="<?php echo e(old("bibliografias.$i.url", $item->url)); ?>">
                            </div>
                        </div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </ul>

            <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="addBibliografia">
                + Agregar bibliografía
            </button>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/admin/cursos/dynamic/bibliografia.blade.php ENDPATH**/ ?>