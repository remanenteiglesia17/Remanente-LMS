<?php $__env->startSection('title', 'Mi Calendario Semanal'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1>Mi Calendario Semanal</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-week"></i>
                        Calendario de <?php echo e($cursos->pluck('nombre')->join(', ')); ?>

                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-primary mr-2"><i class="fas fa-square"></i> Clase</span>
                        <span class="badge badge-danger"><i class="fas fa-square"></i> Evento académico</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Aquí puedes ver, semana a semana, tus clases programadas y los eventos académicos de tu
                        curso (exámenes, entregas, parciales y festivos). Usa las flechas o el botón
                        <strong>Hoy</strong> para moverte entre semanas.
                    </p>
                    <div id="calendario-estudiante"></div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="eventoDetalleModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="evento-titulo">Detalle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Tipo:</strong> <span id="evento-tipo"></span></p>
                    <p><strong>Fecha:</strong> <span id="evento-fecha"></span></p>
                    <p><strong>Hora:</strong> <span id="evento-hora"></span></p>
                    <p id="evento-curso-wrap"><strong>Curso:</strong> <span id="evento-curso"></span></p>
                    <p id="evento-profesor-wrap"><strong>Profesor:</strong> <span id="evento-profesor"></span></p>
                    <p id="evento-lugar-wrap"><strong>Lugar / enlace:</strong> <span id="evento-lugar"></span></p>
                    <p id="evento-descripcion-wrap"><strong>Descripción:</strong> <span id="evento-descripcion"></span></p>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
    <style>
        #calendario-estudiante {
            min-height: 650px;
        }
        .fc-event {
            cursor: pointer;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script>
        window.Laravel = window.Laravel || {};
        window.Laravel.routes = window.Laravel.routes || {};
        window.Laravel.routes.estudianteCalendarioEventos = "<?php echo e(route('estudiante.calendario.eventos')); ?>";
    </script>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/pages/estudiante-calendario.ts']); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS\resources\views/estudiante/calendario/index.blade.php ENDPATH**/ ?>