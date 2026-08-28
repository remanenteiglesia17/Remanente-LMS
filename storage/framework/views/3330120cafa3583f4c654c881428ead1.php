

<?php $__env->startSection('title', 'Mis Módulos'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>Módulos de mis cursos</h1>
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
                    <h3 class="card-title"><?php echo e($curso->nombre); ?></h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-secondary" data-toggle="modal"
                            data-target="#createModuloModal-<?php echo e($curso->id); ?>">
                            <i class="fas fa-plus-circle"></i> Nuevo módulo
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($curso->modulos->isEmpty()): ?>
                        <p class="text-muted p-3 mb-0">Aún no has creado módulos para este curso.</p>
                    <?php else: ?>
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Vigencia</th>
                                    <th>Tareas</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $curso->modulos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modulo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($modulo->orden); ?></td>
                                        <td><?php echo e($modulo->nombre); ?></td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modulo->fecha_inicio && $modulo->fecha_fin): ?>
                                                <?php echo e($modulo->fecha_inicio->format('d/m/Y')); ?> - <?php echo e($modulo->fecha_fin->format('d/m/Y')); ?>

                                            <?php else: ?>
                                                <span class="text-muted">Sin definir</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td><?php echo e($modulo->tareas_count); ?></td>
                                        <td>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($modulo->finalizado): ?>
                                                <span class="badge badge-success">Finalizado</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">En curso</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-info" data-toggle="modal"
                                                data-target="#editModuloModal-<?php echo e($modulo->id); ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="<?php echo e(route('admin.profesor.modulos.toggle-finalizado', $modulo->id)); ?>"
                                                method="POST" style="display:inline;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit"
                                                    class="btn btn-sm <?php echo e($modulo->finalizado ? 'btn-warning' : 'btn-success'); ?>">
                                                    <?php echo e($modulo->finalizado ? 'Reabrir' : 'Finalizar'); ?>

                                                </button>
                                            </form>
                                            <form action="<?php echo e(route('admin.profesor.modulos.destroy', $modulo->id)); ?>"
                                                method="POST" style="display:inline;"
                                                onsubmit="return confirm('¿Eliminar este módulo? Las tareas que tenga quedarán sin módulo asignado.');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="modal fade" id="createModuloModal-<?php echo e($curso->id); ?>" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Nuevo módulo — <?php echo e($curso->nombre); ?></h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <form action="<?php echo e(route('admin.profesor.modulos.store')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="modal-body">
                                <input type="hidden" name="curso_id" value="<?php echo e($curso->id); ?>">
                                <div class="form-group">
                                    <label>Nombre del módulo</label><b class="text-danger">*</b>
                                    <input type="text" name="nombre" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <textarea name="descripcion" class="form-control" rows="2"></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha inicio</label><b class="text-danger">*</b>
                                            <input type="date" name="fecha_inicio" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Fecha fin</label><b class="text-danger">*</b>
                                            <input type="date" name="fecha_fin" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <label>Ponderación por categoría</label><b class="text-danger">*</b>
                                <p class="text-muted small mb-2">
                                    Qué % de la nota de este módulo aporta cada tipo de actividad. Debe sumar 100%.
                                </p>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>Tareas</label>
                                        <div class="input-group">
                                            <input type="number" name="peso_tarea" class="form-control peso-categoria-<?php echo e($curso->id); ?>"
                                                min="0" max="100" step="0.01" value="20" required>
                                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Quizzes</label>
                                        <div class="input-group">
                                            <input type="number" name="peso_quiz" class="form-control peso-categoria-<?php echo e($curso->id); ?>"
                                                min="0" max="100" step="0.01" value="20" required>
                                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Exámenes</label>
                                        <div class="input-group">
                                            <input type="number" name="peso_examen" class="form-control peso-categoria-<?php echo e($curso->id); ?>"
                                                min="0" max="100" step="0.01" value="30" required>
                                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Proyecto</label>
                                        <div class="input-group">
                                            <input type="number" name="peso_proyecto" class="form-control peso-categoria-<?php echo e($curso->id); ?>"
                                                min="0" max="100" step="0.01" value="20" required>
                                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Foro</label>
                                        <div class="input-group">
                                            <input type="number" name="peso_foro" class="form-control peso-categoria-<?php echo e($curso->id); ?>"
                                                min="0" max="100" step="0.01" value="10" required>
                                            <div class="input-group-append"><span class="input-group-text">%</span></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <span id="peso-total-<?php echo e($curso->id); ?>" class="badge badge-secondary d-block p-2 w-100">Suma: 100%</span>
                                    </div>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['peso_categoria'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <small class="text-danger d-block mt-2"><?php echo e($message); ?></small>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Crear módulo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $curso->modulos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modulo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="modal fade" id="editModuloModal-<?php echo e($modulo->id); ?>" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar módulo — <?php echo e($modulo->nombre); ?></h5>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <form action="<?php echo e(route('admin.profesor.modulos.update', $modulo->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>Nombre del módulo</label><b class="text-danger">*</b>
                                        <input type="text" name="nombre" class="form-control" value="<?php echo e($modulo->nombre); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Descripción</label>
                                        <textarea name="descripcion" class="form-control" rows="2"><?php echo e($modulo->descripcion); ?></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Fecha inicio</label><b class="text-danger">*</b>
                                                <input type="date" name="fecha_inicio" class="form-control"
                                                    value="<?php echo e($modulo->fecha_inicio ? $modulo->fecha_inicio->format('Y-m-d') : ''); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Fecha fin</label><b class="text-danger">*</b>
                                                <input type="date" name="fecha_fin" class="form-control"
                                                    value="<?php echo e($modulo->fecha_fin ? $modulo->fecha_fin->format('Y-m-d') : ''); ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>
                                    <label>Ponderación por categoría</label><b class="text-danger">*</b>
                                    <p class="text-muted small mb-2">
                                        Qué % de la nota de este módulo aporta cada tipo de actividad. Debe sumar 100%.
                                    </p>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label>Tareas</label>
                                            <div class="input-group">
                                                <input type="number" name="peso_tarea" class="form-control peso-categoria-edit-<?php echo e($modulo->id); ?>"
                                                    min="0" max="100" step="0.01" value="<?php echo e($modulo->peso_tarea); ?>" required>
                                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Quizzes</label>
                                            <div class="input-group">
                                                <input type="number" name="peso_quiz" class="form-control peso-categoria-edit-<?php echo e($modulo->id); ?>"
                                                    min="0" max="100" step="0.01" value="<?php echo e($modulo->peso_quiz); ?>" required>
                                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Exámenes</label>
                                            <div class="input-group">
                                                <input type="number" name="peso_examen" class="form-control peso-categoria-edit-<?php echo e($modulo->id); ?>"
                                                    min="0" max="100" step="0.01" value="<?php echo e($modulo->peso_examen); ?>" required>
                                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Proyecto</label>
                                            <div class="input-group">
                                                <input type="number" name="peso_proyecto" class="form-control peso-categoria-edit-<?php echo e($modulo->id); ?>"
                                                    min="0" max="100" step="0.01" value="<?php echo e($modulo->peso_proyecto); ?>" required>
                                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Foro</label>
                                            <div class="input-group">
                                                <input type="number" name="peso_foro" class="form-control peso-categoria-edit-<?php echo e($modulo->id); ?>"
                                                    min="0" max="100" step="0.01" value="<?php echo e($modulo->peso_foro); ?>" required>
                                                <div class="input-group-append"><span class="input-group-text">%</span></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 d-flex align-items-end">
                                            <span id="peso-total-edit-<?php echo e($modulo->id); ?>" class="badge badge-secondary d-block p-2 w-100">Suma: 100%</span>
                                        </div>
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

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const inputs = document.querySelectorAll('.peso-categoria-edit-<?php echo e($modulo->id); ?>');
                        const badge = document.getElementById('peso-total-edit-<?php echo e($modulo->id); ?>');

                        function actualizarSuma() {
                            let total = 0;
                            inputs.forEach(input => total += parseFloat(input.value) || 0);
                            total = Math.round(total * 100) / 100;

                            badge.textContent = `Suma: ${total}%`;
                            badge.classList.remove('badge-secondary', 'badge-success', 'badge-danger');
                            badge.classList.add(Math.abs(total - 100) < 0.01 ? 'badge-success' : 'badge-danger');
                        }

                        inputs.forEach(input => input.addEventListener('input', actualizarSuma));
                        actualizarSuma();
                    });
                </script>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const inputs = document.querySelectorAll('.peso-categoria-<?php echo e($curso->id); ?>');
                    const badge = document.getElementById('peso-total-<?php echo e($curso->id); ?>');

                    function actualizarSuma() {
                        let total = 0;
                        inputs.forEach(input => total += parseFloat(input.value) || 0);
                        total = Math.round(total * 100) / 100;

                        badge.textContent = `Suma: ${total}%`;
                        badge.classList.remove('badge-secondary', 'badge-success', 'badge-danger');
                        badge.classList.add(Math.abs(total - 100) < 0.01 ? 'badge-success' : 'badge-danger');
                    }

                    inputs.forEach(input => input.addEventListener('input', actualizarSuma));
                    actualizarSuma();
                });
            </script>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/profesor/modulos/index.blade.php ENDPATH**/ ?>