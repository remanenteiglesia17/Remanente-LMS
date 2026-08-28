<div id="custom-tabs-three-objetivos">
    <div class="row">
        <div class="col-12">
            <h4>Objetivos del curso</h4>
            
            <h5 class="mt-3">Objetivo General<span class="text-danger">*</span>
            </h5>
            <textarea name="objetivo_general" class="form-control mb-3" rows="3" required><?php echo e(old('objetivo_general', optional($curso->objetivos->where('tipo','general')->first())->descripcion_obj)); ?></textarea>
            <hr>
            
            <h5>Objetivos Específicos</h5>
            <div class="input-group mb-2">
                <input type="text" id="input_especifico" class="form-control" placeholder="Escribe un objetivo específico">
                <button type="button" id="add_especifico" class="btn btn-success">Agregar</button>
            </div>

            <ul id="lista_especificos" class="list-group mb-3">
                
            </ul>
            <input type="hidden" name="objetivos_especificos" id="input_hidden_especificos">

        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/admin/cursos/dynamic/objetivos.blade.php ENDPATH**/ ?>