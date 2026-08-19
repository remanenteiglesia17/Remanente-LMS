

<?php $__env->startSection('title', ucfirst(auth()->user()->getRoleNames()->first())); ?>
<?php $__env->startSection('css'); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/items.css'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content_header'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($esProfesor): ?>
        <div class="row pt-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo e($misCursos->count()); ?></h3>
                        <p>Mis cursos</p>
                    </div>
                    <div class="icon"><i class="fas fa-book"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?php echo e($totalMisEstudiantes); ?></h3>
                        <p>Estudiantes a cargo</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?php echo e($entregasPendientes); ?></h3>
                        <p>Entregas por calificar</p>
                    </div>
                    <div class="icon"><i class="fas fa-file-signature"></i></div>
                    <a href="<?php echo e(route('admin.profesor.tareas.index')); ?>" class="small-box-footer">Ver tareas <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3><?php echo e($proximasClases->count()); ?></h3>
                        <p>Próximas clases</p>
                    </div>
                    <div class="icon"><i class="fa-solid fa-calendar-days"></i></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Mis próximas clases</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Curso</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $proximasClases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $clase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($clase->curso->nombre ?? 'N/A'); ?></td>
                                        <td><?php echo e($clase->fecha_hora_inicio->format('d M, Y')); ?></td>
                                        <td><?php echo e($clase->fecha_hora_inicio->format('H:i')); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No tiene clases próximas
                                            programadas.</td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Mis cursos</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Curso</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $misCursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr style="cursor:pointer" onclick="window.location='<?php echo e(route('admin.cursos.show', $curso->id)); ?>'">
                                        <td>
                                            <i class="fas fa-book text-primary mr-1"></i>
                                            <a href="<?php echo e(route('admin.cursos.show', $curso->id)); ?>"
                                               class="text-dark font-weight-bold"
                                               style="text-decoration:none">
                                                <?php echo e($curso->nombre); ?>

                                            </a>
                                        </td>
                                        <td class="text-right">
                                            <span class="badge badge-primary">Ver</span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">
                                            Aún no tiene cursos asignados.
                                        </td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="row pt-3">
        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.config.create')): ?>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo e($total_configuraciones); ?></h3>
                        <p>Configuracion</p>
                    </div>
                    <div class="icon">
                        <i class="fa-solid fa-gear"></i>
                    </div>
                    <a href="<?php echo e(route('admin.config.index')); ?>" class="small-box-footer">Mas info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.secretarias.index')): ?>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?php echo e($total_secretarias); ?></h3>
                        <p>Programador</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <a href="<?php echo e(route('admin.secretarias.index')); ?>" class="small-box-footer">Mas info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.estudiantes.index')): ?>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?php echo e($total_estudiantes); ?></h3>
                        <p>Estudiantes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users mr-2"></i>
                    </div>
                    <a href="<?php echo e(route('admin.estudiantes.index')); ?>" class="small-box-footer">Mas info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.cursos.index')): ?>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?php echo e($total_cursos); ?></h3>
                        <p>Cursos</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <a href="<?php echo e(route('admin.cursos.index')); ?>" class="small-box-footer">Mas info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.estudiantes.index')): ?>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3><?php echo e($total_profesores); ?></h3>
                        <p>Profesores</p>
                    </div>
                    <div class="icon"><i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <a href="<?php echo e(route('admin.profesores.index')); ?>" class="small-box-footer">Mas info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.horarios.index')): ?>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3><?php echo e($total_horarios); ?></h3>

                        <p>Horarios</p>
                        
                    </div>
                    <div class="icon">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <a href="<?php echo e(route('admin.horarios.index')); ?>" class="small-box-footer">Mas info <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        <?php endif; ?>
    </div>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS\resources\views/admin/index.blade.php ENDPATH**/ ?>