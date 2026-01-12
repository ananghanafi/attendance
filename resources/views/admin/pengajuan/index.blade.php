@extends('layouts.app')

@section('title', 'Pengajuan Pegawai WFO')

@section('styles')
<style>
.page-title{
  font-size:24px;
  font-weight:700;
  margin-bottom:32px;
  color:var(--text);
}
.card{
  background:#fff;
  border-radius:16px;
  border:1px solid #e7eaf3;
  box-shadow:0 4px 20px rgba(35,45,120,.08);
  padding:24px;
  margin-bottom:24px;
}
.card.pagination-card{
  padding:16px 24px;
  margin-bottom:0;
}
.card-header{
  font-size:18px;
  font-weight:600;
  margin-bottom:20px;
  color:var(--text);
}
.filters{
  display:flex;
  flex-wrap:wrap;
  gap:12px;
  align-items:flex-end;
}
.filter-group{
  display:flex;
  flex-direction:column;
  gap:6px;
  min-width:180px;
  flex:1;
}
.filter-group.buttons{
  flex-direction:row;
  min-width:auto;
  flex:0;
  gap:8px;
  align-items:flex-end;
}
.filter-group label{font-size:12px;color:var(--text-muted);font-weight:500}
.filter-group input,.filter-group select{
  padding:10px 12px;
  border:1px solid #eef0f6;
  border-radius:10px;
  font-size:14px;
  outline:none;
  background:#fff;
  width:100%;
  box-sizing:border-box;
}
.filter-group input:focus,.filter-group select:focus{box-shadow:0 0 0 3px rgba(89,102,247,0.08);border-color:var(--primary)}
.btn{padding:10px 16px;border-radius:10px;border:none;cursor:pointer;font-weight:600;font-size:14px;transition:all 0.2s ease;text-decoration:none;display:inline-block}
.btn.primary{background:var(--primary);color:#fff;box-shadow:0 4px 12px rgba(89,102,247,0.2)}
.btn.primary:hover{background:var(--primary-dark)}
.btn.secondary{background:#fff;color:var(--text);border:1px solid #eef0f6}
.btn.secondary:hover{background:#f9fafb}
.tableScroll{overflow-x:auto}
table{width:100%;border-collapse:collapse;min-width:1000px}
thead{background:#f9fafb}
th{padding:14px 16px;text-align:left;font-size:13px;font-weight:600;color:var(--text-muted);border-bottom:2px solid #eef0f6;white-space:nowrap}
td{padding:14px 16px;border-bottom:1px solid #f3f4f6;font-size:14px}
tr:hover{background:#f9fafb}
.badge{display:inline-block;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600}
.badge.success{background:#d1fae5;color:#065f46}
.badge.warning{background:#fef3c7;color:#92400e}
.badge.danger{background:#fee2e2;color:#991b1b}
.pagination{display:flex;justify-content:center;align-items:center;gap:8px;flex-wrap:wrap}
.pagination a,.pagination span{padding:8px 12px;border-radius:8px;border:1px solid #eef0f6;text-decoration:none;color:var(--text);font-size:14px;background:#fff}
.pagination a:hover{background:#f3f4f6}
.pagination .active{background:var(--primary);color:#fff;border-color:var(--primary)}
.empty{text-align:center;padding:40px 20px;color:var(--text-muted)}
.alert{padding:12px 16px;border-radius:10px;margin-bottom:16px}
.alert-success{background:#d1fae5;color:#065f46}
.alert-error{background:#fee2e2;color:#991b1b}

/* Mobile Responsive */
@media(max-width:768px){
  .page-title{
    font-size:20px;
    margin-bottom:16px;
  }
  .card{
    padding:16px;
    margin-bottom:16px;
  }
  .filters{
    flex-direction:column;
    gap:12px;
  }
  .filter-group{
    min-width:100%;
    width:100%;
  }
  .filter-group.buttons{
    flex-direction:row;
    width:100%;
    justify-content:flex-start;
  }
  .filter-group.buttons .btn{
    flex:1;
    text-align:center;
  }
}
</style>
@endsection

@section('content')
<h1 class="page-title">PENGAJUAN PEGAWAI WFO</h1>

@if(session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if(session('error'))
  <div class="alert alert-error">{{ session('error') }}</div>
@endif

<!-- Card Filter -->
<div class="card">
  <form method="GET" action="{{ route('pengajuan.index') }}" id="filterForm">
    <div class="filters">
      <div class="filter-group">
        <label>🔍 Cari Biro</label>
        <input 
          type="text" 
          name="search"
          id="searchBiro" 
          value="{{ $filters['search'] ?? '' }}"
          placeholder="Ketik nama biro..." 
          autocomplete="off"
          style="padding:11px 12px"
        >
      </div>
      
      <div class="filter-group">
        <label>📅 Minggu</label>
        <select name="minggu" style="padding:11px 12px">
          <option value="">Semua Minggu</option>
          @for($i = 1; $i <= 6; $i++)
            <option value="{{ $i }}" {{ ($filters['minggu'] ?? '') == $i ? 'selected' : '' }}>
              Minggu {{ $i }}
            </option>
          @endfor
        </select>
      </div>
      
      <div class="filter-group">
        <label>📆 Bulan</label>
        <select name="bulan" style="padding:11px 12px">
          <option value="">Semua Bulan</option>
          @php
            $bulanNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
          @endphp
          @for($i = 1; $i <= 12; $i++)
            <option value="{{ $i }}" {{ ($filters['bulan'] ?? '') == $i ? 'selected' : '' }}>
              {{ $bulanNames[$i] }}
            </option>
          @endfor
        </select>
      </div>
      
      <div class="filter-group">
        <label>🗓️ Tahun</label>
        <input 
          type="number" 
          name="tahun"
          id="searchTahun" 
          value="{{ $filters['tahun'] ?? '' }}"
          placeholder="Ketik tahun..." 
          autocomplete="off"
          style="padding:11px 12px"
        >
      </div>
      
      <div class="filter-group buttons">
        <button type="submit" class="btn primary" style="padding:11px 20px;">
          Filter
        </button>
        <a href="{{ route('pengajuan.index') }}" class="btn secondary" style="padding:11px 20px;">
          Reset
        </a>
      </div>
    </div>
  </form>
</div>

<!-- Card Table -->
<div class="card">
  <!-- Broadcast Multiple Button -->
  <form id="broadcastForm" action="{{ route('pengajuan.broadcastMultiple') }}" method="POST" style="margin-bottom:16px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
    @csrf
    <div id="hiddenIdsContainer"></div>
    <button type="submit" class="btn" style="padding:10px 16px;border:1px solid #c7d2fe;background:#eef2ff;color:#4338ca" onclick="return confirmBroadcast()">
      📢 Broadcast Terpilih
    </button>
    <span id="selectedCount" style="font-size:13px;color:var(--text-muted)">0 biro dipilih</span>
    <button type="button" class="btn secondary" style="padding:8px 12px;font-size:12px" onclick="clearSelection()">
      ✕ Hapus Pilihan
    </button>
  </form>

  <div class="tableScroll">
    <table>
      <thead>
        <tr>
          <th style="width:40px;text-align:center">
            <input type="checkbox" id="selectAll" title="Pilih Semua" style="cursor:pointer">
          </th>
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
        @forelse($pengajuans as $p)
          <tr class="pengajuan-row">
            <td style="text-align:center">
              @if($p->canEdit)
              <input type="checkbox" name="ids[]" value="{{ $p->id }}" class="broadcast-checkbox" form="broadcastForm" data-biro="{{ $p->biro_name }}" style="cursor:pointer">
              @endif
            </td>
            <td>{{ $p->biro_name }}</td>
            <td>
              @php
                $bulanNames = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                echo $bulanNames[$p->bulan ?? 0] ?? '-';
              @endphp
            </td>
            <td>{{ $p->tahun ?? '-' }}</td>
            <td>{{ $p->minggu ?? '-' }}</td>
            <td>{{ $p->tgl_awal ? \Carbon\Carbon::parse($p->tgl_awal)->format('d-M-Y') : '-' }}</td>
            <td>{{ $p->tgl_akhir ? \Carbon\Carbon::parse($p->tgl_akhir)->format('d-M-Y') : '-' }}</td>
            <td>{{ $p->persentase !== null ? $p->persentase . '%' : '-' }}</td>
            <td>{{ $p->persentase_wfa !== null ? $p->persentase_wfa . '%' : '-' }}</td>
            <td style="text-align:center">
              @if(strtolower($p->status ?? '') == 'final')
                <span class="badge success">Close</span>
              @else
                <span class="badge warning">Open</span>
              @endif
            </td>
            <td style="text-align:center">
              <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap">
                <form action="{{ route('pengajuan.setView') }}" method="POST" style="display:inline">
                  @csrf
                  <input type="hidden" name="id" value="{{ $p->id }}">
                  <button type="submit" class="btn secondary" style="padding:8px 16px">View</button>
                </form>
                @if($p->canEdit)
                <form action="{{ route('pengajuan.setEdit') }}" method="POST" style="display:inline">
                  @csrf
                  <input type="hidden" name="id" value="{{ $p->id }}">
                  <button type="submit" class="btn primary" style="padding:8px 16px">Edit</button>
                </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr id="emptyRow">
            <td colspan="11" class="empty">
              @if(!empty($filters['search']))
                Tidak ada hasil yang cocok dengan pencarian "{{ $filters['search'] }}".
              @else
                Belum ada data pengajuan WFO.
              @endif
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@if($pengajuans->hasPages())
<div class="card pagination-card">
  <div class="pagination">
    {{-- Previous Page Link --}}
    @if ($pengajuans->onFirstPage())
      <span style="opacity:0.5">‹ Prev</span>
    @else
      <a href="{{ $pengajuans->previousPageUrl() }}">‹ Prev</a>
    @endif

    {{-- Page Numbers with sliding window (max 10 pages visible) --}}
    @php
      $currentPage = $pengajuans->currentPage();
        $lastPage = $pengajuans->lastPage();
        $maxVisible = 10;
        
        // Calculate start and end of visible page range
        $start = max(1, $currentPage - floor($maxVisible / 2));
        $end = min($lastPage, $start + $maxVisible - 1);
        
        // Adjust start if we're near the end
        if ($end - $start + 1 < $maxVisible) {
          $start = max(1, $end - $maxVisible + 1);
        }
      @endphp

      {{-- Show first page if not in range --}}
      @if($start > 1)
        <a href="{{ $pengajuans->url(1) }}">1</a>
        @if($start > 2)
          <span style="opacity:0.5">...</span>
        @endif
      @endif

      {{-- Show visible page range --}}
      @for($page = $start; $page <= $end; $page++)
        @if($page == $currentPage)
          <span class="active">{{ $page }}</span>
        @else
          <a href="{{ $pengajuans->url($page) }}">{{ $page }}</a>
        @endif
      @endfor

      {{-- Show last page if not in range --}}
      @if($end < $lastPage)
        @if($end < $lastPage - 1)
          <span style="opacity:0.5">...</span>
        @endif
        <a href="{{ $pengajuans->url($lastPage) }}">{{ $lastPage }}</a>
      @endif

      {{-- Next Page Link --}}
    @if ($pengajuans->hasMorePages())
      <a href="{{ $pengajuans->nextPageUrl() }}">Next ›</a>
    @else
      <span style="opacity:0.5">Next ›</span>
    @endif
  </div>
</div>
@endif

<script>
  // All eligible IDs from server (across all pages)
  const allEligibleData = @json($allEligibleIds ?? []);
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

  // Initialize on page load
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
</script>
@endsection