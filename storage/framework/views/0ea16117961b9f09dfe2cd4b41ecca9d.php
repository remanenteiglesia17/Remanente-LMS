<?php $__env->startSection('title', 'Perfil'); ?>

<?php $__env->startSection('content_header'); ?>
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Perfil Configuraciones</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item active">Usuario Perfil</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Main content -->
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-3">
                <form class="form-horizontal" action="<?php echo e(route('admin.user-profile-information.update')); ?>" method="POST"
                    enctype="multipart/form-data">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <!-- Perfil Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <label for="photo">Foto</label>

                            <div class="form-group text-center">
                                <!-- Foto de Perfil Actual -->
                                <img id="current-photo" src="<?php echo e(asset('storage/' . Auth::user()->profile_photo_path)); ?>"
                                    onerror="this.onerror=null; this.src='<?php echo e($user->adminlte_image()); ?>';"
                                    alt="<?php echo e($user->name); ?>" class="profile-user-img img-fluid img-circle"
                                    style="width: 100px; height: 100px; object-fit: cover;" />


                            </div>
                            <div class="form-group">
                                <input type="file" name="photo" id="file" class="form-control-file"
                                    accept=".jpg, .jpeg, .png" accept="image/*" style="display: none;">
                            </div>
                            <h3 class="profile-username text-center"><?php echo e($user->name); ?></h3>
                            <p class="text-muted text-center"><?php echo e($user->email); ?></p>
                        </div>
                    </div>

            </div>
            <div class="col-md-9">
                <div class="card">
                    
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="settings">

                                <div class="row">
                                    <div class="col">
                                        <div class="card">
                                            <div class="card-body">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($info = Session::get('success')): ?>
                                                    <div class="alert alert-success">
                                                        <strong><?php echo e($info); ?></strong>
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                                <div class="form-group row">
                                                    <label for="inputName"
                                                        class="col-sm-2 col-form-label"><?php echo e(__('Name')); ?></label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="name"
                                                            name="name" value="<?php echo e($user->name); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="email"
                                                        class="col-sm-2 col-form-label"><?php echo e(__('Email')); ?></label>
                                                    <div class="col-sm-10">
                                                        <input type="email" class="form-control" id="email"
                                                            name="email" value="<?php echo e($user->email); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="offset-sm-2 col-sm-10">
                                                        <button type="submit"
                                                            class="btn btn-danger"><?php echo e(__('Save')); ?></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                </form>
                                <div class="row">
                                    <div class="col">
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('admin.config.index')): ?>
                                            <form action="<?php echo e(route('admin.user-profile-password.updatePassword')); ?>"
                                                method="POST">
                                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-group row">
                                                            <label for="current_password"
                                                                class="col-sm-2 col-form-label"><?php echo e(__('Contrasena Actual')); ?></label>
                                                            <div class="col-sm-10">
                                                                <input type="password" class="form-control"
                                                                    id="current_password" name="current_password"
                                                                    autocomplete="new-password">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="password"
                                                                class="col-sm-2 col-form-label"><?php echo e(__('Password')); ?></label>
                                                            <div class="col-sm-10">
                                                                <input type="password" class="form-control" id="password"
                                                                    name="password" autocomplete="new-password">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="password_confirmation"
                                                                class="col-sm-2 col-form-label"><?php echo e(__('Password')); ?></label>
                                                            <div class="col-sm-10">
                                                                <input type="password" class="form-control"
                                                                    id="password_confirmation" name="password_confirmation"
                                                                    autocomplete="new-password">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="offset-sm-2 col-sm-10">
                                                                <button type="submit"
                                                                    class="btn btn-danger"><?php echo e(__('Save')); ?></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="activity">
                                <!-- Post -->

                                <!-- /.post -->
                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane " id="timeline">
                                <!-- The timeline -->
                                <div class="timeline timeline-inverse">
                                    <!-- timeline time label -->
                                    <div class="time-label">
                                        <span class="bg-danger">
                                            10 Feb. 2014
                                        </span>
                                    </div>
                                    <!-- /.timeline-label -->
                                    <!-- timeline item -->
                                    <div>
                                        <i class="fas fa-envelope bg-primary"></i>

                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> 12:05</span>

                                            <h3 class="timeline-header"><a href="#">Support Team</a> sent you an
                                                email</h3>

                                            <div class="timeline-body">
                                                Etsy doostang zoodles disqus groupon greplin oooj voxy zoodles,
                                                weebly ning heekya handango imeem plugg dopplr jibjab, movity
                                                jajah plickers sifteo edmodo ifttt zimbra. Babblely odeo kaboodle
                                                quora plaxo ideeli hulu weebly balihoo...
                                            </div>
                                            <div class="timeline-footer">
                                                <a href="#" class="btn btn-primary btn-sm">Read more</a>
                                                <a href="#" class="btn btn-danger btn-sm">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END timeline item -->
                                    <!-- timeline item -->

                                    <div>
                                        <i class="fas fa-camera bg-purple"></i>

                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> 2 days ago</span>

                                            <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos
                                            </h3>

                                            <div class="timeline-body">
                                                <img src="https://placehold.it/150x100" alt="...">
                                                <img src="https://placehold.it/150x100" alt="...">
                                                <img src="https://placehold.it/150x100" alt="...">
                                                <img src="https://placehold.it/150x100" alt="...">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END timeline item -->
                                    <div>
                                        <i class="far fa-clock bg-gray"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- /.tab-pane -->


                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('js'); ?>
        <script>
            // Obtén los elementos
            const picture = document.getElementById('current-photo');
            const fileInput = document.getElementById('file');

            // Al hacer clic en la imagen, simula un clic en el input file
            picture.addEventListener('click', () => {
                fileInput.click();
            });
            // Cambiar la imagen mostrada cuando se selecciona un archivo
            fileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                const reader = new FileReader();

                reader.onload = (event) => {
                    // Actualiza la imagen con la nueva seleccionada
                    picture.src = event.target.result;
                };

                if (file) {
                    reader.readAsDataURL(file);
                }
            });
        </script>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('adminlte::page', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\Remanente\Canvas-Church60\resources\views/admin/profile/index.blade.php ENDPATH**/ ?>