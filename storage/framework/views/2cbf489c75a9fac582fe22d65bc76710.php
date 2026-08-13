<div id="custom-tabs-three-descripcion" class="mt-4">
    <div class="row">
        <div class="col-12">
            
            <h4>Descripción del curso</h4>

            
            <div class="form-group">
                <div class="row">
                    
                    <div class="col-md-4">
                        <label for="codigo">Código</label>
                        <input type="text" name="codigo" id="codigo" class="form-control"
                            placeholder="COD-001"
                            value="<?php echo e(old('codigo', $curso->codigo)); ?>" required>
                    </div>

                    
                    <div class="col-md-4">
                        <label for="nombre">Nombre del curso</label>
                        <input type="text" name="nombre" id="nombre" class="form-control"
                            placeholder="Escribe el nombre"
                            value="<?php echo e(old('nombre', $curso->nombre)); ?>" required>
                    </div>
                                        
                    <div class="col-md-4">
                        <label for="periodo">Período académico</label>
                        <select name="periodo" id="periodo" class="form-control" required>
                            <option value="">Seleccione un período</option>
                            <option value="2026-1" <?php echo e(old('periodo', $curso->periodo) == '2026-1' ? 'selected' : ''); ?>>2026 - I</option>
                            <option value="2026-2" <?php echo e(old('periodo', $curso->periodo) == '2026-2' ? 'selected' : ''); ?>>2026 - II</option>
                            <option value="2027-1" <?php echo e(old('periodo', $curso->periodo) == '2027-1' ? 'selected' : ''); ?>>2027 - I</option>
                        </select>
                    </div>
                </div>
            </div>

            
            <div class="form-group">
                <div class="row">
                    
                    <div class="col-md-12">
                        <label for="descripcion">Descripción</label>
                        <textarea name="descripcion" id="descripcion" class="form-control" rows="3"
                            placeholder="Escribe la descripción del curso..." required><?php echo e(old('descripcion', $curso->descripcion)); ?></textarea>
                    </div>


                </div>
            </div>

            <hr>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/admin/cursos/dynamic/descripcion.blade.php ENDPATH**/ ?>