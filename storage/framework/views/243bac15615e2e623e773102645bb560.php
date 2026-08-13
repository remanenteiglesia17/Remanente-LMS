<nav class="main-header navbar
    <?php echo e(config('adminlte.classes_topnav_nav', 'navbar-expand')); ?>

    <?php echo e(config('adminlte.classes_topnav', 'navbar-white navbar-light')); ?>">

    
    <ul class="navbar-nav">
        <?php echo $__env->make('adminlte::partials.navbar.menu-item-left-sidebar-toggler', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->renderEach('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-left'), 'item'); ?>
        <?php echo $__env->yieldContent('content_top_nav_left'); ?>
    </ul>

    
    <ul class="navbar-nav ml-auto">
        <?php echo $__env->yieldContent('content_top_nav_right'); ?>
        <?php echo $__env->renderEach('adminlte::partials.navbar.menu-item', $adminlte->menu('navbar-right'), 'item'); ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        <li class="nav-item dropdown" id="notif-nav-item">
            <a class="nav-link" data-toggle="dropdown" href="#" id="notif-bell-btn" aria-expanded="false">
                <i class="far fa-bell"></i>
                <span id="notif-badge" class="badge badge-warning navbar-badge" style="display:none">0</span>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notif-dropdown-menu">
                
                <span class="dropdown-item dropdown-header" id="notif-header-count">
                    0 Notificaciones
                </span>
                <div class="dropdown-divider"></div>

                
                <div id="notif-list-container">
                    <span class="dropdown-item text-muted text-center" style="font-size:13px">
                        Sin notificaciones nuevas
                    </span>
                </div>

                <div class="dropdown-divider"></div>
                <span class="dropdown-item dropdown-footer" id="notif-leidas-count">
                    0 Leídas
                </span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer" onclick="notifMarkAllRead(event)">
                    Ver todas
                </a>
            </div>
        </li>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(Auth::user()): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('adminlte.usermenu_enabled')): ?>
                <?php echo $__env->make('adminlte::partials.navbar.menu-item-dropdown-user-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php else: ?>
                <?php echo $__env->make('adminlte::partials.navbar.menu-item-logout-link', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('adminlte.right_sidebar')): ?>
            <?php echo $__env->make('adminlte::partials.navbar.menu-item-right-sidebar-toggler', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </ul>
</nav>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
<div class="modal fade" id="notif-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notif-modal-title">
                    <i class="fas fa-bell mr-2 text-warning"></i>Notificación
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="notif-modal-body">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="notif-modal-link" class="btn btn-primary">
                    <i class="fas fa-external-link-alt mr-1"></i>Ir a la tarea
                </a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<div id="notif-toast-container" style="position:fixed;bottom:20px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:8px;"></div>

<script>
(function () {
    const POLL = 30000;
    let knownIds = new Set();

    async function fetchUnread() {
        try {
            const res  = await fetch('<?php echo e(route('notificaciones.unread')); ?>', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            renderBadge(json.count);
            renderList(json.items, json.read_count ?? 0);
            showNewToasts(json.items);
        } catch (e) {}
    }

    function renderBadge(count) {
        const b = document.getElementById('notif-badge');
        const h = document.getElementById('notif-header-count');
        if (!b) return;
        if (count > 0) {
            b.textContent = count > 99 ? '99+' : count;
            b.style.display = 'inline-block';
        } else {
            b.style.display = 'none';
        }
        if (h) h.textContent = count + ' Notificaciones';
    }

    function renderList(items, readCount) {
        const c = document.getElementById('notif-list-container');
        const l = document.getElementById('notif-leidas-count');
        if (l) l.textContent = readCount + ' Leídas';
        if (!c) return;
        if (!items.length) {
            c.innerHTML = '<span class="dropdown-item text-muted text-center" style="font-size:13px">Sin notificaciones nuevas</span>';
            return;
        }
        c.innerHTML = items.map(n => `
            <a href="#" class="dropdown-item" onclick="notifShowModal('${n.id}', event)">
                <i class="fas fa-envelope mr-2"></i>
                <span style="font-weight:600">${n.data.titulo}</span>
                <span class="float-right text-muted text-sm">${n.created}</span>
            </a>
            <div class="dropdown-divider"></div>
        `).join('');
    }

    function showNewToasts(items) {
        items.forEach(n => {
            if (knownIds.has(n.id)) return;
            knownIds.add(n.id);
            const c = document.getElementById('notif-toast-container');
            const t = document.createElement('div');
            t.className = 'toast show';
            t.style.cssText = 'background:#1f3c4a;color:#fff;border-radius:8px;padding:12px 16px;min-width:260px;max-width:320px;box-shadow:0 4px 14px rgba(0,0,0,.25);position:relative;';
            t.innerHTML = `
                <strong style="display:block;margin-bottom:2px;font-size:13px">📋 ${n.data.titulo}</strong>
                <span style="font-size:12px;opacity:.85">${n.data.curso} · Entrega: ${n.data.fecha_entrega ?? '—'}</span>
                <button onclick="this.parentElement.remove()" style="position:absolute;top:6px;right:10px;background:none;border:none;color:#fff;font-size:18px;cursor:pointer;opacity:.7">×</button>
            `;
            c.appendChild(t);
            setTimeout(() => t.remove(), 6000);
        });
    }

    // Abre modal con detalle
    window.notifShowModal = async function (id, e) {
        e.preventDefault();
        // Marcar como leída
        await fetch(`/notificaciones/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        // Obtener datos del servidor
        const res  = await fetch(`/notificaciones/${id}/detail`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        // Rellenar modal
        document.getElementById('notif-modal-title').innerHTML =
            `<i class="fas fa-bell mr-2 text-warning"></i>${data.titulo}`;

        document.getElementById('notif-modal-body').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <p><strong><i class="fas fa-book mr-1 text-primary"></i> Curso:</strong> ${data.curso}</p>
                    <p><strong><i class="fas fa-calendar mr-1 text-danger"></i> Fecha de entrega:</strong> ${data.fecha_entrega ?? '—'}</p>
                    ${data.tipo ? `<p><strong><i class="fas fa-tag mr-1 text-info"></i> Tipo:</strong> <span class="badge badge-info">${data.tipo}</span></p>` : ''}
                    ${data.puntaje ? `<p><strong><i class="fas fa-star mr-1 text-warning"></i> Puntaje máximo:</strong> ${data.puntaje}</p>` : ''}
                </div>
                <div class="col-md-6">
                    ${data.descripcion ? `<p><strong>Descripción:</strong></p><p class="text-muted">${data.descripcion}</p>` : ''}
                </div>
            </div>
            ${data.documentos && data.documentos.length ? `
                <hr>
                <p><strong><i class="fas fa-paperclip mr-1"></i> Archivos adjuntos:</strong></p>
                <ul>${data.documentos.map(d => `<li><a href="${d.url}" target="_blank">${d.nombre}</a></li>`).join('')}</ul>
            ` : ''}
        `;
        document.getElementById('notif-modal-link').href = data.url ?? '#';

        // Cerrar dropdown y abrir modal
        $('#notif-dropdown-menu').removeClass('show');
        $('#notif-modal').modal('show');

        // Actualizar badge
        fetchUnread();
    };

    window.notifMarkAllRead = async function (e) {
        e.preventDefault();
        await fetch('/notificaciones/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        fetchUnread();
    };

    fetchUnread();
    setInterval(fetchUnread, POLL);
})();
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\laragon\www\Remanente\Canvas-Church5\resources\views/vendor/adminlte/partials/navbar/navbar.blade.php ENDPATH**/ ?>