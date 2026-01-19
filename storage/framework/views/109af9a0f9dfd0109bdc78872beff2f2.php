<?php $__env->startSection('title', 'WG Absen — Kalender Kerja'); ?>

<?php $__env->startSection('styles'); ?>
    .layout{display:grid;grid-template-columns:260px 1fr;gap:16px;align-items:start;width:100%;min-width:0;max-width:100%}
    @media(max-width:900px){
    .layout{grid-template-columns:1fr}
    .tabs{flex-direction:row;gap:10px}
    .tab{flex:1;text-align:center}
    }
    main{min-width:0;max-width:100%}
    .panel{min-width:0;max-width:100%}
    .card,.sidebarCard{min-width:0;max-width:100%}
    .card{overflow:hidden;background:#fff;border-radius:18px;border:1px solid #e7eaf3;box-shadow:0 10px 35px
    rgba(35,45,120,.08);padding:18px}
    .tableScroll{width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch;border-radius:12px}
    .tableScroll table{min-width:720px;white-space:nowrap}
    h2,.tab{overflow-wrap:anywhere;word-break:break-word}
    .sidebarCard{background:#fff;border-radius:18px;border:1px solid #e7eaf3;box-shadow:0 10px 35px
    rgba(35,45,120,.08);padding:12px}
    .side-title{font-size:12px;color:var(--text-muted);margin:0 0 10px}
    .tabs{display:flex;flex-direction:column;gap:8px}
    .tab{width:100%;text-align:left;padding:10px 12px;border-radius:12px;border:1px solid
    #e9ecf5;background:#fff;color:#111;cursor:pointer;font-weight:700}
    .tab.active{background:var(--primary);border-color:var(--primary);color:#fff}
    .panel{display:none}
    .panel.active{display:block}
    .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
    @media(max-width:900px){.grid{grid-template-columns:1fr}}
    label{display:block;font-size:12px;color:var(--text-muted);margin-bottom:6px}
    select,input{width:100%;padding:11px 12px;border:1px solid
    #eef0f6;border-radius:12px;background:#fff;outline:none;font-size:14px}
    select:focus,input:focus{box-shadow:0 0 0 3px rgba(89,102,247,0.08);border-color:var(--primary)}
    .row2{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-top:14px}
    @media(max-width:900px){.row2{grid-template-columns:1fr}}
    .calendar{margin-top:14px;display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:8px}
    .cal-head{font-size:12px;color:var(--text-muted);text-align:center}
    .date{padding:10px 0;border:1px solid
    #eef0f6;border-radius:12px;background:#fff;text-align:center;cursor:pointer;user-select:none}
    .date.selected{background:rgba(89,102,247,0.12);border-color:rgba(89,102,247,0.5)}
    .actions{margin-top:16px;display:flex;gap:10px;justify-content:flex-end}
    .btn{padding:11px 14px;border-radius:12px;border:none;cursor:pointer;font-weight:800;background:#fff;border:1px solid
    #eef0f6}
    .btn.primary{background:var(--primary);border-color:var(--primary);color:#fff;box-shadow:0 6px 18px
    rgba(89,102,247,0.18)}
    table{width:100%;border-collapse:collapse}
    th,td{padding:10px 8px;border-bottom:1px solid #eef1ff;text-align:left;font-size:14px;white-space:nowrap}
    th{color:#111;font-size:12px;text-transform:uppercase;letter-spacing:.02em}
    .status{margin:10px 0 0;color:#065f46;background:#ecfdf5;border:1px solid #a7f3d0;padding:10px;border-radius:12px}
    .error{margin:10px 0 0;color:#991b1b;background:#fff1f2;border:1px solid #fecdd3;padding:10px;border-radius:12px}
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="layout">
        <aside class="sidebarCard">
            <div class="side-title">Menu</div>
            <div class="tabs" role="tablist">
                <button type="button" class="tab active" data-tab="form">Form Input Kalender Kerja</button>
                <button type="button" class="tab" data-tab="data">Data Kalender Kerja</button>
                <button type="button" class="tab" data-tab="libur">Kalender Libur</button>
            </div>
        </aside>
        <main>
            <div id="panel-form" class="panel active">
                <div class="card">
                    <h2 style="margin:0 0 12px">Input Kalender Kerja</h2>

                    <?php if(session('status')): ?>
                        <div class="status"><?php echo e(session('status')); ?></div>
                    <?php endif; ?>
                    <?php if($errors->any()): ?>
                        <div class="error"><?php echo e($errors->first()); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('admin.kalender.store')); ?>" id="kalenderForm">
                        <?php echo csrf_field(); ?>
                        <div class="grid">
                            <div>
                                <label for="minggu">Minggu ke-</label>
                                <select id="minggu" name="minggu" required>
                                    <?php for($i = 1; $i <= 6; $i++): ?>
                                        <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label for="tipe_persentase">Tipe Persentase</label>
                                <select id="tipe_persentase" name="tipe_persentase" required>
                                    <option value="wfo">WFO (Work From Office)</option>
                                    <option value="wfa">WFA (Work From Anywhere)</option>
                                </select>
                            </div>
                            <div>
                                <label for="nilai_persentase"><span id="label_persentase">WFO</span> Maksimal (%)</label>
                                <input type="number" id="nilai_persentase" name="nilai_persentase" min="0"
                                    max="100" step="0.01" required placeholder="Contoh: 50">
                                <div id="info_persentase" style="font-size:11px;color:var(--text-muted);margin-top:4px">
                                </div>
                            </div>
                            <div>
                                <label for="bulan">Bulan</label>
                                <select id="bulan" name="bulan" required></select>
                            </div>
                            <div>
                                <label for="tahun">Tahun</label>
                                <select id="tahun" name="tahun" required></select>
                            </div>
                        </div>

                        <div class="row2">
                            <div>
                                <label>Tanggal Awal</label>
                                <input type="text" id="tgl_awal_view" placeholder="Klik tanggal di kalender" readonly>
                                <input type="hidden" id="tgl_awal" name="tgl_awal" required>
                            </div>
                            <div>
                                <label>Tanggal Akhir</label>
                                <input type="text" id="tgl_akhir_view" placeholder="Klik tanggal di kalender" readonly>
                                <input type="hidden" id="tgl_akhir" name="tgl_akhir" required>
                            </div>
                        </div>

                        <div style="margin-top:14px">
                            <div
                                style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
                                <div>
                                    <strong id="calTitle">Kalender</strong>
                                    <div style="font-size:12px;color:var(--muted)" id="calSub"></div>
                                </div>
                                <div style="display:flex;gap:8px;flex-wrap:wrap">
                                    <button type="button" class="btn" id="prevMonth" title="Bulan sebelumnya">‹
                                        Bulan</button>
                                    <button type="button" class="btn" id="nextMonth" title="Bulan berikutnya">Bulan
                                        ›</button>
                                    <button type="button" class="btn" id="pickModeStart">Pilih Tanggal Awal</button>
                                    <button type="button" class="btn" id="pickModeEnd">Pilih Tanggal Akhir</button>
                                </div>
                            </div>

                            <div class="calendar" id="calendar">
                                <!-- header days -->
                            </div>
                        </div>

                        <div class="actions">
                            <button type="submit" class="btn primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="panel-data" class="panel">
                <div class="card">
                    <h2 style="margin:0 0 12px">Data Kalender Kerja</h2>
                    <?php if(session('status')): ?>
                        <div class="status"><?php echo e(session('status')); ?></div>
                    <?php endif; ?>
                    <!-- Search Box -->
                    <div style="margin-bottom:16px">
                        <input type="text" id="searchKalender"
                            placeholder="🔍 Cari kalender (minggu, bulan, tahun, WFO maks...)"
                            style="width:100%;padding:11px 12px;border:1px solid #eef0f6;border-radius:12px;background:#fff;outline:none;font-size:14px">
                    </div>

                    <div class="tableScroll">
                        <table id="tableKalender" width="100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kalender</th>
                                    <th>Tanggal Awal</th>
                                    <th>Tanggal Akhir</th>
                                    <th>WFO (%)</th>
                                    <th>WFA (%)</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="kalender-row">
                                        <td><?php echo e(($rows->currentPage() - 1) * $rows->perPage() + $loop->iteration); ?></td>
                                        <td><?php echo e($r->judul); ?></td>
                                        <td><?php echo e($r->tgl_awal ? $r->tgl_awal->format('Y-m-d') : ''); ?></td>
                                        <td><?php echo e($r->tgl_akhir ? $r->tgl_akhir->format('Y-m-d') : ''); ?></td>
                                        <td><?php echo e($r->persentase !== null ? $r->persentase . '%' : '-'); ?></td>
                                        <td><?php echo e($r->persentase_wfa !== null ? $r->persentase_wfa . '%' : '-'); ?></td>
                                        <td>
                                            <div style="display:flex;gap:8px;flex-wrap:wrap">
                                                <form method="POST" action="<?php echo e(route('kalender.setEdit')); ?>"
                                                    style="margin:0">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="id" value="<?php echo e($r->id); ?>">
                                                    <button type="submit" class="btn"
                                                        style="padding:8px 10px;border-radius:8px;border:1px solid #eef0f6;background:#fff">Edit</button>
                                                </form>

                                                <form method="POST" action="<?php echo e(route('kalender.broadcast')); ?>"
                                                    onsubmit="return confirm('Kirim notifikasi ke semua biro?');"
                                                    style="margin:0">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="id" value="<?php echo e($r->id); ?>">
                                                    <button type="submit" class="btn"
                                                        style="padding:8px 10px;border-radius:8px;border:1px solid #c7d2fe;background:#eef2ff;color:#4338ca"
                                                        title="Broadcast ke semua biro">📢 Broadcast</button>
                                                </form>

                                                <form method="POST" action="<?php echo e(route('kalender.setDelete')); ?>"
                                                    onsubmit="return confirm('Yakin hapus data ini?');" style="margin:0">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="id" value="<?php echo e($r->id); ?>">
                                                    <button type="submit" class="btn"
                                                        style="padding:8px 10px;border-radius:8px;border:1px solid #fecdd3;background:#fff1f2;color:#991b1b">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr id="emptyRow">
                                        <td colspan="7" style="color:var(--muted)">Belum ada data.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- No Results Message -->
                    <div id="noResults" style="display:none;text-align:center;padding:20px;color:var(--text-muted)">
                        Tidak ada data yang cocok dengan pencarian.
                    </div>

                    <!-- Pagination Kalender Kerja -->
                    <?php if($rows->total() > 0): ?>
                        <div style="text-align:center;margin-top:12px;font-size:12px;color:var(--text-muted)">
                            Menampilkan <?php echo e($rows->firstItem() ?? 0); ?> - <?php echo e($rows->lastItem() ?? 0); ?> dari
                            <?php echo e($rows->total()); ?> data
                        </div>
                    <?php endif; ?>

                    <?php if($rows->hasPages()): ?>
                        <div class="pagination-wrapper"
                            style="margin-top:12px;display:flex;justify-content:center;align-items:center;gap:8px;flex-wrap:wrap">
                            
                            <?php if($rows->onFirstPage()): ?>
                                <span class="btn" style="padding:8px 12px;opacity:0.5;cursor:not-allowed">‹ Prev</span>
                            <?php else: ?>
                                <button type="button" class="btn pagination-btn"
                                    data-page="<?php echo e($rows->currentPage() - 1); ?>" data-type="kalender"
                                    style="padding:8px 12px">‹ Prev</button>
                            <?php endif; ?>

                            
                            <?php
                                $start = max(1, $rows->currentPage() - 2);
                                $end = min($rows->lastPage(), $rows->currentPage() + 2);
                            ?>

                            <?php if($start > 1): ?>
                                <button type="button" class="btn pagination-btn" data-page="1" data-type="kalender"
                                    style="padding:8px 12px">1</button>
                                <?php if($start > 2): ?>
                                    <span style="padding:8px 4px;color:var(--text-muted)">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for($i = $start; $i <= $end; $i++): ?>
                                <?php if($i == $rows->currentPage()): ?>
                                    <span class="btn primary" style="padding:8px 12px"><?php echo e($i); ?></span>
                                <?php else: ?>
                                    <button type="button" class="btn pagination-btn" data-page="<?php echo e($i); ?>"
                                        data-type="kalender" style="padding:8px 12px"><?php echo e($i); ?></button>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if($end < $rows->lastPage()): ?>
                                <?php if($end < $rows->lastPage() - 1): ?>
                                    <span style="padding:8px 4px;color:var(--text-muted)">...</span>
                                <?php endif; ?>
                                <button type="button" class="btn pagination-btn" data-page="<?php echo e($rows->lastPage()); ?>"
                                    data-type="kalender" style="padding:8px 12px"><?php echo e($rows->lastPage()); ?></button>
                            <?php endif; ?>

                            
                            <?php if($rows->hasMorePages()): ?>
                                <button type="button" class="btn pagination-btn"
                                    data-page="<?php echo e($rows->currentPage() + 1); ?>" data-type="kalender"
                                    style="padding:8px 12px">Next ›</button>
                            <?php else: ?>
                                <span class="btn" style="padding:8px 12px;opacity:0.5;cursor:not-allowed">Next ›</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Panel Kalender Libur -->
            <div id="panel-libur" class="panel">
                <div class="card">
                    <h2 style="margin:0 0 12px">Kelola Tanggal Libur</h2>
                    <p style="color:var(--text-muted);font-size:14px;margin-bottom:16px">Klik tanggal pada kalender untuk
                        menandai sebagai hari libur. Tanggal libur akan otomatis menonaktifkan pilihan WFO/WFA pada
                        pengajuan.</p>

                    <?php if(session('status')): ?>
                        <div class="status"><?php echo e(session('status')); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('kalender.libur.store')); ?>" id="liburForm">
                        <?php echo csrf_field(); ?>

                        <div class="grid" style="grid-template-columns: repeat(2, 1fr);">
                            <div>
                                <label for="liburBulan">Bulan</label>
                                <select id="liburBulan"></select>
                            </div>
                            <div>
                                <label for="liburTahun">Tahun</label>
                                <select id="liburTahun"></select>
                            </div>
                        </div>

                        <div style="margin-top:14px">
                            <div
                                style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
                                <div>
                                    <strong id="liburCalTitle">Kalender</strong>
                                    <div style="font-size:12px;color:var(--muted)">Klik tanggal untuk memilih/batalkan
                                        sebagai hari libur</div>
                                </div>
                                <div style="display:flex;gap:8px;flex-wrap:wrap">
                                    <button type="button" class="btn" id="liburPrevMonth" title="Bulan sebelumnya">‹
                                        Bulan</button>
                                    <button type="button" class="btn" id="liburNextMonth"
                                        title="Bulan berikutnya">Bulan ›</button>
                                </div>
                            </div>

                            <div class="calendar" id="liburCalendar">
                                <!-- calendar days -->
                            </div>
                        </div>

                        <div style="margin-top:14px">
                            <label>Tanggal Terpilih:</label>
                            <div id="selectedDatesContainer"
                                style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;min-height:40px;padding:12px;background:#f8fafc;border-radius:12px;border:1px solid #eef0f6">
                                <span style="color:var(--text-muted);font-size:13px" id="noDateSelected">Belum ada tanggal
                                    dipilih</span>
                            </div>
                            <input type="hidden" name="tanggal[]" id="hiddenDates">
                        </div>

                        <div class="actions">
                            <button type="button" class="btn" id="clearSelectedDates" style="margin-right:8px">Hapus
                                Semua Pilihan</button>
                            <button type="submit" class="btn primary" id="submitLiburBtn" disabled>Simpan Tanggal
                                Libur</button>
                        </div>
                    </form>
                </div>

                <div class="card" style="margin-top:16px">
                    <h2 style="margin:0 0 12px">Data Tanggal Libur</h2>

                    <div style="margin-bottom:16px">
                        <input type="text" id="searchLibur" placeholder="🔍 Cari tanggal libur..."
                            style="width:100%;padding:11px 12px;border:1px solid #eef0f6;border-radius:12px;background:#fff;outline:none;font-size:14px">
                    </div>

                    <div class="tableScroll">
                        <table id="tableLibur" width="100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="liburTableBody">
                                <?php $__empty_1 = true; $__currentLoopData = $libur; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                        $dateObj = \Carbon\Carbon::parse($l->tanggal);
                                        $dayName = $dayNames[$dateObj->dayOfWeek];
                                    ?>
                                    <tr class="libur-row">
                                        <td><?php echo e(($libur->currentPage() - 1) * $libur->perPage() + $loop->iteration); ?></td>
                                        <td><?php echo e($dateObj->format('d F Y')); ?></td>
                                        <td><?php echo e($dayName); ?></td>
                                        <td>
                                            <button type="button" class="btn delete-libur-btn"
                                                data-id="<?php echo e($l->id); ?>"
                                                style="padding:8px 10px;border-radius:8px;border:1px solid #fecdd3;background:#fff1f2;color:#991b1b">Hapus</button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr id="emptyLiburRow">
                                        <td colspan="4" style="text-align:center;color:var(--text-muted)">Belum ada
                                            data tanggal libur.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div id="noResultsLibur" style="display:none;text-align:center;padding:20px;color:var(--text-muted)">
                        Tidak ada data yang cocok dengan pencarian.
                    </div>

                    <!-- Info Total Data Libur -->
                    <?php if($libur->total() > 0): ?>
                        <div style="text-align:center;margin-top:12px;font-size:12px;color:var(--text-muted)">
                            Menampilkan <?php echo e($libur->firstItem() ?? 0); ?> - <?php echo e($libur->lastItem() ?? 0); ?> dari
                            <?php echo e($libur->total()); ?> data
                        </div>
                    <?php endif; ?>

                    <!-- Pagination Kalender Libur -->
                    <?php if($libur->hasPages()): ?>
                        <div class="pagination-wrapper"
                            style="margin-top:12px;display:flex;justify-content:center;align-items:center;gap:8px;flex-wrap:wrap">
                            
                            <?php if($libur->onFirstPage()): ?>
                                <span class="btn" style="padding:8px 12px;opacity:0.5;cursor:not-allowed">‹ Prev</span>
                            <?php else: ?>
                                <button type="button" class="btn pagination-btn"
                                    data-page="<?php echo e($libur->currentPage() - 1); ?>" data-type="libur"
                                    style="padding:8px 12px">‹ Prev</button>
                            <?php endif; ?>

                            
                            <?php
                                $startL = max(1, $libur->currentPage() - 2);
                                $endL = min($libur->lastPage(), $libur->currentPage() + 2);
                            ?>

                            <?php if($startL > 1): ?>
                                <button type="button" class="btn pagination-btn" data-page="1" data-type="libur"
                                    style="padding:8px 12px">1</button>
                                <?php if($startL > 2): ?>
                                    <span style="padding:8px 4px;color:var(--text-muted)">...</span>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for($i = $startL; $i <= $endL; $i++): ?>
                                <?php if($i == $libur->currentPage()): ?>
                                    <span class="btn primary" style="padding:8px 12px"><?php echo e($i); ?></span>
                                <?php else: ?>
                                    <button type="button" class="btn pagination-btn" data-page="<?php echo e($i); ?>"
                                        data-type="libur" style="padding:8px 12px"><?php echo e($i); ?></button>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if($endL < $libur->lastPage()): ?>
                                <?php if($endL < $libur->lastPage() - 1): ?>
                                    <span style="padding:8px 4px;color:var(--text-muted)">...</span>
                                <?php endif; ?>
                                <button type="button" class="btn pagination-btn" data-page="<?php echo e($libur->lastPage()); ?>"
                                    data-type="libur" style="padding:8px 12px"><?php echo e($libur->lastPage()); ?></button>
                            <?php endif; ?>

                            
                            <?php if($libur->hasMorePages()): ?>
                                <button type="button" class="btn pagination-btn"
                                    data-page="<?php echo e($libur->currentPage() + 1); ?>" data-type="libur"
                                    style="padding:8px 12px">Next ›</button>
                            <?php else: ?>
                                <span class="btn" style="padding:8px 12px;opacity:0.5;cursor:not-allowed">Next ›</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    // --- Tabs ---
    const tabs = document.querySelectorAll('.tab');
    const panelForm = document.getElementById('panel-form');
    const panelData = document.getElementById('panel-data');
    const panelLibur = document.getElementById('panel-libur');
    
    tabs.forEach(t => t.addEventListener('click', () => {
        tabs.forEach(x => x.classList.remove('active'));
        t.classList.add('active');
        const key = t.getAttribute('data-tab');
        panelForm.classList.toggle('active', key === 'form');
        panelData.classList.toggle('active', key === 'data');
        panelLibur.classList.toggle('active', key === 'libur');
    }));

    // --- Dropdown bulan & tahun ---
    const bulanEl = document.getElementById('bulan');
    const tahunEl = document.getElementById('tahun');
    const mingguEl = document.getElementById('minggu');

    const bulanNames = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    function initBulan() {
        bulanEl.innerHTML = '';
        bulanNames.forEach((n, i) => {
            const opt = document.createElement('option');
            opt.value = String(i + 1);
            opt.textContent = n;
            bulanEl.appendChild(opt);
        });
    }

    function initTahun() {
        const now = new Date();
        const y = now.getFullYear();
        const years = [y - 1, y, y + 1, y + 2];
        tahunEl.innerHTML = '';
        years.forEach(yy => {
            const opt = document.createElement('option');
            opt.value = String(yy);
            opt.textContent = String(yy);
            tahunEl.appendChild(opt);
        });
        tahunEl.value = String(y);
    }

    // --- Persentase WFO/WFA ---
    const tipePersentase = document.getElementById('tipe_persentase');
    const nilaiPersentase = document.getElementById('nilai_persentase');
    const labelPersentase = document.getElementById('label_persentase');
    const infoPersentase = document.getElementById('info_persentase');

    function updatePersentaseInfo() {
        const tipe = tipePersentase.value;
        if (tipe === 'wfo') {
            labelPersentase.textContent = 'WFO';
        } else {
            labelPersentase.textContent = 'WFA';
        }
    }

    tipePersentase.addEventListener('change', updatePersentaseInfo);
    nilaiPersentase.addEventListener('input', updatePersentaseInfo);
    updatePersentaseInfo();

    // --- Kalender (grid bulanan) ---
    const cal = document.getElementById('calendar');
    const calTitle = document.getElementById('calTitle');
    const calSub = document.getElementById('calSub');
    const tglAwalHidden = document.getElementById('tgl_awal');
    const tglAkhirHidden = document.getElementById('tgl_akhir');
    const tglAwalView = document.getElementById('tgl_awal_view');
    const tglAkhirView = document.getElementById('tgl_akhir_view');
    const pickModeStart = document.getElementById('pickModeStart');
    const pickModeEnd = document.getElementById('pickModeEnd');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    let pickMode = 'start';

    function setPickMode(mode) {
        pickMode = mode;
        pickModeStart.classList.toggle('primary', mode === 'start');
        pickModeEnd.classList.toggle('primary', mode === 'end');

        const activeShadow = '0 0 0 3px rgba(89,102,247,0.10)';
        const inactiveShadow = '';
        const activeBorder = '1px solid rgba(89,102,247,0.55)';
        const inactiveBorder = '';
        
        if (mode === 'start') {
            tglAwalView.style.boxShadow = activeShadow;
            tglAwalView.style.border = activeBorder;
            tglAkhirView.style.boxShadow = inactiveShadow;
            tglAkhirView.style.border = inactiveBorder;
        } else {
            tglAkhirView.style.boxShadow = activeShadow;
            tglAkhirView.style.border = activeBorder;
            tglAwalView.style.boxShadow = inactiveShadow;
            tglAwalView.style.border = inactiveBorder;
        }
    }

    pickModeStart.addEventListener('click', () => setPickMode('start'));
    pickModeEnd.addEventListener('click', () => setPickMode('end'));

    // UX: klik input otomatis ganti mode
    [tglAwalView, tglAwalHidden].forEach(el => {
        el.addEventListener('click', () => setPickMode('start'));
        el.addEventListener('focus', () => setPickMode('start'));
    });
    
    [tglAkhirView, tglAkhirHidden].forEach(el => {
        el.addEventListener('click', () => setPickMode('end'));
        el.addEventListener('focus', () => setPickMode('end'));
    });
    
    setPickMode('start');

    function pad2(n) {
        return String(n).padStart(2, '0');
    }

    function fmtISO(y, m, d) {
        return `${y}-${pad2(m)}-${pad2(d)}`;
    }

    function ymd(date) {
        return `${date.getFullYear()}-${pad2(date.getMonth()+1)}-${pad2(date.getDate())}`;
    }

    let viewYear = null;
    let viewMonth = null;
    let suppressDropdownChange = false;

    function setViewMonth(year, month) {
        viewYear = year;
        viewMonth = month;
    }

    function syncDropdownToView() {
        suppressDropdownChange = true;
        bulanEl.value = String(viewMonth);
        tahunEl.value = String(viewYear);
        suppressDropdownChange = false;
    }

    function shiftViewMonth(delta) {
        const d = new Date(viewYear, viewMonth - 1, 1);
        d.setMonth(d.getMonth() + delta);
        setViewMonth(d.getFullYear(), d.getMonth() + 1);
        syncDropdownToView();
        buildCalendar();
    }

    prevMonthBtn.addEventListener('click', () => shiftViewMonth(-1));
    nextMonthBtn.addEventListener('click', () => shiftViewMonth(1));

    function buildCalendar() {
        const year = parseInt(tahunEl.value, 10);
        const month = parseInt(bulanEl.value, 10);

        if (viewYear === null || viewMonth === null) {
            setViewMonth(year, month);
        }

        calTitle.textContent = `Kalender ${bulanNames[viewMonth-1]} ${viewYear}`;
        calSub.textContent = `Klik tanggal untuk mengisi Tanggal Awal / Tanggal Akhir`;

        const heads = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        cal.innerHTML = '';
        heads.forEach(h => {
            const el = document.createElement('div');
            el.className = 'cal-head';
            el.textContent = h;
            cal.appendChild(el);
        });

        const firstOfView = new Date(viewYear, viewMonth - 1, 1);
        const daysInView = new Date(viewYear, viewMonth, 0).getDate();
        const firstIdx = firstOfView.getDay();
        const weeksInView = Math.ceil((firstIdx + daysInView) / 7);

        const gridStart = new Date(viewYear, viewMonth - 1, 1 - firstIdx);
        const totalCells = weeksInView * 7;

        for (let i = 0; i < totalCells; i++) {
            const date = new Date(gridStart);
            date.setDate(gridStart.getDate() + i);
            const el = document.createElement('div');
            el.className = 'date';
            el.textContent = String(date.getDate());
            
            if (date.getMonth() + 1 !== viewMonth || date.getFullYear() !== viewYear) {
                el.style.opacity = '0.55';
            }
            
            const iso = ymd(date);
            if (tglAwalHidden.value === iso || tglAkhirHidden.value === iso) {
                el.classList.add('selected');
            }
            
            el.addEventListener('click', () => {
                if ((date.getMonth() + 1) !== viewMonth || date.getFullYear() !== viewYear) {
                    setViewMonth(date.getFullYear(), date.getMonth() + 1);
                    syncDropdownToView();
                }

                if (pickMode === 'start') {
                    tglAwalHidden.value = iso;
                    tglAwalView.value = iso;
                    if (tglAkhirHidden.value && tglAkhirHidden.value < tglAwalHidden.value) {
                        tglAkhirHidden.value = '';
                        tglAkhirView.value = '';
                    }
                } else {
                    if (tglAwalHidden.value && iso < tglAwalHidden.value) {
                        alert('Tanggal akhir harus lebih besar atau sama dengan tanggal awal.');
                        return;
                    }
                    tglAkhirHidden.value = iso;
                    tglAkhirView.value = iso;
                }
                buildCalendar();
            });
            cal.appendChild(el);
        }
    }

    function resetDates() {
        tglAwalHidden.value = '';
        tglAkhirHidden.value = '';
        tglAwalView.value = '';
        tglAkhirView.value = '';
    }

    // Initialize
    initBulan();
    initTahun();
    const now = new Date();
    bulanEl.value = String(now.getMonth() + 1);
    setViewMonth(parseInt(tahunEl.value, 10), parseInt(bulanEl.value, 10));
    
    [bulanEl, tahunEl, mingguEl].forEach(el => el.addEventListener('change', () => {
        if (suppressDropdownChange) return;
        resetDates();
        setViewMonth(parseInt(tahunEl.value, 10), parseInt(bulanEl.value, 10));
        buildCalendar();
    }));

    buildCalendar();

    // --- Search Functionality ---
    const searchInput = document.getElementById('searchKalender');
    const tableRows = document.querySelectorAll('.kalender-row');
    const noResults = document.getElementById('noResults');
    const emptyRow = document.getElementById('emptyRow');

    if (searchInput && tableRows.length > 0) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;

            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                noResults.style.display = 'block';
                if (emptyRow) emptyRow.style.display = 'none';
            } else {
                noResults.style.display = 'none';
            }
        });
    }

    // =====================
    // KALENDER LIBUR
    // =====================

    const hariNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    let selectedLiburDates = [];
    let liburViewYear = null;
    let liburViewMonth = null;

    const liburBulanEl = document.getElementById('liburBulan');
    const liburTahunEl = document.getElementById('liburTahun');
    const liburCalEl = document.getElementById('liburCalendar');
    const liburCalTitle = document.getElementById('liburCalTitle');

    function initLiburBulan() {
        liburBulanEl.innerHTML = '';
        bulanNames.forEach((n, i) => {
            const opt = document.createElement('option');
            opt.value = String(i + 1);
            opt.textContent = n;
            liburBulanEl.appendChild(opt);
        });
    }

    function initLiburTahun() {
        const now = new Date();
        const y = now.getFullYear();
        const years = [y - 1, y, y + 1, y + 2];
        liburTahunEl.innerHTML = '';
        years.forEach(yy => {
            const opt = document.createElement('option');
            opt.value = String(yy);
            opt.textContent = String(yy);
            liburTahunEl.appendChild(opt);
        });
        liburTahunEl.value = String(y);
    }

    function setLiburViewMonth(year, month) {
        liburViewYear = year;
        liburViewMonth = month;
    }

    function syncLiburDropdownToView() {
        liburBulanEl.value = String(liburViewMonth);
        liburTahunEl.value = String(liburViewYear);
    }

    function shiftLiburViewMonth(delta) {
        const d = new Date(liburViewYear, liburViewMonth - 1, 1);
        d.setMonth(d.getMonth() + delta);
        setLiburViewMonth(d.getFullYear(), d.getMonth() + 1);
        syncLiburDropdownToView();
        buildLiburCalendar();
    }

    document.getElementById('liburPrevMonth').addEventListener('click', () => shiftLiburViewMonth(-1));
    document.getElementById('liburNextMonth').addEventListener('click', () => shiftLiburViewMonth(1));

    function buildLiburCalendar() {
        if (liburViewYear === null || liburViewMonth === null) {
            const now = new Date();
            setLiburViewMonth(now.getFullYear(), now.getMonth() + 1);
        }

        liburCalTitle.textContent = `Kalender ${bulanNames[liburViewMonth-1]} ${liburViewYear}`;

        const heads = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        liburCalEl.innerHTML = '';
        heads.forEach(h => {
            const el = document.createElement('div');
            el.className = 'cal-head';
            el.textContent = h;
            liburCalEl.appendChild(el);
        });

        const firstOfView = new Date(liburViewYear, liburViewMonth - 1, 1);
        const daysInView = new Date(liburViewYear, liburViewMonth, 0).getDate();
        const firstIdx = firstOfView.getDay();
        const weeksInView = Math.ceil((firstIdx + daysInView) / 7);

        const gridStart = new Date(liburViewYear, liburViewMonth - 1, 1 - firstIdx);
        const totalCells = weeksInView * 7;

        for (let i = 0; i < totalCells; i++) {
            const date = new Date(gridStart);
            date.setDate(gridStart.getDate() + i);
            const el = document.createElement('div');
            el.className = 'date';
            el.textContent = String(date.getDate());
            const iso = ymd(date);
            
            if (date.getMonth() + 1 !== liburViewMonth || date.getFullYear() !== liburViewYear) {
                el.style.opacity = '0.55';
            }
            
            if (selectedLiburDates.includes(iso)) {
                el.classList.add('selected');
                el.style.background = '#fee2e2';
                el.style.borderColor = '#fca5a5';
                el.style.color = '#991b1b';
            }
            
            el.addEventListener('click', () => {
                toggleLiburDate(iso, el);
            });

            liburCalEl.appendChild(el);
        }
    }

    function toggleLiburDate(iso, el) {
        const index = selectedLiburDates.indexOf(iso);
        if (index > -1) {
            selectedLiburDates.splice(index, 1);
            el.classList.remove('selected');
            el.style.background = '';
            el.style.borderColor = '';
            el.style.color = '';
        } else {
            selectedLiburDates.push(iso);
            el.classList.add('selected');
            el.style.background = '#fee2e2';
            el.style.borderColor = '#fca5a5';
            el.style.color = '#991b1b';
        }
        updateSelectedDatesDisplay();
    }

    function updateSelectedDatesDisplay() {
        const container = document.getElementById('selectedDatesContainer');
        const submitBtn = document.getElementById('submitLiburBtn');

        if (selectedLiburDates.length === 0) {
            container.innerHTML = '<span style="color:var(--text-muted);font-size:13px" id="noDateSelected">Belum ada tanggal dipilih</span>';
            submitBtn.disabled = true;
            return;
        }

        const sorted = [...selectedLiburDates].sort();

        container.innerHTML = sorted.map(d => {
            const date = new Date(d);
            const hari = hariNames[date.getDay()];
            return `
            <span style="display:inline-flex;align-items:center;gap:6px;padding:6px 10px;background:#fee2e2;color:#991b1b;border-radius:8px;font-size:13px;border:1px solid #fca5a5">
                ${d} (${hari})
                <button type="button" onclick="removeLiburDate('${d}')" style="background:none;border:none;color:#991b1b;cursor:pointer;font-weight:bold;padding:0;line-height:1">✕</button>
            </span>
            `;
        }).join('');

        submitBtn.disabled = false;
    }

    function removeLiburDate(iso) {
        const index = selectedLiburDates.indexOf(iso);
        if (index > -1) {
            selectedLiburDates.splice(index, 1);
            buildLiburCalendar();
            updateSelectedDatesDisplay();
        }
    }

    document.getElementById('clearSelectedDates').addEventListener('click', () => {
        selectedLiburDates = [];
        buildLiburCalendar();
        updateSelectedDatesDisplay();
    });

    document.getElementById('liburForm').addEventListener('submit', function(e) {
        if (selectedLiburDates.length === 0) {
            e.preventDefault();
            alert('Pilih minimal 1 tanggal libur.');
            return;
        }

        this.querySelectorAll('input[name="tanggal[]"]').forEach(el => el.remove());

        selectedLiburDates.forEach(d => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tanggal[]';
            input.value = d;
            this.appendChild(input);
        });
    });

    [liburBulanEl, liburTahunEl].forEach(el => el.addEventListener('change', () => {
        setLiburViewMonth(parseInt(liburTahunEl.value, 10), parseInt(liburBulanEl.value, 10));
        buildLiburCalendar();
    }));

    async function deleteLibur(id) {
        if (!confirm('Yakin hapus tanggal libur ini?')) return;

        try {
            const response = await fetch(`<?php echo e(url('/kalender-kerja/libur')); ?>/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                }
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = '<?php echo e(route('admin.kalender')); ?>?tab=libur';
            } else {
                alert(result.message || 'Gagal menghapus tanggal libur.');
            }
        } catch (error) {
            console.error('Error deleting libur:', error);
            alert('Gagal menghapus tanggal libur.');
        }
    }

    document.querySelectorAll('.delete-libur-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            deleteLibur(id);
        });
    });

    const searchLiburInput = document.getElementById('searchLibur');
    if (searchLiburInput) {
        searchLiburInput.addEventListener('input', function() {
            const term = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.libur-row');
            const noResultsLibur = document.getElementById('noResultsLibur');
            let visibleCount = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(term)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const totalRows = document.querySelectorAll('.libur-row').length;
            noResultsLibur.style.display = visibleCount === 0 && totalRows > 0 ? 'block' : 'none';
        });
    }

    document.querySelectorAll('.pagination-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const page = this.getAttribute('data-page');
            const type = this.getAttribute('data-type');

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo e(route('admin.kalender.page')); ?>';
            form.style.display = 'none';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '<?php echo e(csrf_token()); ?>';
            form.appendChild(csrf);

            const pageInput = document.createElement('input');
            pageInput.type = 'hidden';
            pageInput.name = type === 'kalender' ? 'p' : 'lp';
            pageInput.value = page;
            form.appendChild(pageInput);

            const tabInput = document.createElement('input');
            tabInput.type = 'hidden';
            tabInput.name = 'tab';
            tabInput.value = type === 'kalender' ? 'data' : 'libur';
            form.appendChild(tabInput);

            document.body.appendChild(form);
            form.submit();
        });
    });

    const activeTab = '<?php echo e($activeTab ?? 'form'); ?>';
    if (activeTab && activeTab !== 'form') {
        tabs.forEach(t => {
            const key = t.getAttribute('data-tab');
            t.classList.toggle('active', key === activeTab);
        });
        panelForm.classList.toggle('active', activeTab === 'form');
        panelData.classList.toggle('active', activeTab === 'data');
        panelLibur.classList.toggle('active', activeTab === 'libur');
    }

    // Initialize libur calendar
    initLiburBulan();
    initLiburTahun();
    const nowLibur = new Date();
    liburBulanEl.value = String(nowLibur.getMonth() + 1);
    setLiburViewMonth(parseInt(liburTahunEl.value, 10), parseInt(liburBulanEl.value, 10));
    buildLiburCalendar();
    updateSelectedDatesDisplay();

    // Make functions available globally
    window.removeLiburDate = removeLiburDate;
    window.deleteLibur = deleteLibur;
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kevannn\Documents\FILE MAGANG\AbsensiWika\resources\views/admin/kalender/index.blade.php ENDPATH**/ ?>