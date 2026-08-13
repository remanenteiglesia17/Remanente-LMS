<!-- Modal de visualización -->
<div class="modal fade" id="configModal" tabindex="-1" role="dialog"
    aria-labelledby="configModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="configModalLabel">
                    Configuración
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">

                    <!-- Logo -->
                    <div class="col-md-6">
                        <div class="form-group text-center">
                            <img
                                src="<?php echo e(isset($config->logo)
                                    ? asset('storage/' . $config->logo)
                                    : 'https://cdn.pixabay.com/photo/2020/03/27/13/02/venice-4973502_1280.jpg'); ?>"
                                alt="Logo"
                                style="max-width: 90%; height: auto;">
                        </div>
                    </div>

                    <!-- Información -->
                    <div class="col-md-6">

                        <div class="form-group">
                            <label>Nombre del sitio</label>
                            <p class="form-control">
                                <?php echo e($config->site_name ?? 'No configurado'); ?>

                            </p>
                        </div>

                        <div class="form-group">
                            <label>Dirección</label>
                            <p class="form-control">
                                <?php echo e($config->address ?? 'No configurada'); ?>

                            </p>
                        </div>

                        <div class="form-group">
                            <label>Teléfono</label>
                            <p class="form-control">
                                <?php echo e($config->phone ?? 'No configurado'); ?>

                            </p>
                        </div>

                        <div class="form-group">
                            <label>Correo de contacto</label>
                            <p class="form-control">
                                <?php echo e($config->email_contact ?? 'No configurado'); ?>

                            </p>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div><?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/admin/config/show.blade.php ENDPATH**/ ?>