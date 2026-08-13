<!-- Modal de Show -->
<div class="modal fade" id="showEstudianteModal" tabindex="-1" role="dialog" aria-labelledby="showEstudianteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showEstudianteModalLabel">Mostrar Estudiante</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="h2">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="nombres">Nombres </label>
                                        <p><?php echo e($estudiante->nombres); ?></p>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="apellidos">Apellidos </label>
                                        <p><?php echo e($estudiante->apellidos); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cc">CC </label>
                                        <p><?php echo e($estudiante->cc); ?></p>

                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="telefono">Teléfono </label>
                                        <p><?php echo e($estudiante->telefono); ?></p>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="genero">Sexo </label>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($estudiante->genero == 'M'): ?>
                                            'Masculino'
                                        <?php else: ?>
                                            'Femenino'
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="correo">Correo </label>
                                        <p><?php echo e($estudiante->correo); ?></p>

                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="direccion">Direccion </label>
                                        <p><?php echo e($estudiante->direccion); ?></p>

                                    </div>
                                </div> 
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="contacto_emergencia">Contacto Emergencia</label>
                                        <p><?php echo e($estudiante->contacto_emergencia); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="observaciones">Observaciones</label>
                                        <p><?php echo e($estudiante->observaciones); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/admin/estudiantes/show.blade.php ENDPATH**/ ?>