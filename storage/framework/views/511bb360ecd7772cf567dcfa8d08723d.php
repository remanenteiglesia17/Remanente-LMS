

<?php $__env->startSection('title', ucfirst(auth()->user()->getRoleNames()->first())); ?>

<?php $__env->startSection('content_header'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row pt-3">
        <div class="col-12">
            <div class="card card-outline card-primary ">
                <div class="card-header">
                    <h3 class="card-title"><?php echo e(__('Permissions')); ?></h3>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permissions.create')): ?>
                        <div class="card-tools">
                            
                            <a class="btn btn-secondary" data-toggle="modal" data-target="#createModal">
                                Registrar<i class="bi bi-plus-circle-fill"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (isset($component)) { $__componentOriginal88b0e6813f5b80100a19437aa80e29ba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal88b0e6813f5b80100a19437aa80e29ba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.message','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal88b0e6813f5b80100a19437aa80e29ba)): ?>
<?php $attributes = $__attributesOriginal88b0e6813f5b80100a19437aa80e29ba; ?>
<?php unset($__attributesOriginal88b0e6813f5b80100a19437aa80e29ba); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal88b0e6813f5b80100a19437aa80e29ba)): ?>
<?php $component = $__componentOriginal88b0e6813f5b80100a19437aa80e29ba; ?>
<?php unset($__componentOriginal88b0e6813f5b80100a19437aa80e29ba); ?>
<?php endif; ?>

                <div class="card-body">
                    <table id="permissions" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Creación</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($permission->id); ?></td>
                                    <td><?php echo e($permission->name); ?></td>
                                    <td><?php echo e($permission->created_at->format('d M, Y')); ?></td>
                                    <td class="text-center">
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permissions.edit')): ?>
                                            
                                            <a href="#" class="btn btn-warning btn-sm mr-1"
                                                data-id="<?php echo e($permission->id); ?>" data-toggle="modal" data-target="#editModal"
                                                title="Editar"> <i class="fas fa-edit"></i></a>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('permissions.delete')): ?>
                                            <form id="delete-form-<?php echo e($permission->id); ?>"
                                                action="<?php echo e(route('admin.permissions.destroy', $permission->id)); ?>"
                                                method="POST" style="display:inline;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="button" class="btn btn-danger btn-delete"
                                                    data-id="<?php echo e($permission->id); ?>"
                                                    data-text="¿Estás seguro de que deseas eliminar este permiso?">
                                                    <i class="fas fa-trash"></i>
                                        </form>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center">No hay permisos registrados.</td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                    
                    <?php echo $__env->make('admin.permissions.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php echo $__env->make('admin.permissions.edit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        $('#editModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // button that opened the modal
            var id = button.data('id'); // permission ID
            var modal = $(this);

            // Construct the URL for fetching the permission data
            var url = "<?php echo e(route('admin.permissions.edit', ':id')); ?>".replace(':id', id);

            $.ajax({
                url: url,
                method: 'GET',
                cache: false,
                success: function(data) {
                    // Set the form action URL for update
                    var formAction = "<?php echo e(route('admin.permissions.update', ':id')); ?>".replace(':id', data.permission.id);
                    modal.find('#editForm').attr('action', formAction);
                    modal.find('#edit-name').val(data.permission.name); // Populate the name field
                },
                error: function(xhr) {  // You might want to show a SweetAlert error here
                    console.error('Error al cargar los datos del permiso:', xhr);
                    Swal.fire('Error', 'No se pudieron cargar los datos del permiso.', 'error');
                }
            });
        });

        // --- DataTables Initialization Logic (The Fix) ---
        $(document).ready(function() {
            // **FIXED: Using the jQuery plugin method for compatibility**
            $('#permissions').DataTable({
                responsive: true,
                scrollX: true,
                autoWidth: false,
                dom: 'Bfrtip', // This correctly enables the Buttons extension
                buttons: [{extend: 'copyHtml5',text: '<i class="bi bi-clipboard-check"></i> Copiar',className: 'btn btn-sm btn-success'}, // Added btn-sm for better consistency
                          {extend: 'csvHtml5',text: '<i class="bi bi-filetype-csv"></i> CSV',className: 'btn btn-sm btn-primary'},
                          {extend: 'excelHtml5',text: '<i class="bi bi-file-earmark-excel"></i> Excel',className: 'btn btn-sm btn-secondary'},
                          {extend: 'pdfHtml5',text: '<i class="bi bi-filetype-pdf"></i> PDF',className: 'btn btn-sm btn-danger'},
                          {extend: 'print',text: '<i class="bi bi-printer"></i> Imprimir',className: 'btn btn-sm btn-dark' }
                ],
                "language": {
                    "decimal": "",
                    "emptyTable": "No hay datos disponibles en la tabla",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ permisos", // Corrected "permissions" to "permisos"
                    "infoEmpty": "Mostrando 0 a 0 de 0 permisos",
                    "infoFiltered": "(filtrado de _MAX_ permisos totales)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Mostrar _MENU_ permisos",
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
                }
            });
        });

    </script> 
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/admin/permissions/index.blade.php ENDPATH**/ ?>