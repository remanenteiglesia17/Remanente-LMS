

<?php $__env->startSection('title', 'Editar Tarea'); ?>

<?php $__env->startSection('content_header'); ?>
    <h1><i class="fas fa-edit"></i> Editar Tarea: <?php echo e($tarea->titulo_tarea); ?></h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <form action="<?php echo e(route('admin.profesor.tareas.update', $tarea->id)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <div class="row">
            
            <div class="col-md-8">
                
                
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-info-circle"></i> Información Básica</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="curso_id">Curso <span class="text-danger">*</span></label>
                            <select name="curso_id" id="curso_id" class="form-control <?php $__errorArgs = ['curso_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cursos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $curso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($curso->id); ?>" <?php echo e(old('curso_id', $tarea->curso_id) == $curso->id ? 'selected' : ''); ?>>
                                        <?php echo e($curso->codigo ?? ''); ?> - <?php echo e($curso->nombre); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>

                        
                        <div class="form-group">
                            <label for="modulo_id">Módulo <span class="text-danger">*</span></label>
                            <select name="modulo_id" id="modulo_id" class="form-control <?php $__errorArgs = ['modulo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">-- Seleccione un módulo --</option>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['modulo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="tipo">Tipo de Actividad <span class="text-danger">*</span></label>
                            <select name="tipo" id="tipo" class="form-control <?php $__errorArgs = ['tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <?php $tipos = ['tarea', 'quiz', 'examen', 'proyecto', 'foro']; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tipos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($t); ?>" <?php echo e(old('tipo', $tarea->tipo) == $t ? 'selected' : ''); ?>><?php echo e(ucfirst($t)); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="titulo_tarea">Título <span class="text-danger">*</span></label>
                            <input type="text" name="titulo_tarea" id="titulo_tarea" class="form-control <?php $__errorArgs = ['titulo_tarea'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   value="<?php echo e(old('titulo_tarea', $tarea->titulo_tarea)); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="descripcion_tarea">Descripción <span class="text-danger">*</span></label>
                            <textarea name="descripcion_tarea" id="descripcion_tarea" class="form-control <?php $__errorArgs = ['descripcion_tarea'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="5" required><?php echo e(old('descripcion_tarea', $tarea->descripcion_tarea)); ?></textarea>
                        </div>
                    </div>
                </div>

                
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list-ul"></i> Requisitos y Criterios</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="requisitos">Requisitos (opcional)</label>
                            <textarea name="requisitos" id="requisitos" class="form-control" rows="5"
                                      placeholder="Lista los requisitos que debe cumplir la tarea..."><?php echo e(old('requisitos', $tarea->requisitos)); ?></textarea>
                            <small class="text-muted">Un requisito por línea</small>
                        </div>
                        <div class="form-group">
                            <label for="criterios_evaluacion">Criterios de Evaluación (opcional)</label>
                            <textarea name="criterios_evaluacion" id="criterios_evaluacion" class="form-control" rows="5"
                                      placeholder="Define cómo se evaluará la tarea..."><?php echo e(old('criterios_evaluacion', $tarea->criterios_evaluacion)); ?></textarea>
                        </div>
                    </div>
                </div>

                
                <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-paperclip"></i> Recursos y Documentos</h3>
                    </div>
                    <div class="card-body">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tarea->documentos->count() > 0): ?>
                            <h6>Documentos actuales:</h6>
                            <ul class="list-group mb-3">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $tarea->documentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-file-alt"></i> <?php echo e($doc->titulo); ?></span>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo e(asset('storage/'.$doc->archivo)); ?>" target="_blank" class="btn btn-info"><i class="fas fa-eye"></i></a>
                                            <button type="button" class="btn btn-danger" onclick="eliminarArchivo(<?php echo e($doc->id); ?>)"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </ul>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <div class="form-group">
                            <label for="documentos">Agregar nuevos archivos</label>
                            <div class="custom-file">
                                <input type="file" name="documentos[]" id="documentos" class="custom-file-input" multiple>
                                <label class="custom-file-label" for="documentos">Seleccionar archivos...</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-md-4">
                
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Fechas</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="fecha_apertura">Apertura</label>
                            <input type="datetime-local" name="fecha_apertura" id="fecha_apertura" class="form-control" 
                                   value="<?php echo e(old('fecha_apertura', $tarea->fecha_apertura ? date('Y-m-d\TH:i', strtotime($tarea->fecha_apertura)) : '')); ?>">
                        </div>
                        <div class="form-group">
                            <label for="fecha_entrega">Entrega <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="fecha_entrega" id="fecha_entrega" class="form-control" 
                                   value="<?php echo e(old('fecha_entrega', date('Y-m-d\TH:i', strtotime($tarea->fecha_entrega)))); ?>" required>
                        </div>
                    </div>
                </div>

                
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-star"></i> Calificación</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="puntaje">Nota Máxima <span class="text-danger">*</span></label>
                            <input type="number" name="puntaje" id="puntaje" class="form-control <?php $__errorArgs = ['puntaje'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('puntaje', $tarea->puntaje ?? 5.0)); ?>" min="0" max="5" step="0.1" required>
                            <small class="text-muted">Escala de evaluación (0.0 - 5.0)</small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['puntaje'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="peso">Porcentaje del Curso (%) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="peso" id="peso" class="form-control <?php $__errorArgs = ['peso'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('peso', $tarea->peso)); ?>" min="0" max="100" step="0.01" placeholder="Ej. 10" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <small class="text-muted">Peso sobre la nota final (0.00% - 100.00%)</small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['peso'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="invalid-feedback d-block"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="permite_entregas_tardias" name="permite_entregas_tardias" 
                                   <?php echo e(old('permite_entregas_tardias', $tarea->permite_entregas_tardias) ? 'checked' : ''); ?> onchange="togglePenalizacion()">
                            <label class="custom-control-label" for="permite_entregas_tardias">Permitir entregas tardías</label>
                        </div>

                        <div id="div_penalizacion" class="form-group" style="display: none;">
                            <label for="penalizacion_tardia">Penalización (%)</label>
                            <input type="number" name="penalizacion_tardia" id="penalizacion_tardia" class="form-control" value="<?php echo e(old('penalizacion_tardia', $tarea->penalizacion_tardia)); ?>">
                        </div>
                    </div>
                </div>

                
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-upload"></i> Formato de Entrega</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="formato_entrega">Tipo de Entrega <span class="text-danger">*</span></label>
                            <select name="formato_entrega" id="formato_entrega" class="form-control <?php $__errorArgs = ['formato_entrega'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="archivo" <?php echo e(old('formato_entrega', $tarea->formato_entrega) == 'archivo' ? 'selected' : ''); ?>>Solo archivos</option>
                                <option value="enlace" <?php echo e(old('formato_entrega', $tarea->formato_entrega) == 'enlace' ? 'selected' : ''); ?>>Solo enlace (URL)</option>
                                <option value="texto" <?php echo e(old('formato_entrega', $tarea->formato_entrega) == 'texto' ? 'selected' : ''); ?>>Solo texto</option>
                                <option value="ambos" <?php echo e(old('formato_entrega', $tarea->formato_entrega) == 'ambos' ? 'selected' : ''); ?>>Archivos y enlace</option>
                            </select>
                        </div>
                    </div>
                </div>

                
                <div class="card card-outline card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-eye"></i> Visibilidad</h3>
                    </div>
                    <div class="card-body">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="visible" name="visible"
                                   <?php echo e(old('visible', $tarea->visible) ? 'checked' : ''); ?>>
                            <label class="custom-control-label" for="visible">Visible para estudiantes</label>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Guardar Cambios</button>
                        <a href="<?php echo e(route('admin.profesor.tareas.index')); ?>" class="btn btn-secondary btn-block">Cancelar</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<form id="form-eliminar-doc" action="" method="POST" style="display: none;">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
<script>
    const cursosData = <?php echo json_encode($cursos, 15, 512) ?>;
    const moduloActualId = "<?php echo e(old('modulo_id', $tarea->modulo_id)); ?>";

    function cargarModulos(cursoId, selectedModuloId = null) {
        const moduloSelect = document.getElementById('modulo_id');
        moduloSelect.innerHTML = '<option value="">-- Seleccione un módulo --</option>';

        const curso = cursosData.find(c => c.id == cursoId);
        if (curso && curso.modulos && curso.modulos.length > 0) {
            curso.modulos.forEach(mod => {
                const option = document.createElement('option');
                option.value = mod.id;
                option.textContent = mod.nombre;
                if (selectedModuloId && selectedModuloId == mod.id) {
                    option.selected = true;
                }
                moduloSelect.appendChild(option);
            });
        }
    }

    document.getElementById('curso_id').addEventListener('change', function() {
        cargarModulos(this.value);
    });

    function togglePenalizacion() {
        const checkbox = document.getElementById('permite_entregas_tardias');
        const div = document.getElementById('div_penalizacion');
        div.style.display = checkbox.checked ? 'block' : 'none';
    }

    function eliminarArchivo(docId) {
        if(confirm('¿Estás seguro de eliminar este documento?')) {
            const form = document.getElementById('form-eliminar-doc');
            form.action = `/profesor/tareas/documentos/${docId}`;
            form.submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        togglePenalizacion();
        const cursoIdInicial = document.getElementById('curso_id').value;
        if (cursoIdInicial) {
            cargarModulos(cursoIdInicial, moduloActualId);
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\www\Remanente-LMS-UPDATE\resources\views/profesor/tareas/edit.blade.php ENDPATH**/ ?>