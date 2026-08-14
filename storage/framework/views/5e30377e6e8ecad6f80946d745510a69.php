

<?php $__env->startSection('title', ucfirst(auth()->user()->getRoleNames()->first())); ?>
<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content_header'); ?>
    
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <h1>Listado de programadores</h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Usuarios registrados</h3>
                    <div class="card-tools">
                        <a class="btn btn-secondary" data-toggle="modal" data-target="#createModal">Registrar
                            <i class="bi bi-plus-circle-fill"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('info')): ?>
                        <div class="alert alert-success"><strong><?php echo e(session('info')); ?></strong></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <table id="secretarias" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nro</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Fecha de Nacimiento</th>
                                <th>Direccion</th>
                                <th>Email</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $contador = 1; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $secretarias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $secretaria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td scope="row"><?php echo e($contador++); ?></td>
                                    <td scope="row"><?php echo e($secretaria->nombres); ?></td>
                                    <td scope="row"><?php echo e($secretaria->apellidos); ?></td>
                                    <td scope="row"><?php echo e($secretaria->cc); ?></td>
                                    <td scope="row"><?php echo e($secretaria->telefono); ?></td>
                                    <td scope="row"><?php echo e($secretaria->fecha_nacimiento); ?></td>
                                    <td scope="row"><?php echo e($secretaria->direccion); ?></td>
                                    <td scope="row"><?php echo e($secretaria->user->email); ?></td>
                                    <td scope="row">
                                        <div class="btn-group" role="group" aria-label="basic example">
                                            
                                            <a href="#" class="btn btn-primary" data-id="<?php echo e($secretaria->id); ?>"
                                                data-toggle="modal" data-target="#showModal"> <i class="fas fa-eye"></i></a>
                                            
                                            <a href="#" class="btn btn-warning btn-sm mr-1"
                                                data-id="<?php echo e($secretaria->id); ?>" data-toggle="modal"
                                                data-target="#editModal" title="Editar"> <i class="fas fa-edit"></i></a>

                                            <form id="delete-form-<?php echo e($secretaria->id); ?>"
                                                action="<?php echo e(route('admin.secretarias.destroy', $secretaria->id)); ?>"
                                                method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="button" class="btn btn-danger btn-delete"
                                                    data-id="<?php echo e($secretaria->id); ?>"
                                                    data-text="¿Estás seguro de que deseas eliminar este programador?">
                                                    <i class="fas fa-trash"></i>
                                            </form>

                                            <div class="text-center">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()->hasRole('root')): ?>
                                                    <form id="delete-form-<?php echo e($secretaria->id); ?>"
                                                        action="<?php echo e(route('admin.secretarias.toggleStatus', $secretaria->user->id)); ?>"
                                                        method="POST">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('PATCH'); ?>
                                                        <!-- Laravel permite cambios parciales con PATCH -->
                                                        <button type="submit"
                                                            class="btn <?php echo e($secretaria->user->status ? 'btn-success' : 'btn-danger'); ?>">
                                                            <?php echo $secretaria->user->status
                                                                ? '<i class="fa-solid fa-square-check"></i>'
                                                                : '<i class="fa-solid fa-circle-xmark"></i>'; ?>

                                                        </button>
                                                    </form>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('admin.secretarias.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php echo $__env->make('admin.secretarias.edit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php echo $__env->make('admin.secretarias.show', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    
    <script>
        new DataTable('#secretarias', {
            responsive: true,
            scrollX: true,
            autoWidth: false, //no le vi la funcionalidad
            dom: 'Bfrtip', // Añade el contenedor de botones
            buttons: [{
                extend: 'collection',
                text: 'Reportes',
                orientation: 'landscape',
                buttons: [{
                        extend: 'copyHtml5',
                        text: '<i class="bi bi-clipboard-check"></i> Copiar'
                    }, // Added btn-sm for better consistency
                    {
                        extend: 'csvHtml5',
                        text: '<i class="bi bi-filetype-csv"></i> CSV'
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-earmark-excel"></i> Excel'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="bi bi-filetype-pdf"></i> PDF'
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Imprimir'
                    },
                    {
                        extend: 'colvis'
                    }
                ],
            }, ],
            "language": {
                "decimal": "",
                "emptyTable": "No hay datos disponibles en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ secretarias",
                "infoEmpty": "Mostrando 0 a 0 de 0 secretarias",
                "infoFiltered": "(filtrado de _MAX_ secretarias totales)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ secretarias",
                "loadingRecords": "Cargando...",
                "processing": "",
                "search": "Buscar:",
                "zeroRecords": "No se encontraron registros coincidentes",
                "paginate": {
                    "first": "Primero",
                    "last": "Último",
                    "next": "Siguiente",
                    "previous": "Anterior"
                },
                "aria": {
                    "orderable": "Ordenar por esta columna",
                    "orderableReverse": "Invertir el orden de esta columna"
                }
            },
            initComplete: function() {
                // Apply custom styles after initialization
                $('.dt-button').css({
                    'background-color': '#4a4a4a',
                    'color': 'white',
                    'border': 'none',
                    'border-radius': '4px',
                    'padding': '8px 12px',
                    'margin': '0 5px',
                    'font-size': '14px'
                });
            },
        });
    </script>
    <!-- EDIT MODAL -->
    <script>
        $('#editModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var modal = $(this);

            var url = "<?php echo e(route('admin.secretarias.edit', ':id')); ?>".replace(':id', id);

            $.ajax({
                url: url,
                method: 'GET',
                cache: false,
                success: function(data) {
                    // Cambiar la acción del form
                    var formAction = "<?php echo e(route('admin.secretarias.update', ':id')); ?>".replace(':id',
                        data.id);
                    modal.find('#editForm').attr('action', formAction);

                    // Llenar los campos
                    modal.find('#edit-nombres').val(data.nombres);
                    modal.find('#edit-apellidos').val(data.apellidos);
                    modal.find('#edit-cc').val(data.cc);
                    modal.find('#edit-telefono').val(data.telefono);
                    modal.find('#edit-direccion').val(data.direccion);
                    modal.find('#edit-email').val(data.user.email);
                    // 👇 convertir fecha de DD/MM/YYYY → YYYY-MM-DD
                    if (data.fecha_nacimiento) {
                        let partes = data.fecha_nacimiento.split('/');
                        if (partes.length === 3) {
                            let fechaISO =
                                `${partes[2]}-${partes[1].padStart(2, '0')}-${partes[0].padStart(2, '0')}`;
                            modal.find('#edit-fecha_nacimiento').val(fechaISO);
                        }
                    }

                },
                error: function(xhr) {
                    console.error('Error al cargar los datos deel programador:', xhr);
                }
            });
        });
    </script>
    
    <script>
        $('#createForm').on('submit', function(e) {
            e.preventDefault(); // evita el cierre automático

            let form = $(this);
            let actionUrl = form.attr('action');

            $.ajax({
                url: actionUrl,
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    toastr.success("Registro exitoso");
                    $('#createModal').modal('hide'); // aquí sí cierras manualmente si quieres
                    form[0].reset(); // limpiar formulario
                    location.reload(); // refresca la vista y la tabla se repuebla desde Blade
                    // $('#secretarias').DataTable().ajax.reload(); // refrescar tabla sin recargar la página
                },
                error: function(xhr) {
                    if (xhr.status === 422) { // errores de validación Laravel
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, messages) {
                            toastr.error(messages[0]);
                        });
                    } else {
                        toastr.error("Ocurrió un error inesperado");
                    }
                }
            });
        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Canvas-Church60\resources\views/admin/secretarias/index.blade.php ENDPATH**/ ?>