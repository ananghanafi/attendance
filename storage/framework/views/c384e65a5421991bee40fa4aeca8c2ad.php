

<?php $__env->startSection('title', 'WG Absen — Pengajuan WFO'); ?>

<?php $__env->startSection('styles'); ?>
<style>
  .page-title {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 32px;
    color: var(--text);
  }

  .card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e7eaf3;
    box-shadow: 0 4px 20px rgba(35, 45, 120, .08);
    padding: 24px;
    margin-bottom: 24px;
  }

  .card.pagination-card {
    padding: 16px 24px;
    margin-bottom: 0;
  }

  .card-header {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
    color: var(--text);
  }

  .filters {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: flex-end;
  }

  .filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 180px;
    flex: 1;
  }

  .filter-group.buttons {
    flex-direction: row;
    min-width: auto;
    flex: 0;
    gap: 8px;
    align-items: flex-end;
  }

  .filter-group label {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 500
  }

  .filter-group input,
  .filter-group select {
    padding: 10px 12px;
    border: 1px solid #eef0f6;
    border-radius: 10px;
    font-size: 14px;
    outline: none;
    background: #fff;
    width: 100%;
    box-sizing: border-box;
  }

  .filter-group input:focus,
  .filter-group select:focus {
    box-shadow: 0 0 0 3px rgba(89, 102, 247, 0.08);
    border-color: var(--primary)
  }

  .btn {
    padding: 10px 16px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-block
  }

  .btn.primary {
    background: var(--primary);
    color: #fff;
    box-shadow: 0 4px 12px rgba(89, 102, 247, 0.2)
  }

  .btn.primary:hover {
    background: var(--primary-dark)
  }

  .btn.secondary {
    background: #fff;
    color: var(--text);
    border: 1px solid #eef0f6
  }

  .btn.secondary:hover {
    background: #f9fafb
  }

  .tableScroll {
    overflow-x: auto
  }

  table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1000px
  }

  thead {
    background: #f9fafb
  }

  th {
    padding: 14px 16px;
    text-align: left;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-muted);
    border-bottom: 2px solid #eef0f6;
    white-space: nowrap
  }

  td {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px
  }

  tr:hover {
    background: #f9fafb
  }

  .badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600
  }

  .badge.success {
    background: #d1fae5;
    color: #065f46
  }

  .badge.warning {
    background: #fef3c7;
    color: #92400e
  }

  .badge.danger {
    background: #fee2e2;
    color: #991b1b
  }

  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap
  }

  .pagination a,
  .pagination span {
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #eef0f6;
    text-decoration: none;
    color: var(--text);
    font-size: 14px;
    background: #fff
  }

  .pagination a:hover {
    background: #f3f4f6
  }

  .pagination .active {
    background: var(--primary);
    color: #fff;
    border-color: var(--primary)
  }

  .empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-muted)
  }

  .alert {
    padding: 12px 16px;
    border-radius: 10px;
    margin-bottom: 16px
  }

  .alert-success {
    background: #d1fae5;
    color: #065f46
  }

  .alert-error {
    background: #fee2e2;
    color: #991b1b
  }

  /* Mobile Responsive */
  @media(max-width:768px) {
    .page-title {
      font-size: 20px;
      margin-bottom: 16px;
    }

    .card {
      padding: 16px;
      margin-bottom: 16px;
    }

    .filters {
      flex-direction: column;
      gap: 12px;
    }

    .filter-group {
      min-width: 100%;
      width: 100%;
    }

    .filter-group.buttons {
      flex-direction: row;
      width: 100%;
      justify-content: flex-start;
    }

    .filter-group.buttons .btn {
      flex: 1;
      text-align: center;
    }
  }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<h1 class="page-title">PENGAJUAN PEGAWAI WFO</h1>

<?php if(session('status')): ?>
<div class="alert alert-success"><?php echo e(session('status')); ?></div>
<?php endif; ?>

<?php if(session('error')): ?>
<div class="alert alert-error"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<!-- Card Filter -->
<div class="card">
  <form method="GET" action="<?php echo e(route('pengajuan.index')); ?>" id="filterForm">
    <div class="filters">
      <div class="filter-group">
        <label>🔍 Cari Biro</label>
        <input
          type="text"
          name="search"
          id="searchBiro"
          value="<?php echo e($filters['search'] ?? ''); ?>"
          placeholder="Ketik nama biro..."
          autocomplete="off"
          style="padding:11px 12px">
      </div>

      <div class="filter-group">
        <label>📅 Minggu</label>
        <select name="minggu" style="padding:11px 12px">
          <option value="">Semua Minggu</option>
          <?php for($i = 1; $i <= 6; $i++): ?>
            <option value="<?php echo e($i); ?>" <?php echo e(($filters['minggu'] ?? '') == $i ? 'selected' : ''); ?>>
            Minggu <?php echo e($i); ?>

            </option>
            <?php endfor; ?>
        </select>
      </div>

      <div class="filter-group">
        <label>📆 Bulan</label>
        <select name="bulan" style="padding:11px 12px">
          <option value="">Semua Bulan</option>
          <?php
          $bulanNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
          ?>
          <?php for($i = 1; $i <= 12; $i++): ?>
            <option value="<?php echo e($i); ?>" <?php echo e(($filters['bulan'] ?? '') == $i ? 'selected' : ''); ?>>
            <?php echo e($bulanNames[$i]); ?>

            </option>
            <?php endfor; ?>
        </select>
      </div>

      <div class="filter-group">
        <label>🗓️ Tahun</label>
        <input
          type="number"
          name="tahun"
          id="searchTahun"
          value="<?php echo e($filters['tahun'] ?? ''); ?>"
          placeholder="Ketik tahun..."
          autocomplete="off"
          style="padding:11px 12px">
      </div>

      <div class="filter-group buttons">
        <button type="submit" class="btn primary" style="padding:11px 20px;">
          Filter
        </button>
        <a href="<?php echo e(route('pengajuan.index')); ?>" class="btn secondary" style="padding:11px 20px;">
          Reset
        </a>
      </div>
    </div>
  </form>
</div>

<!-- Card Table -->
<div class="card">
  <?php if($canBroadcast ?? false): ?>
  <!-- Broadcast Multiple Button -->
  <form id="broadcastForm" action="<?php echo e(route('pengajuan.broadcastMultiple')); ?>" method="POST" style="margin-bottom:16px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
    <?php echo csrf_field(); ?>
    <div id="hiddenIdsContainer"></div>
    <button type="submit" class="btn" style="padding:10px 16px;border:1px solid #c7d2fe;background:#eef2ff;color:#4338ca" onclick="return confirmBroadcast()">
      📢 Broadcast Terpilih
    </button>
    <span id="selectedCount" style="font-size:13px;color:var(--text-muted)">0 biro dipilih</span>
    <button type="button" class="btn secondary" style="padding:8px 12px;font-size:12px" onclick="clearSelection()">
      ✕ Hapus Pilihan
    </button>
  </form>
  <?php endif; ?>

  <div class="tableScroll">
    <table>
      <thead>
        <tr>
          <?php if($canBroadcast ?? false): ?>
          <th style="width:40px;text-align:center">
            <input type="checkbox" id="selectAll" title="Pilih Semua" style="cursor:pointer">
          </th>
          <?php endif; ?>
          <th>Biro</th>
          <th>Bulan</th>
          <th>Tahun</th>
          <th>Minggu</th>
          <th>Tanggal Mulai</th>
          <th>Tanggal Selesai</th>
          <th>WFO (%)</th>
          <th>WFA (%)</th>
          <th style="text-align:center">Pembuatan<br>Schedule</th>
          <th style="text-align:center">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $pengajuans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="pengajuan-row">
          <?php if($canBroadcast ?? false): ?>
          <td style="text-align:center">
            <?php if($p->canEdit): ?>
            <input type="checkbox" name="ids[]" value="<?php echo e($p->id); ?>" class="broadcast-checkbox" form="broadcastForm" data-biro="<?php echo e($p->biro_name); ?>" style="cursor:pointer">
            <?php endif; ?>
          </td>
          <?php endif; ?>
          <td><?php echo e($p->biro_name); ?></td>
          <td>
            <?php
            $bulanNames = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            echo $bulanNames[$p->bulan ?? 0] ?? '-';
            ?>
          </td>
          <td><?php echo e($p->tahun ?? '-'); ?></td>
          <td><?php echo e($p->minggu ?? '-'); ?></td>
          <td><?php echo e($p->tgl_awal ? \Carbon\Carbon::parse($p->tgl_awal)->format('d-M-Y') : '-'); ?></td>
          <td><?php echo e($p->tgl_akhir ? \Carbon\Carbon::parse($p->tgl_akhir)->format('d-M-Y') : '-'); ?></td>
          <td><?php echo e($p->persentase !== null ? $p->persentase . '%' : '-'); ?></td>
          <td><?php echo e($p->persentase_wfa !== null ? $p->persentase_wfa . '%' : '-'); ?></td>
          <td style="text-align:center">
            <?php if(strtolower($p->status ?? '') == 'final'): ?>
            <span class="badge success">Final</span>
            <?php else: ?>
            <span class="badge warning">Draft</span>
            <?php endif; ?>
          </td>
          <td style="text-align:center">
            <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap">
              <form action="<?php echo e(route('pengajuan.setView')); ?>" method="POST" style="display:inline">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo e($p->id); ?>">
                <button type="submit" class="btn secondary" style="padding:8px 16px">View</button>
              </form>
              <?php
                // Admin/VP/HC bisa edit kapan saja selama periode aktif
                // User biasa HANYA bisa edit via magic link (tidak dari halaman ini)
                $canUserEdit = $p->canEdit && ($canEdit ?? false);
              ?>
              <?php if($canUserEdit): ?>
              <form action="<?php echo e(route('pengajuan.setEdit')); ?>" method="POST" style="display:inline">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo e($p->id); ?>">
                <button type="submit" class="btn primary" style="padding:8px 16px">Edit</button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr id="emptyRow">
          <td colspan="<?php echo e(($canBroadcast ?? false) ? 11 : 10); ?>" class="empty">
            <?php if(!empty($filters['search'])): ?>
            Tidak ada hasil yang cocok dengan pencarian "<?php echo e($filters['search']); ?>".
            <?php else: ?>
            Belum ada data pengajuan WFO.
            <?php endif; ?>
          </td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if($pengajuans->hasPages()): ?>
<div class="card pagination-card">
  <div class="pagination">
    
    <?php if($pengajuans->onFirstPage()): ?>
    <span style="opacity:0.5">‹ Prev</span>
    <?php else: ?>
    <a href="<?php echo e($pengajuans->previousPageUrl()); ?>">‹ Prev</a>
    <?php endif; ?>

    
    <?php
    $currentPage = $pengajuans->currentPage();
    $lastPage = $pengajuans->lastPage();
    $maxVisible = 10;

    // Calculate start and end of visible page range
    $start = max(1, $currentPage - floor($maxVisible / 2));
    $end = min($lastPage, $start + $maxVisible - 1);

    // Adjust start if we're near the end
    if ($end - $start + 1 < $maxVisible) {
      $start=max(1, $end - $maxVisible + 1);
      }
      ?>

      
      <?php if($start> 1): ?>
      <a href="<?php echo e($pengajuans->url(1)); ?>">1</a>
      <?php if($start > 2): ?>
      <span style="opacity:0.5">...</span>
      <?php endif; ?>
      <?php endif; ?>

      
      <?php for($page = $start; $page <= $end; $page++): ?>
        <?php if($page==$currentPage): ?>
        <span class="active"><?php echo e($page); ?></span>
        <?php else: ?>
        <a href="<?php echo e($pengajuans->url($page)); ?>"><?php echo e($page); ?></a>
        <?php endif; ?>
        <?php endfor; ?>

        
        <?php if($end < $lastPage): ?>
          <?php if($end < $lastPage - 1): ?>
          <span style="opacity:0.5">...</span>
          <?php endif; ?>
          <a href="<?php echo e($pengajuans->url($lastPage)); ?>"><?php echo e($lastPage); ?></a>
          <?php endif; ?>

          
          <?php if($pengajuans->hasMorePages()): ?>
          <a href="<?php echo e($pengajuans->nextPageUrl()); ?>">Next ›</a>
          <?php else: ?>
          <span style="opacity:0.5">Next ›</span>
          <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<script>
  // Server-side search with debounce (500ms delay)
  const searchInput = document.getElementById('searchBiro');
  const filterForm = document.getElementById('filterForm');
  let searchTimeout;

  if (searchInput) {
    searchInput.addEventListener('input', function() {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(function() {
        filterForm.submit();
      }, 500);
    });
  }

  <?php if($canBroadcast ?? false): ?>
  // All eligible IDs from server (across all pages)
  const allEligibleData = <?php echo json_encode($allEligibleIds ?? [], 15, 512) ?>;
  const STORAGE_KEY = 'pengajuan_broadcast_selection';

  // Get selected IDs from localStorage
  function getSelectedIds() {
    try {
      const stored = localStorage.getItem(STORAGE_KEY);
      return stored ? JSON.parse(stored) : [];
    } catch (e) {
      return [];
    }
  }

  // Save selected IDs to localStorage
  function saveSelectedIds(ids) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
  }

  // Get biro name by ID from allEligibleData
  function getBiroName(id) {
    const item = allEligibleData.find(d => d.id == id);
    return item ? item.biro_name : 'Biro';
  }

  // Update hidden inputs and display count
  function updateBroadcastForm() {
    const selectedIds = getSelectedIds();
    const container = document.getElementById('hiddenIdsContainer');
    const selectedCountEl = document.getElementById('selectedCount');

    if (!container || !selectedCountEl) return;

    // Clear and rebuild hidden inputs
    container.innerHTML = '';
    selectedIds.forEach(id => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'ids[]';
      input.value = id;
      container.appendChild(input);
    });

    // Update count display
    selectedCountEl.textContent = selectedIds.length + ' biro dipilih';

    // Sync visible checkboxes
    document.querySelectorAll('.broadcast-checkbox').forEach(cb => {
      cb.checked = selectedIds.includes(parseInt(cb.value));
    });

    // Update select all state
    updateSelectAllState();
  }

  // Update Select All checkbox state
  function updateSelectAllState() {
    const selectAllCheckbox = document.getElementById('selectAll');
    if (!selectAllCheckbox) return;

    const selectedIds = getSelectedIds();
    const allEligibleIds = allEligibleData.map(d => d.id);

    if (selectedIds.length === 0) {
      selectAllCheckbox.checked = false;
      selectAllCheckbox.indeterminate = false;
    } else if (allEligibleIds.every(id => selectedIds.includes(id))) {
      selectAllCheckbox.checked = true;
      selectAllCheckbox.indeterminate = false;
    } else {
      selectAllCheckbox.checked = false;
      selectAllCheckbox.indeterminate = true;
    }
  }

  // Toggle single ID
  function toggleSelection(id, biroName) {
    let selectedIds = getSelectedIds();
    const idNum = parseInt(id);

    if (selectedIds.includes(idNum)) {
      selectedIds = selectedIds.filter(i => i !== idNum);
    } else {
      selectedIds.push(idNum);
    }

    saveSelectedIds(selectedIds);
    updateBroadcastForm();
  }

  // Select all eligible across ALL pages
  function selectAllEligible() {
    const allIds = allEligibleData.map(d => d.id);
    saveSelectedIds(allIds);
    updateBroadcastForm();
  }

  // Deselect all
  function deselectAll() {
    saveSelectedIds([]);
    updateBroadcastForm();
  }

  // Clear selection button
  function clearSelection() {
    if (getSelectedIds().length === 0) return;
    if (confirm('Hapus semua pilihan?')) {
      deselectAll();
    }
  }

  // Confirm broadcast
  function confirmBroadcast() {
    const selectedIds = getSelectedIds();
    if (selectedIds.length === 0) {
      alert('Pilih minimal 1 biro untuk broadcast');
      return false;
    }

    const biroNames = selectedIds.map(id => getBiroName(id)).join(', ');
    const confirmed = confirm('Kirim notifikasi ke ' + selectedIds.length + ' biro?\n\n' + biroNames);

    if (confirmed) {
      // Clear selection after successful submit
      localStorage.removeItem(STORAGE_KEY);
    }

    return confirmed;
  }

  // Initialize broadcast on page load
  document.addEventListener('DOMContentLoaded', function() {
    // Sync checkboxes with stored selection
    updateBroadcastForm();

    // Select All checkbox handler
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
      selectAllCheckbox.addEventListener('change', function() {
        if (this.checked) {
          selectAllEligible();
        } else {
          deselectAll();
        }
      });
    }

    // Individual checkbox handlers
    document.querySelectorAll('.broadcast-checkbox').forEach(cb => {
      cb.addEventListener('change', function() {
        toggleSelection(this.value, this.dataset.biro);
      });
    });
  });
  <?php endif; ?>
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kevannn\Documents\FILE MAGANG\AbsensiWika\resources\views/admin/pengajuan/index.blade.php ENDPATH**/ ?>