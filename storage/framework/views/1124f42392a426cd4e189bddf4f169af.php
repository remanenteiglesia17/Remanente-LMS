

<?php $__env->startSection('title', ucfirst(auth()->user()->getRoleNames()->first())); ?>
<?php $__env->startSection('css'); ?>
<style>
    .curso-ellipsis {
        max-width: 120px;
        /* ajusta según ancho de la columna */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        vertical-align: middle;
    }
</style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content_header'); ?>
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">Registro de un nuevo horario</h1>

    <a href="<?php echo e(route('admin.home')); ?>" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Volver
    </a>
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Llene los Datos</h3>
        </div>
        <div class="card-body">
            
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.horarios.crear_nuevos')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <strong>Permisos completos:</strong>
                Puede crear nuevos horarios libremente y modificar existentes.
            </div>
            <?php else: ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.horarios.agendar_dia_libre')): ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.horarios.modificar_existentes')): ?>
            <div class="alert alert-info">
                <i class="fas fa-calendar-check"></i> <strong>Permisos mixtos:</strong>
                Puede modificar horarios existentes y agendar en días sin horarios previos.
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-calendar-plus"></i> <strong>Solo días libres:</strong>
                Solo puede agendar en días donde el profesor NO tiene horarios configurados.
            </div>
            <?php endif; ?>
            <?php else: ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.horarios.modificar_existentes')): ?>
            <div class="alert alert-warning">
                <i class="fas fa-edit"></i> <strong>Solo modificar:</strong>
                Solo puede editar horarios ya existentes. Seleccione un profesor para ver sus horarios.
            </div>
            <?php else: ?>
            <div class="alert alert-danger">
                <i class="fas fa-ban"></i> <strong>Sin permisos:</strong>
                No tiene permisos para gestionar horarios.
            </div>
            <?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-2">
                    <form action="<?php echo e(route('admin.horarios.store')); ?>" method="POST" autocomplete="off">
                        <?php echo csrf_field(); ?>

                        <div class="form-group">
                            <label for="profesor_id">Profesores </label><b class="text-danger">*</b>
                            <select class="form-control" name="profesor_id" id="profesor_id" required>
                                <option value="" selected disabled>Seleccione</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $profesores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $profesor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($profesor->id); ?>">
                                    <?php echo e($profesor->nombres . ' ' . $profesor->apellidos); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['profesor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="bg-danger text-white p-1"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.horarios.modificar_existentes')): ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->denies('admin.horarios.crear_nuevos')): ?>
                        <div class="form-group" id="horario_existente_group" style="display: none;">
                            <label for="horario_existente">Horario a modificar</label><b class="text-danger">*</b>
                            <select class="form-control" id="horario_existente">
                                <option value="" selected>Primero seleccione un profesor</option>
                            </select>
                            <small class="text-muted">Seleccione el horario que desea modificar</small>
                        </div>
                        <?php endif; ?>
                        <?php endif; ?>

                        
                        
                        <div class="form-group">
                            <label for="curso_id">Curso</label><b class="text-danger">*</b>
                            <select name="cursos[]" id="curso_select" class="form-control" required>
                                <option value="" disabled selected>Seleccione</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($curso->id); ?>"><?php echo e($curso->nombre); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['cursos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="bg-danger text-white p-1"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="dia">Día </label><b class="text-danger">*</b>
                            <select class="form-control" name="dia" id="dia" required>
                                <option value="" selected disabled>Seleccione</option>
                                <option value="LUNES">LUNES</option>
                                <option value="MARTES">MARTES</option>
                                <option value="MIERCOLES">MIÉRCOLES</option>
                                <option value="JUEVES">JUEVES</option>
                                <option value="VIERNES">VIERNES</option>
                                <option value="SABADO">SÁBADO</option>
                                <option value="DOMINGO">DOMINGO</option>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['dia'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="bg-danger text-white p-1"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="hora_inicio">Hora Inicio </label><b class="text-danger">*</b>
                            <input type="time" class="form-control" name="hora_inicio" id="hora_inicio" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['hora_inicio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="bg-danger text-white p-1"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="hora_fin">Hora Final </label><b class="text-danger">*</b>
                            <input type="time" class="form-control" name="hora_fin" id="hora_fin" required>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['hora_fin'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <small class="bg-danger text-white p-1"><?php echo e($message); ?></small>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.horarios.crear_nuevos')): ?>
                                <i class="fas fa-save"></i>
                                <?php else: ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.horarios.modificar_existentes')): ?>
                                Actualizar horario
                                <?php else: ?>
                                Agendar
                                <?php endif; ?>
                                <?php endif; ?>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-10">
                    <hr>
                    <div id="curso_info"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.horarios.modificar_existentes')): ?>
<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->denies('admin.horarios.crear_nuevos')): ?>
<script>
    const horariosExistentes = <?php echo json_encode($horariosExistentes ?? [], 15, 512) ?>;
    const puedeModificarExistentes = true;
    console.log('Horarios existentes:', horariosExistentes);
</script>
<?php endif; ?>
<?php endif; ?>

<script>
$('#profesor_id').on('change', function() {
    var profesor_id = $(this).val();
    if (!profesor_id) return;

    var url = "<?php echo e(route('admin.horarios.show_datos_cursos', ':id')); ?>";
    url = url.replace(':id', profesor_id);

    $.get(url, function(response) {
        // 1. Actualizar la tabla de información inferior
        $('#curso_info').html(response.html_tabla);

        // 2. Referencia al select de cursos
        var select = $('#curso_select');
        select.empty();

        if (response.tiene_curso) {
            // Caso: El profesor YA tiene un curso asignado
            // Añadimos el curso asignado como la única opción y la seleccionamos
            select.append(`<option value="${response.curso_asignado.id}" selected>${response.curso_asignado.nombre}</option>`);
            
            // Bloqueamos la interacción pero NO usamos .prop('disabled', true) 
            // para que el valor viaje en el formulario al hacer el submit.
            select.css({
                'pointer-events': 'none',
                'background-color': '#e9ecef', // Color grisáceo de campo bloqueado
                'cursor': 'not-allowed'
            });

            console.log("Curso fijado automáticamente: " + response.curso_asignado.nombre);
        } 
        else {
            // Caso: El profesor NO tiene curso, permitimos elegir de la lista
            select.append('<option value="" disabled selected>Seleccione un curso</option>');
            
            // Restauramos estilo normal
            select.css({
                'pointer-events': 'auto',
                'background-color': '#ffffff',
                'cursor': 'default'
            });

            response.cursos.forEach(function(curso) {
                select.append(`<option value="${curso.id}">${curso.nombre}</option>`);
            });
        }
        
        // Gracias a que no refrescamos la página, el cursor no saltará el campo IVA
        // si el usuario sigue su flujo normal de tabulación o clic.
    }).fail(function() {
        alert('Error al obtener los datos del profesor');
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const horaInicio = document.getElementById('hora_inicio');
        const horaFin = document.getElementById('hora_fin');

        horaInicio.addEventListener('change', function() {
            let selectedTime = this.value;

            if (selectedTime) {
                selectedTime = selectedTime.split(':');
                selectedTime = selectedTime[0] + ':00';
                this.value = selectedTime;
            }

            if (selectedTime < '06:00' || selectedTime > '20:00') {
                this.value = null;
                Swal.fire({
                    title: "No fue posible",
                    text: "Por favor seleccione una hora entre 06:00 am y 8:00 pm",
                    icon: "info"
                });
            }
        });

        horaFin.addEventListener('change', function() {
            let selectedTime = this.value;

            selectedTime = selectedTime.split(':')[0] + ':00';
            this.value = selectedTime;

            if (selectedTime < '06:00' || selectedTime > '20:00') {
                this.value = null;
                Swal.fire({
                    title: "No fue posible",
                    text: "Por favor seleccione una hora entre 06:00 am y 8:00 pm",
                    icon: "info"
                });
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS\resources\views/admin/horarios/create.blade.php ENDPATH**/ ?>