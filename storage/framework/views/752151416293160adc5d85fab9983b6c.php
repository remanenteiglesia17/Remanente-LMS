<div id="custom-tabs-three-descripcion" class="mt-4">
    <div class="row">
        <div class="col-12">
            
            <h4>Descripción del curso</h4>

            
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h4><?php echo e($curso->codigo); ?></h4>
                            <p>Código del curso</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hashtag"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h4><?php echo e($curso->periodo); ?></h4>
                            <p>Período académico</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="small-box <?php echo e($curso->estado ? 'bg-primary' : 'bg-secondary'); ?>">
                        <div class="inner">
                            <h4><?php echo e($curso->estado ? 'Activo' : 'Inactivo'); ?></h4>
                            <p>Estado del curso</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-<?php echo e($curso->estado ? 'check' : 'times'); ?>-circle"></i>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="form-group">
                <label class="font-weight-bold">Nombre del curso</label>
                <div class="alert alert-light border">
                    <h5 class="mb-0"><?php echo e($curso->nombre); ?></h5>
                </div>
            </div>

            
            <div class="form-group">
                <label class="font-weight-bold">Descripción</label>
                <div class="card content-card">
                    <div class="card-body">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($curso->descripcion): ?>
                            <p class="mb-0" style="white-space: pre-wrap;"><?php echo e($curso->descripcion); ?></p>
                        <?php else: ?>
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle"></i> Sin descripción registrada
                            </p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>

            <hr>
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/admin/cursos/content/descripcion-show.blade.php ENDPATH**/ ?>