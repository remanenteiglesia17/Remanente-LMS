
<div class="modal fade" id="modalEstado<?php echo e($inscripcion->id); ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.inscripciones.cambiar-estado', $inscripcion->id)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>

                <div class="modal-header bg-info">
                    <h5 class="modal-title">Cambiar Estado de Inscripción</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <p><strong>Estudiante:</strong> <?php echo e($inscripcion->nombres); ?> <?php echo e($inscripcion->apellidos); ?></p>
                    <p><strong>Curso:</strong> <?php echo e($inscripcion->curso_nombre); ?></p>

                    <hr>

                    <div class="form-group">
                        <label for="estado<?php echo e($inscripcion->id); ?>">Nuevo Estado</label>
                        <select name="estado" id="estado<?php echo e($inscripcion->id); ?>" class="form-control" required>
                            <option value="activo" <?php echo e($inscripcion->estado == 'activo' ? 'selected' : ''); ?>>Activo
                            </option>
                            <option value="retirado" <?php echo e($inscripcion->estado == 'retirado' ? 'selected' : ''); ?>>Retirado
                            </option>
                            <option value="aprobado" <?php echo e($inscripcion->estado == 'aprobado' ? 'selected' : ''); ?>>Aprobado
                            </option>
                            <option value="reprobado" <?php echo e($inscripcion->estado == 'reprobado' ? 'selected' : ''); ?>>
                                Reprobado</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/admin/inscripciones/partials/modal-estado.blade.php ENDPATH**/ ?>