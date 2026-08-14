<!-- Modal de Show -->
<div class="modal fade" id="showModal" tabindex="-1" role="dialog" aria-labelledby="showModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showModalLabel">Crear Programador</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <h1>Programador: <?php echo e($secretaria->nombres); ?> <?php echo e($secretaria->apellidos); ?></h1>
                </div>
                <div class="row">
                    <div class="col-md-10">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="nombres">Nombres </label>
                                        <p><?php echo e($secretaria->nombres); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="apellidos">Apellidos </label>
                                        <p><?php echo e($secretaria->apellidos); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="cc">CC </label>
                                        <p><?php echo e($secretaria->cc); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="telefono">Teléfono </label>
                                        <p><?php echo e($secretaria->telefono); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="fecha_nacimiento">Fecha de Nacimiento </label>
                                        <p><?php echo e($secretaria->fecha_nacimiento); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="direccion">Direccion </label>
                                        <p><?php echo e($secretaria->direccion); ?></p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <p><?php echo e($secretaria->user->email); ?></p>
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
<?php /**PATH C:\xampp\htdocs\www\Canvas-Church60\resources\views/admin/secretarias/show.blade.php ENDPATH**/ ?>