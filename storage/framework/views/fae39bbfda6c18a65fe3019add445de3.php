<?php $__env->startSection('title', 'Nueva Inscripción'); ?>

<?php $__env->startSection('content_header'); ?>
<h1>Inscribir Estudiante a Curso</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-plus"></i> Inscripción Individual</h3>
                </div>
                <form action="<?php echo e(route('admin.inscripciones.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Estudiante <span class="text-danger">*</span></label>
                            <select name="estudiante_id" class="form-control select2" required>
                                <option value="">-- Seleccione un estudiante --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $estudiantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estudiante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($estudiante->id); ?>"><?php echo e($estudiante->nombres); ?> <?php echo e($estudiante->apellidos); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Curso <span class="text-danger">*</span></label>
                            <select name="curso_id" class="form-control select2 select-curso" required>
                                <option value="">-- Seleccione un curso --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($curso->id); ?>"><?php echo e($curso->nombre); ?> (<?php echo e($curso->codigo); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Profesor <span class="text-danger">*</span></label>
                            <select name="profesor_id" class="form-control select2 select-profesor" required disabled>
                                <option value="">-- Seleccione primero un curso --</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.acciones.insMasiva')): ?>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Inscripción Masiva
                    </h3>
                </div>
                <form action="<?php echo e(route('admin.inscripciones.store-multiple')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="card-body">

                        
                        <div class="form-group">
                            <label for="curso_id_multiple">Curso <span class="text-danger">*</span></label>
                            <select name="curso_id"
                                id="curso_id_multiple"
                                class="form-control select2"
                                required>
                                <option value="">-- Seleccione un curso --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($curso->id); ?>">
                                    <?php echo e($curso->codigo); ?> - <?php echo e($curso->nombre); ?> (<?php echo e($curso->periodo); ?>)
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>

                        
                        <div class="form-group">
                            <label for="estudiantes">Estudiantes <span class="text-danger">*</span></label>
                            <select name="estudiantes[]"
                                id="estudiantes"
                                class="form-control select2"
                                multiple
                                required
                                style="width: 100%;">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $estudiantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $estudiante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($estudiante->id); ?>">
                                    <?php echo e($estudiante->nombres); ?> <?php echo e($estudiante->apellidos); ?> - <?php echo e($estudiante->cc); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <small class="text-muted">Mantén presionado Ctrl (Windows) o Cmd (Mac) para seleccionar múltiples estudiantes</small>
                        </div>
                        <div class="form-group">
                            <label>Profesor <span class="text-danger">*</span></label>
                            <select name="profesor_id" class="form-control select2 select-profesor" required disabled>
                                <option value="">-- Seleccione primero un curso --</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-users-cog"></i> Inscribir Seleccionados
                        </button>
                        <a href="<?php echo e(route('admin.inscripciones.index')); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Inicialización de Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // 1. Detectar cambio en el curso (usamos una clase común)
    // Asegúrate de que los select de curso tengan la clase 'select-curso'
    $(document).on('change', '.select-curso', function() {
        let cursoId = $(this).val();
        let $card = $(this).closest('.card-body');
        let $selectProfesor = $card.find('.select-profesor');

        if (!cursoId) {
            $selectProfesor.prop('disabled', true).html('<option value="">-- Seleccione curso primero --</option>');
            return;
        }

        // Estado de carga
        $selectProfesor.prop('disabled', true).html('<option>Cargando...</option>');

        // Petición AJAX
        $.get("<?php echo e(route('admin.inscripciones.get_profesores', '')); ?>/" + cursoId, function(data) {
            $selectProfesor.empty().append('<option value="">-- Seleccione Profesor --</option>');

            if (data && data.length > 0) {
                data.forEach(prof => {
                    $selectProfesor.append(`<option value="${prof.id}">${prof.nombres} ${prof.apellidos}</option>`);
                });
                $selectProfesor.prop('disabled', false);
            } else {
                $selectProfesor.append('<option value="">No hay profesores con clases en este curso</option>');
            }

            // IMPORTANTE: Refrescar Select2 para que muestre los nuevos <option>
            $selectProfesor.trigger('change'); 
        }).fail(function() {
            $selectProfesor.html('<option value="">Error al cargar</option>');
        });
    });

    // 2. Habilitar campos antes de enviar
    $('form').on('submit', function(e) {
        let $profesor = $(this).find('.select-profesor');
        
        if ($profesor.is(':disabled')) {
            $profesor.prop('disabled', false);
        }

        if (!$profesor.val() && $(this).find('.select-curso').val()) {
            e.preventDefault();
            alert('Debe seleccionar un profesor para continuar.');
        }
    });
});
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/admin/inscripciones/create.blade.php ENDPATH**/ ?>