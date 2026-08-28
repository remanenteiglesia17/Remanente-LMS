

<?php $__env->startSection('title', ucfirst(auth()->user()->getRoleNames()->first())); ?>
<?php $__env->startSection('css'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content_header'); ?>
    <h1>
        Listado de horarios</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Horarios registrados</h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('admin.horarios.create')); ?>" class="btn btn-primary">Registrar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($info = Session::get('info')): ?>
                        <div class="alert alert-success"><strong><?php echo e(session('info')); ?></strong></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <table id="horarios" class="table table-striped table-bordered table-hover table-sm">
                        <thead class="thead-dark">
                            <tr>
                                <th>Nro</th>
                                <th>Profesor</th> 
                                <th>Curso</th>
                                <th>Dia de atencion</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Vigencia</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $contador = 1; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $horarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $horario): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td scope="row"><?php echo e($contador++); ?></td>
                                    <td scope="row"><?php echo e($horario->profesores->first()->nombres ?? ''); ?></td> 
                                    <td scope="row"><?php echo e($horario->cursos->pluck('nombre')->join(', ')); ?></td>

                                    <td scope="row"><?php echo e($horario->dia); ?></td>
                                    <td scope="row" class="text-center"><?php echo e($horario->hora_inicio->format('h:i A')); ?></td>
                                    <td scope="row" class="text-center"><?php echo e($horario->hora_fin->format('h:i A')); ?></td>
                                    <td scope="row" class="text-center">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($horario->fecha_inicio && $horario->fecha_fin): ?>
                                            <?php echo e($horario->fecha_inicio->format('d/m/Y')); ?> - <?php echo e($horario->fecha_fin->format('d/m/Y')); ?>

                                        <?php else: ?>
                                            <span class="text-muted">Sin definir</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                    <td scope="row">
                                        <div class="btn-group" role="group" aria-label="basic example">

                                            
                                            <a href="#" class="btn btn-warning btn-sm mr-1"
                                                data-id="<?php echo e($horario->id); ?>" data-toggle="modal" data-target="#editModal"
                                                title="Editar"> <i class="fas fa-edit"></i></a>

                                            <form id="delete-form-<?php echo e($horario->id); ?>"
                                                action="<?php echo e(route('admin.horarios.destroy', $horario->id)); ?>"
                                                method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="button" class="btn btn-danger btn-delete"
                                                    data-id="<?php echo e($horario->id); ?>"
                                                    data-text="¿Estás seguro de eliminar este horario?">
                                                    <i class="fas fa-trash"></i>
                                        </form>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                    <?php echo $__env->make('admin.horarios.edit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-4">
                            <h3 class="card-title">Calendario de atencion de Profesores</h3>
                        </div>
                        <div class="col-md-4 d-flex justify-content-end">
                            <label for="curso_id">Cursos </label><b class="text-danger">*</b>
                        </div>
                        <div class="col-md-4">
                            <select name="curso_id" id="curso_select" class="form-control">
                                <option value="" selected disabled>Seleccione una opción</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($curso->id); ?>">
                                        <?php echo e($curso->nombre); ?> </option>
                                    
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <hr>
                    <div id="curso_info"></div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>

    <script>  
        $('#curso_select').on('change', function() {
            var curso_id = $('#curso_select').val();
            
            // ✅ USAR LA NUEVA RUTA
            var url = "<?php echo e(route('admin.horarios.show_datos_por_curso', ':id')); ?>";
            url = url.replace(':id', curso_id);

            if (curso_id) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    cache: false,
                    success: function(data) {
                        $('#curso_info').html(data);
                    },
                    error: function() {
                        alert('Error al obtener datos del curso');
                    }
                });
            } else {
                $('#curso_info').html('');
            }
        });
    </script>

    <script>
        new DataTable('#horarios', {
            scrollX: true,
            responsive: true,
            autoWidth: false,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'collection',
                text: 'Reportes',
                orientation: 'landscape',
                buttons: [{extend: 'copyHtml5',text: '<i class="bi bi-clipboard-check"></i> Copiar'}, // Added btn-sm for better consistency
                          {extend: 'csvHtml5',text: '<i class="bi bi-filetype-csv"></i> CSV'},
                          {extend: 'excelHtml5',text: '<i class="bi bi-file-earmark-excel"></i> Excel'},
                          {extend: 'pdfHtml5',text: '<i class="bi bi-filetype-pdf"></i> PDF'},
                          {extend: 'print',text: '<i class="bi bi-printer"></i> Imprimir' },
                          {extend: 'colvis'}],
            }, ],
            "language": {
                "decimal": "",
                "emptyTable": "No hay datos disponibles en la tabla",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ horarios",
                "infoEmpty": "Mostrando 0 a 0 de 0 horarios",
                "infoFiltered": "(filtrado de _MAX_ horarios totales)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ horarios",
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
                $('.dt-button').css({ // Apply custom styles after initialization
                    'background-color': '#4a4a4a',
                    'color': 'white',
                    'border': 'none',
                    'border-radius': '4px',
                    'padding': '8px 12px',
                    'margin': '0 5px',
                    'font-size': '14px'
                });
            }

        });
    </script>
    <script>
        $('#editModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var modal = $(this);

            var url = "<?php echo e(route('admin.horarios.edit', ':id')); ?>".replace(':id', id);

            $.ajax({
                url: url,
                method: 'GET',
                cache: false,
                success: function(data) {
                    // Cambiar acción del form
                    var formAction = "<?php echo e(route('admin.horarios.update', ':id')); ?>".replace(':id', data.horario.id);
                    modal.find('#editForm').attr('action', formAction);

                    // Llenar Profesores
                    var profesorSelect = modal.find('#edit-profesor');
                    profesorSelect.empty();

                    $.each(data.profesores, function(index, profesor) {
                        profesorSelect.append(new Option(profesor.nombres + ' ' + profesor.apellidos, profesor.id));
                    });
                    // Seleccionar el profesor asignado
                    profesorSelect.val(data.horario.profesor_id).trigger('change');
                    // Llenar Cursos
                    var cursoSelect = modal.find('#edit-curso');
                    cursoSelect.empty();
                    $.each(data.cursos, function(index, curso) {
                        cursoSelect.append(new Option(curso.nombre, curso.id));
                    });
                    // Seleccionar cursos relacionados
                    var selectedCursos = data.horario.cursos.map(c => c.id);
                    cursoSelect.val(selectedCursos).trigger('change');

                    // Inputs de horario
                    modal.find('#edit-dia').val(data.horario.dia);
                    modal.find('#edit-hora_inicio').val(data.horario.hora_inicio);
                    modal.find('#edit-hora_fin').val(data.horario.hora_fin);
                    modal.find('#edit-fecha_inicio').val(data.horario.fecha_inicio);
                    modal.find('#edit-fecha_fin').val(data.horario.fecha_fin);
                },
                error: function(xhr) {
                    console.error('Error al cargar los datos del horario:', xhr);
                }
            });
        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/admin/horarios/index.blade.php ENDPATH**/ ?>