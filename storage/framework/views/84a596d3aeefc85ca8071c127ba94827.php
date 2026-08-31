

<?php $__env->startSection('title', 'Libro de Calificaciones'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1><i class="fas fa-book"></i> Gestión de Calificaciones</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-body">
                <form action="<?php echo e(route('admin.profesor.calificaciones.index')); ?>" method="GET">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Cursos en su Horario:</label>
                            <select name="curso_id" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Seleccione un curso --</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($curso->id); ?>"
                                        <?php echo e(request('curso_id') == $curso->id ? 'selected' : ''); ?>>
                                        <?php echo e($curso->codigo); ?> - <?php echo e($curso->nombre); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cursoSeleccionado): ?>
            <div class="text-right mb-3">
                <form action="<?php echo e(route('admin.profesor.calificaciones.finalizar-curso')); ?>" method="POST"
                    onsubmit="return confirm('Esto evaluará a todos los estudiantes activos según su promedio y horas cumplidas, y cerrará el curso. ¿Continuar?');">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="curso_id" value="<?php echo e($cursoSeleccionado->id); ?>">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-flag-checkered"></i> Finalizar curso
                    </button>
                </form>
            </div>

            <div class="card shadow">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Planilla: <?php echo e($cursoSeleccionado->nombre); ?></h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped m-0" id="tabla-notas">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50" class="text-center">#</th>
                                    <th>Estudiante</th>
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tareasDelCurso; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tarea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th class="text-center">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tarea->modulo): ?>
                                                <span class="badge badge-secondary d-block mb-1" style="font-size:10px">
                                                    <i class="fas fa-layer-group"></i> <?php echo e($tarea->modulo->nombre); ?>

                                                </span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php echo e($tarea->titulo_tarea); ?> <br>
                                            <small class="badge badge-info"><?php echo e(number_format($tarea->puntaje, 1)); ?> pts</small>
                                        </th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <th class="text-center bg-gray-light">FINAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $estudiantes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $estudiante): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td class="text-center align-middle"><?php echo e($index + 1); ?></td>
                                        <td class="align-middle">
                                            <strong><?php echo e($estudiante->user->name); ?></strong>
                                        </td>

                                        
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tareasDelCurso; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tarea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <td class="text-center p-1" style="min-width: 130px;">
                                                <?php
                                                    $entrega = $estudiante->entregas->firstWhere('tarea_id', $tarea->id);
                                                    $calificacion = $estudiante->calificaciones->where('concepto', $tarea->titulo_tarea)->first();
                                                ?>

                                                
                                                <input type="number"
                                                    name="notas[<?php echo e($estudiante->id); ?>][<?php echo e($tarea->id); ?>]"
                                                    class="form-control form-control-sm text-center input-nota mb-1"
                                                    step="0.1" min="0" max="5"
                                                    value="<?php echo e($calificacion->nota ?? ''); ?>"
                                                    placeholder="0.0 – 5.0"
                                                    data-peso="<?php echo e($tarea->puntaje); ?>" data-max="5">

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entrega): ?>
                                                    
                                                    <a href="<?php echo e(route('admin.profesor.calificaciones.revision', $entrega->id)); ?>"
                                                        class="btn btn-xs btn-primary btn-block shadow-sm">
                                                        <i class="fas fa-file-alt"></i> Ver Entrega
                                                    </a>
                                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($entrega->entrega_tardia): ?>
                                                        <small class="text-danger" style="font-size: 0.6rem;">
                                                            <i class="fas fa-clock"></i> Entregada tarde
                                                        </small>
                                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary disabled"
                                                        style="font-size: 0.65rem; opacity: 0.6;">
                                                        Sin actividad
                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <?php
                                            $sumaNotasBD = 0;
                                            $conteoNotasBD = 0;

                                            foreach ($tareasDelCurso as $t) {
                                                $cal = $estudiante->calificaciones->where('concepto', $t->titulo_tarea)->first();
                                                if ($cal && $cal->nota !== null && $cal->nota !== '') {
                                                    $sumaNotasBD += (float)$cal->nota;
                                                    $conteoNotasBD++;
                                                }
                                            }

                                            $notaFinalBD = $conteoNotasBD > 0 ? ($sumaNotasBD / $conteoNotasBD) : 0;
                                            $aprobadoBD = $conteoNotasBD > 0 && $notaFinalBD >= 3.0;
                                        ?>
                                        
                                        
                                        <td class="text-center align-middle" style="min-width:90px">
                                            <span class="badge nota-final <?php echo e($conteoNotasBD > 0 ? ($aprobadoBD ? 'badge-success' : 'badge-danger') : 'badge-secondary'); ?> d-block mb-1"
                                                id="final-<?php echo e($estudiante->id); ?>"
                                                style="font-size:14px;padding:5px 8px">
                                                <?php echo e($conteoNotasBD > 0 ? number_format($notaFinalBD, 2) : '—'); ?>

                                            </span>
                                            <span class="estado-final" id="estado-<?php echo e($estudiante->id); ?>">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($conteoNotasBD > 0 && $aprobadoBD): ?>
                                                    <span class="badge badge-success" style="font-size:11px">
                                                        <i class="fas fa-check-circle"></i> Aprobado
                                                    </span>
                                                <?php elseif($conteoNotasBD > 0): ?>
                                                    <span class="badge badge-danger" style="font-size:11px">
                                                        <i class="fas fa-times-circle"></i> Reprobado
                                                    </span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="<?php echo e(count($tareasDelCurso) + 2); ?>" class="text-center">No hay estudiantes.</td>
                                    </tr>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-success float-right" onclick="guardarTodo()">
                        <i class="fas fa-save"></i> Guardar Planilla
                    </button>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        $(document).ready(function() {
            calcularFinales();
            $('.input-nota').on('input', calcularFinales);
        });

        function calcularFinales() {
            $('#tabla-notas tbody tr').each(function() {
                let row = $(this);
                let sumaNotas = 0;
                let conteoNotas = 0;

                row.find('.input-nota').each(function() {
                    let val = $(this).val();
                    if (val !== '' && !isNaN(val)) {
                        sumaNotas += parseFloat(val);
                        conteoNotas++;
                    }
                });

                let badge = row.find('.nota-final');
                let estadoSpan = row.find('.estado-final');

                if (conteoNotas === 0) {
                    badge.text('—').removeClass('badge-success badge-danger').addClass('badge-secondary');
                    estadoSpan.html('');
                    return;
                }

                let notaFinal = sumaNotas / conteoNotas;
                badge.text(notaFinal.toFixed(2));

                if (notaFinal >= 3.0) {
                    badge.removeClass('badge-danger badge-secondary').addClass('badge-success');
                    estadoSpan.html('<span class="badge badge-success" style="font-size:11px"><i class="fas fa-check-circle"></i> Aprobado</span>');
                } else {
                    badge.removeClass('badge-success badge-secondary').addClass('badge-danger');
                    estadoSpan.html('<span class="badge badge-danger" style="font-size:11px"><i class="fas fa-times-circle"></i> Reprobado</span>');
                }
            });
        }

        function guardarTodo() {
            const cursoId = <?php echo e($cursoSeleccionado->id ?? 'null'); ?>;
            if (!cursoId) {
                Swal.fire('Atención', 'Selecciona un curso primero.', 'warning');
                return;
            }

            const notas = {};
            let totalCapturadas = 0;

            $('#tabla-notas tbody tr').each(function() {
                $(this).find('.input-nota').each(function() {
                    const valor = $(this).val();
                    if (valor === '' || valor === null) {
                        return;
                    }

                    const nombre = $(this).attr('name');
                    const match = nombre.match(/notas\[(\d+)\]\[(\d+)\]/);
                    if (!match) {
                        return;
                    }

                    const estudianteId = match[1];
                    const tareaId = match[2];

                    if (!notas[estudianteId]) {
                        notas[estudianteId] = {};
                    }
                    notas[estudianteId][tareaId] = valor;
                    totalCapturadas++;
                });
            });

            if (totalCapturadas === 0) {
                Swal.fire('Atención', 'No hay notas capturadas para guardar.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Guardando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: '<?php echo e(route("admin.profesor.calificaciones.planilla")); ?>',
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    curso_id: cursoId,
                    notas: notas
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Notas guardadas!',
                        text: `Se guardaron ${response.guardadas} nota(s)` +
                            (response.omitidas > 0 ? ` (${response.omitidas} omitida(s) por estar fuera de rango).` : '.')
                    });
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Ocurrió un error al guardar las notas.';
                    Swal.fire('Error', msg, 'error');
                }
            });
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/profesor/calificaciones/index.blade.php ENDPATH**/ ?>