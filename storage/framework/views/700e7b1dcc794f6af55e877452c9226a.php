<?php $__env->startSection('title', 'Setting User'); ?>

<?php $__env->startSection('styles'); ?>
    .layout{display:grid;grid-template-columns:260px 1fr;gap:16px;align-items:start;width:100%;max-width:100%}
    @media(max-width:900px){
    .layout{grid-template-columns:1fr}
    .tabs{flex-direction:row;gap:10px;overflow-x:auto;-webkit-overflow-scrolling:touch}
    .tab{flex:1;text-align:center;min-width:80px}
    }
    .card,.sidebarCard{min-width:0;width:100%;max-width:100%}
    .card{overflow:hidden;background:#fff;border-radius:18px;border:1px solid #e7eaf3;box-shadow:0 10px 35px
    rgba(35,45,120,.08);padding:18px}
    .tableScroll{width:100%;max-width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch;border-radius:12px}
    .tableScroll table{min-width:720px;width:100%;white-space:nowrap}
    h2,.tab{overflow-wrap:anywhere;word-break:break-word}
    .sidebarCard{background:#fff;border-radius:18px;border:1px solid #e7eaf3;box-shadow:0 10px 35px
    rgba(35,45,120,.08);padding:12px}
    .side-title{font-size:12px;color:var(--text-muted);margin:0 0 10px}
    .tabs{display:flex;flex-direction:column;gap:8px}
    .tab{width:100%;text-align:left;padding:10px 12px;border-radius:12px;border:1px solid
    #e9ecf5;background:#fff;color:#111;cursor:pointer;font-weight:700}
    .tab.active{background:var(--primary);border-color:var(--primary);color:#fff}
    .panel{display:none;width:100%;min-width:0;max-width:100%}
    .panel.active{display:block}
    .panel-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:12px}
    .panel-header h2{margin:0;font-size:18px}
    .btn{padding:11px 14px;border-radius:12px;border:none;cursor:pointer;font-weight:800;background:#fff;border:1px solid
    #eef0f6}
    .btn.primary{background:var(--primary);border-color:var(--primary);color:#fff;box-shadow:0 6px 18px
    rgba(89,102,247,0.18)}
    table{width:100%;border-collapse:collapse;table-layout:auto}
    th,td{padding:10px 8px;border-bottom:1px solid #eef1ff;text-align:left;font-size:14px;white-space:nowrap}
    th{color:#111;font-size:12px;text-transform:uppercase;letter-spacing:.02em}
    .search-box{margin-bottom:16px}
    .search-box input{width:100%;padding:11px 12px;border:1px solid
    #eef0f6;border-radius:12px;background:#fff;outline:none;font-size:14px}
    .search-box input:focus{box-shadow:0 0 0 3px rgba(89,102,247,0.08);border-color:var(--primary)}
    .no-results{display:none;text-align:center;padding:20px;color:var(--text-muted)}
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="layout">
        <div class="sidebarCard">
            <div class="side-title">KATEGORI</div>
            <div class="tabs">
                <button class="tab active" data-tab="user">User</button>
                <button class="tab" data-tab="biro">Biro</button>
                <button class="tab" data-tab="jabatan">Jabatan</button>
                <button class="tab" data-tab="role">Role</button>
            </div>
        </div>

        <div style="width:100%;min-width:0;max-width:100%">
            <!-- Success Message -->
            <?php if(session('status')): ?>
                <div
                    style="background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:14px 16px;border-radius:12px;margin-bottom:16px;font-size:15px">
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>

            <!-- User Panel -->
            <div id="panel-user" class="panel active">
                <div class="card">
                    <div class="panel-header">
                        <h2>Data User</h2>
                        <a href="<?php echo e(route('users.create')); ?>" style="text-decoration:none">
                            <button class="btn primary">+ Tambah User</button>
                        </a>
                    </div>

                    <div class="search-box">
                        <input type="text" id="searchUser" placeholder="🔍 Cari user (nama, username, NIP...)">
                    </div>

                    <div class="tableScroll">
                        <table id="tableUser" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>NIP</th>
                                    <th>Role</th>
                                    <th>Biro</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="user-row">
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($u->nama); ?></td>
                                        <td><?php echo e($u->username); ?></td>
                                        <td><?php echo e($u->nip); ?></td>
                                        <td><?php echo e($u->role_name); ?></td>
                                        <td><?php echo e($u->biro_name); ?></td>
                                        <td>
                                            <div style="display:flex;gap:6px;align-items:center">
                                                <form method="POST" action="<?php echo e(route('users.setEdit')); ?>" style="margin:0">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="id" value="<?php echo e($u->id); ?>">
                                                    <button type="submit" class="btn"
                                                        style="padding:8px 10px;white-space:nowrap">Edit</button>
                                                </form>
                                                <form method="POST" action="<?php echo e(route('users.destroy')); ?>"
                                                    onsubmit="return confirm('Yakin hapus user ini?');" style="margin:0">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <input type="hidden" name="id" value="<?php echo e($u->id); ?>">
                                                    <button type="submit" class="btn"
                                                        style="padding:8px 10px;background:#fff1f2;color:#991b1b;border-color:#fecdd3;white-space:nowrap">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr id="emptyUser">
                                        <td colspan="7" style="color:var(--text-muted);text-align:center">Belum ada data
                                            user.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="noResultsUser" class="no-results">Tidak ada data yang cocok dengan pencarian.</div>
                </div>
            </div>

            <!-- Biro Panel -->
            <div id="panel-biro" class="panel">
                <div class="card">
                    <div class="panel-header">
                        <h2>Data Biro</h2>
                        <a href="<?php echo e(route('biro.create')); ?>" style="text-decoration:none">
                            <button class="btn primary">+ Tambah Biro</button>
                        </a>
                    </div>

                    <div class="search-box">
                        <input type="text" id="searchBiro" placeholder="🔍 Cari biro (nama, divisi...)">
                    </div>

                    <div class="tableScroll">
                        <table id="tableBiro" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Biro</th>
                                    <th>Divisi</th>
                                    <th>Proyek</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $biros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="biro-row">
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($b->biro_name); ?></td>
                                        <td><?php echo e($b->divisi); ?></td>
                                        <td><?php echo e($b->is_proyek ? 'Ya' : 'Tidak'); ?></td>
                                        <td>
                                            <div style="display:flex;gap:6px;align-items:center">
                                                <form method="POST" action="<?php echo e(route('biro.setEdit')); ?>" style="margin:0">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="id" value="<?php echo e($b->id); ?>">
                                                    <button type="submit" class="btn"
                                                        style="padding:8px 10px;white-space:nowrap">Edit</button>
                                                </form>
                                                <form method="POST" action="<?php echo e(route('biro.destroy')); ?>"
                                                    onsubmit="return confirm('Yakin hapus biro ini?');" style="margin:0">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <input type="hidden" name="id" value="<?php echo e($b->id); ?>">
                                                    <button type="submit" class="btn"
                                                        style="padding:8px 10px;background:#fff1f2;color:#991b1b;border-color:#fecdd3;white-space:nowrap">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr id="emptyBiro">
                                        <td colspan="5" style="color:var(--text-muted);text-align:center">Belum ada data
                                            biro.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="noResultsBiro" class="no-results">Tidak ada data yang cocok dengan pencarian.</div>
                </div>
            </div>

            <!-- Jabatan Panel -->
            <div id="panel-jabatan" class="panel">
                <div class="card">
                    <div class="panel-header">
                        <h2>Data Jabatan</h2>
                        <a href="<?php echo e(route('jabatan.create')); ?>" style="text-decoration:none">
                            <button class="btn primary">+ Tambah Jabatan</button>
                        </a>
                    </div>

                    <div class="search-box">
                        <input type="text" id="searchJabatan" placeholder="🔍 Cari jabatan...">
                    </div>

                    <div class="tableScroll">
                        <table id="tableJabatan" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jabatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $jabatans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="jabatan-row">
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($j->jabatan); ?></td>
                                        <td>
                                            <div style="display:flex;gap:6px;align-items:center">
                                                <form method="POST" action="<?php echo e(route('jabatan.setEdit')); ?>"
                                                    style="margin:0">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="id" value="<?php echo e($j->id); ?>">
                                                    <button type="submit" class="btn"
                                                        style="padding:8px 10px;white-space:nowrap">Edit</button>
                                                </form>
                                                <form method="POST" action="<?php echo e(route('jabatan.destroy')); ?>"
                                                    onsubmit="return confirm('Yakin hapus jabatan ini?');"
                                                    style="margin:0">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <input type="hidden" name="id" value="<?php echo e($j->id); ?>">
                                                    <button type="submit" class="btn"
                                                        style="padding:8px 10px;background:#fff1f2;color:#991b1b;border-color:#fecdd3;white-space:nowrap">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr id="emptyJabatan">
                                        <td colspan="3" style="color:var(--text-muted);text-align:center">Belum ada
                                            data jabatan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="noResultsJabatan" class="no-results">Tidak ada data yang cocok dengan pencarian.</div>
                </div>
            </div>

            <!-- Role Panel -->
            <div id="panel-role" class="panel">
                <div class="card">
                    <div class="panel-header">
                        <h2>Data Role</h2>
                        <a href="<?php echo e(route('role.create')); ?>" style="text-decoration:none">
                            <button class="btn primary">+ Tambah Role</button>
                        </a>
                    </div>

                    <div class="search-box">
                        <input type="text" id="searchRole" placeholder="🔍 Cari role...">
                    </div>

                    <div class="tableScroll">
                        <table id="tableRole" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Role</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="role-row">
                                        <td><?php echo e($loop->iteration); ?></td>
                                        <td><?php echo e($r->role_name); ?></td>
                                        <td>
                                            <div style="display:flex;gap:6px;align-items:center">
                                                <form method="POST" action="<?php echo e(route('role.setEdit')); ?>"
                                                    style="margin:0">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="id" value="<?php echo e($r->id); ?>">
                                                    <button type="submit" class="btn"
                                                        style="padding:8px 10px;white-space:nowrap">Edit</button>
                                                </form>
                                                <form method="POST" action="<?php echo e(route('role.destroy')); ?>"
                                                    onsubmit="return confirm('Yakin hapus role ini?');" style="margin:0">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <input type="hidden" name="id" value="<?php echo e($r->id); ?>">
                                                    <button type="submit" class="btn"
                                                        style="padding:8px 10px;background:#fff1f2;color:#991b1b;border-color:#fecdd3;white-space:nowrap">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr id="emptyRole">
                                        <td colspan="3" style="color:var(--text-muted);text-align:center">Belum ada
                                            data role.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="noResultsRole" class="no-results">Tidak ada data yang cocok dengan pencarian.</div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    // Tab switching
    const tabs = document.querySelectorAll('.tab');
    const panelUser = document.getElementById('panel-user');
    const panelBiro = document.getElementById('panel-biro');
    const panelJabatan = document.getElementById('panel-jabatan');
    const panelRole = document.getElementById('panel-role');

    tabs.forEach(t => t.addEventListener('click', () => {
    tabs.forEach(x => x.classList.remove('active'));
    t.classList.add('active');
    const key = t.getAttribute('data-tab');
    panelUser.classList.toggle('active', key === 'user');
    panelBiro.classList.toggle('active', key === 'biro');
    panelJabatan.classList.toggle('active', key === 'jabatan');
    panelRole.classList.toggle('active', key === 'role');
    }));

    // Search functionality for User
    const searchUser = document.getElementById('searchUser');
    const userRows = document.querySelectorAll('.user-row');
    const noResultsUser = document.getElementById('noResultsUser');
    const emptyUser = document.getElementById('emptyUser');

    if (searchUser && userRows.length > 0) {
    searchUser.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();
    let visibleCount = 0;

    userRows.forEach(row => {
    const text = row.textContent.toLowerCase();
    if (text.includes(searchTerm)) {
    row.style.display = '';
    visibleCount++;
    } else {
    row.style.display = 'none';
    }
    });

    if (visibleCount === 0) {
    noResultsUser.style.display = 'block';
    if (emptyUser) emptyUser.style.display = 'none';
    } else {
    noResultsUser.style.display = 'none';
    }
    });
    }

    // Search functionality for Biro
    const searchBiro = document.getElementById('searchBiro');
    const biroRows = document.querySelectorAll('.biro-row');
    const noResultsBiro = document.getElementById('noResultsBiro');
    const emptyBiro = document.getElementById('emptyBiro');

    if (searchBiro && biroRows.length > 0) {
    searchBiro.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();
    let visibleCount = 0;

    biroRows.forEach(row => {
    const text = row.textContent.toLowerCase();
    if (text.includes(searchTerm)) {
    row.style.display = '';
    visibleCount++;
    } else {
    row.style.display = 'none';
    }
    });

    if (visibleCount === 0) {
    noResultsBiro.style.display = 'block';
    if (emptyBiro) emptyBiro.style.display = 'none';
    } else {
    noResultsBiro.style.display = 'none';
    }
    });
    }

    // Search functionality for Jabatan
    const searchJabatan = document.getElementById('searchJabatan');
    const jabatanRows = document.querySelectorAll('.jabatan-row');
    const noResultsJabatan = document.getElementById('noResultsJabatan');
    const emptyJabatan = document.getElementById('emptyJabatan');

    if (searchJabatan && jabatanRows.length > 0) {
    searchJabatan.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();
    let visibleCount = 0;

    jabatanRows.forEach(row => {
    const text = row.textContent.toLowerCase();
    if (text.includes(searchTerm)) {
    row.style.display = '';
    visibleCount++;
    } else {
    row.style.display = 'none';
    }
    });

    if (visibleCount === 0) {
    noResultsJabatan.style.display = 'block';
    if (emptyJabatan) emptyJabatan.style.display = 'none';
    } else {
    noResultsJabatan.style.display = 'none';
    }
    });
    }

    // Search functionality for Role
    const searchRole = document.getElementById('searchRole');
    const roleRows = document.querySelectorAll('.role-row');
    const noResultsRole = document.getElementById('noResultsRole');
    const emptyRole = document.getElementById('emptyRole');

    if (searchRole && roleRows.length > 0) {
    searchRole.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();
    let visibleCount = 0;

    roleRows.forEach(row => {
    const text = row.textContent.toLowerCase();
    if (text.includes(searchTerm)) {
    row.style.display = '';
    visibleCount++;
    } else {
    row.style.display = 'none';
    }
    });

    if (visibleCount === 0) {
    noResultsRole.style.display = 'block';
    if (emptyRole) emptyRole.style.display = 'none';
    } else {
    noResultsRole.style.display = 'none';
    }
    });
    }
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kevannn\Documents\FILE MAGANG\AbsensiWika\resources\views/settings/index.blade.php ENDPATH**/ ?>