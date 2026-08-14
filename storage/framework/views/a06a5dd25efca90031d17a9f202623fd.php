
<div id="nav-curso-fixed" class="bg-white border-bottom" style="position: sticky; top: 57px; z-index: 1020;">
    <div class="d-flex justify-content-between align-items-center">
        <div class="mb-3">

            <h1><?php echo e($curso->nombre); ?></h1>
            
        </div>
        <a href="<?php echo e(route('admin.cursos.index')); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>


    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="#custom-tabs-three-descripcion">
                Descripción
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#custom-tabs-three-objetivos">
                Objetivos
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#custom-tabs-three-bibliografia">
                Bibliografía
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="#custom-tabs-three-calendario">
                Calendario
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#custom-tabs-three-politicas">
                Políticas
            </a>
        </li>
    </ul>
</div>
<?php /**PATH C:\xampp\htdocs\www\Canvas-Church60\resources\views/admin/cursos/content/nav-show.blade.php ENDPATH**/ ?>