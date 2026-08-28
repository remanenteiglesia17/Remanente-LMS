

<?php $__env->startSection('title', ucfirst(auth()->user()->getRoleNames()->first())); ?>
<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content_header'); ?>
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0"> Listado de cursos</h1>

        <a href="<?php echo e(route('admin.home')); ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Volver
        </a>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Cursos registrados</h3>
                    <div class="card-tools">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.cursos.create')): ?>
                            <a class="btn btn-secondary" data-toggle="modal" data-target="#createCursoModal">Registrar
                                <i class="bi bi-plus-circle-fill"></i>
                            </a>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('info')): ?>
                        <div class="alert alert-success"><strong><?php echo e(session('info')); ?></strong></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <table id="cursos" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nro</th>
                                <th>Curso</th>
                                <th>Horas</th>
                                <th>Ver</th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!Auth::user()->estudiante): ?>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $contador = 1; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td scope="row"><?php echo e($contador++); ?></td>
                                    <td scope="row"><?php echo e($curso->nombre); ?></td>
                                    <td scope="row"><?php echo e($curso->horas_requeridas); ?></td>
                                    <td scope="row">
                                        
                                        <a href="<?php echo e(route('admin.cursos.show', $curso)); ?>" class="btn btn-sm btn-info"
                                            title="Ver curso">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!Auth::user()->estudiante): ?>
                                        <td scope="row">
                                            <form id="disable-form-<?php echo e($curso->id); ?>"
                                                action="<?php echo e(route('admin.cursos.toggleStatus', $curso->id)); ?>"
                                                method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?> <!-- Laravel permite cambios parciales con PATCH -->
                                                <button type="submit"
                                                    class="btn <?php echo e($curso->estado ? 'btn-success' : 'btn-danger'); ?>  btn-sm">
                                                    <?php echo $curso->estado ? '<i class="fa-solid fa-square-check"></i>' : '<i class="fa-solid fa-circle-xmark"></i>'; ?>

                                                </button>
                                            </form>
                                        </td>
                                        <td scope="row">
                                            <a href="<?php echo e(route('admin.cursos.edit', $curso->id)); ?>"
                                                class="btn btn-warning btn-sm mr-1">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form id="delete-form-<?php echo e($curso->id); ?>"
                                                action="<?php echo e(route('admin.cursos.destroy', $curso->id)); ?>" method="POST"
                                                style="display:inline;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="button" class="btn btn-danger btn-sm btn-delete"
                                                    data-id="<?php echo e($curso->id); ?>"
                                                    data-text="¿Estás seguro de que deseas eliminar este curso?">
                                                    <i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('admin.cursos.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>


    <script>
        new DataTable('#cursos', {
            responsive: true,
            scrollX: true,
            autoWidth: false,
            dom: 'Bfrtip', // Añade el contenedor de botones
            buttons: [{
                    extend: 'copyHtml5',
                    text: '<i class="bi bi-clipboard-check"></i> Copiar',
                    className: 'btn btn-sm btn-success'
                }, // Added btn-sm for better consistency
                {
                    extend: 'csvHtml5',
                    text: '<i class="bi bi-filetype-csv"></i> CSV',
                    className: 'btn btn-sm btn-warning'
                },
                {
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    className: 'btn btn-sm btn-secondary'
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-filetype-pdf"></i> PDF',
                    className: 'btn btn-sm btn-danger'
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Imprimir',
                    className: 'btn btn-sm btn-dark'
                },
                {
                    extend: 'colvis'
                }
            ],
            "language": {
                "decimal": "",
                "emptyTable": "No hay datos disponibles en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ cursos",
                "infoEmpty": "Mostrando 0 a 0 de 0 cursos",
                "infoFiltered": "(filtrado de _MAX_ cursos totales)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ cursos",
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
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/admin/cursos/index.blade.php ENDPATH**/ ?>