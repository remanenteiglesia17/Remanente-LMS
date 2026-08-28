<div  id="custom-tabs-three-calendario"  class="mt-4">
    <div class="row">
        <div class="col-12">
            <h4>Calendario académico</h4>
            <p class="text-muted">Programa los eventos importantes del curso: exámenes, entregas, parciales y festivos.</p>
            
            
            <div class="card card-primary card-outline mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Agregar nuevo evento</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="evento_fecha">Fecha <span class="text-danger">*</span></label>
                                <input type="date" 
                                       id="evento_fecha" 
                                       class="form-control form-control-sm"
                                       min="<?php echo e(date('Y-m-d')); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="evento_nombre">Nombre del evento <span class="text-danger">*</span></label>
                                <input type="text" 
                                       id="evento_nombre" 
                                       class="form-control form-control-sm" 
                                       placeholder="Ej: Examen parcial - Unidad 1"
                                       maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="evento_tipo">Tipo <span class="text-danger">*</span></label>
                                <select id="evento_tipo" class="form-control form-control-sm">
                                    <option value="examen">📝 Examen</option>
                                    <option value="parcial">📋 Parcial</option>
                                    <option value="entrega">📤 Entrega</option>
                                    <option value="festivo">🎉 Festivo</option>
                                    <option value="otro">📌 Otro</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-group mb-0 w-100">
                                <button type="button" id="addEvento" class="btn btn-success btn-sm btn-block">
                                    <i class="fas fa-plus"></i> Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Eventos programados</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0" id="tabla-calendario">
                            <thead class="thead-light">
                                <tr>
                                    <th width="20%">
                                        <i class="fas fa-calendar"></i> Fecha
                                    </th>
                                    <th width="50%">
                                        <i class="fas fa-file-alt"></i> Evento
                                    </th>
                                    <th width="20%">
                                        <i class="fas fa-tag"></i> Tipo
                                    </th>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.cursos.destroy')): ?>

                                    <th width="10%" class="text-center">
                                        <i class="fas fa-cog"></i> Acciones
                                    </th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody id="calendario-body">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            
            <input type="hidden" name="calendario_json" id="calendario_json">
        </div>
    </div>
</div><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-Re\resources\views/admin/cursos/dynamic/calendario.blade.php ENDPATH**/ ?>