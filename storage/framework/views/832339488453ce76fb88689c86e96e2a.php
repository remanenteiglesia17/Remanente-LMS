<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover table-sm">
        <thead>
            <tr>
                <th scope="col">Hora</th>
                <?php
                    $diasSemana = ['LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO', 'DOMINGO'];
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $diasSemana; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dia): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <th scope="col"><?php echo e($dia); ?></th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
                $horas = [
                    '06:00 am - 07:00 am',
                    '07:00 am - 08:00 am',
                    '08:00 am - 09:00 am',
                    '09:00 am - 10:00 am',
                    '10:00 am - 11:00 am',
                    '11:00 am - 12:00 pm', 
                    '01:00 pm - 02:00 pm',
                    '02:00 pm - 03:00 pm',
                    '03:00 pm - 04:00 pm',
                    '04:00 pm - 05:00 pm',
                    '05:00 pm - 06:00 pm',
                    '06:00 pm - 07:00 pm',
                    '07:00 pm - 08:00 pm',
                ];
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $horas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hora): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    [$hora_inicio, $hora_fin] = explode(' - ', $hora);
                    $hora_inicio_24 = date('H:i', strtotime($hora_inicio));
                    $hora_fin_24 = date('H:i', strtotime($hora_fin));
                ?>
                <tr>
                    <td scope="row" class="fw-bold"><?php echo e($hora); ?></td>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $diasSemana; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dia): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $curso_mostrado = '';
                            $agendado = false;
                            $es_del_usuario = false;

                            // 🔹 Verifica si hay curso disponible en este bloque (solapamiento flexible)
                            foreach ($horarios as $horario) {
                                $horario_inicio_24 = date('H:i', strtotime($horario->hora_inicio));
                                $horario_fin_24 = date('H:i', strtotime($horario->hora_fin));

                                if (
                                    strtoupper($horario->dia) == $dia &&
                                    $hora_inicio_24 < $horario_fin_24 && // Se cruza con el bloque actual
                                    $hora_fin_24 > $horario_inicio_24
                                ) {
                                    // Muestra todos los cursos disponibles
                                    $curso_mostrado = $horario->cursos->pluck('nombre')->join(', ');
                                    break;
                                }
                            }

                            // 🔹 Verifica si hay curso agendado (resaltado de color)
                            foreach ($horarios_asignados as $horario_asignado) {
                                $asignado_inicio_24 = date('H:i', strtotime($horario_asignado->hora_inicio));
                                $asignado_fin_24 = date('H:i', strtotime($horario_asignado->hora_fin));
                                $asignado_dia = strtoupper($horario_asignado->dia);

                                if (
                                    $asignado_dia == $dia &&
                                    $hora_inicio_24 < $asignado_fin_24 &&
                                    $hora_fin_24 > $asignado_inicio_24
                                ) {
                                    $agendado = true;
                                    $es_del_usuario = auth()->user()->id == $horario_asignado->user_id;
                                    $curso_mostrado = $horario_asignado->curso_nombre ?? $curso_mostrado;
                                    break;
                                }
                            }

                            // 🔹 Define el color de la celda
                            $clase = '';
                             $contenido = $curso_mostrado;
                            if ($agendado) {
                                $clase = $es_del_usuario ? 'table-success' : 'table-primary';
                            } elseif ($curso_mostrado) {
                                // Badge para horarios disponibles (no agendados)
                                $contenido = '
                                    <span class="badge badge-pill badge-light border border-dark">
                                        <span class="curso-ellipsis" title="' . e($curso_mostrado) . '">
                                            ' . e($curso_mostrado) . '
                                        </span>
                                    </span>';
                            }
                        ?>

                        <td class="<?php echo e($clase); ?>  text-center"><?php echo $contenido; ?></td>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>
</div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->superAdmin): ?>
    <div class="mt-3">
    <div class="alert alert-primary d-inline-block me-2 p-2"><strong>Azul:</strong> Ocupado (otro estudiante)</div>
    <div class="alert alert-success d-inline-block me-2 p-2"><strong>Verde:</strong> agendado por ti</div>
    <div class="alert alert-light border border-dark text-center d-inline-block me-2 p-2"><strong>Blanco:</strong> Disponible</div>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/admin/horarios/show_datos_cursos.blade.php ENDPATH**/ ?>