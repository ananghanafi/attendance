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
  <div class="tableScroll">
    <table>
      <thead>
        <tr>
          <th>Biro</th>
          <th>Bulan</th>
          <th>Tahun</th>
          <th>Minggu</th>
          <th>Tanggal Mulai</th>
          <th>Tanggal Selesai</th>
          <th>%</th>
          <th style="text-align:center">Pembuatan<br>Schedule</th>
          <th style="text-align:center">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($pengajuans as $p)
          <tr class="pengajuan-row">
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
            <td>{{ ($p->persentase_decimal ?? 0) . '%' }}</td>
            <td style="text-align:center">
              @if(strtolower($p->status ?? '') == 'final')
                <span class="badge success">Close</span>
              @else
                <span class="badge warning">Open</span>
              @endif
            </td>
            <td style="text-align:center">
              <form action="{{ route('pengajuan.setView') }}" method="POST" style="display:inline">
                @csrf
                <input type="hidden" name="id" value="{{ $p->id }}">
                <button type="submit" class="btn secondary" style="padding:8px 16px">View</button>
              </form>
              <form action="{{ route('pengajuan.setEdit') }}" method="POST" style="display:inline">
                @csrf
                <input type="hidden" name="id" value="{{ $p->id }}">
                <button type="submit" class="btn primary" style="padding:8px 16px">Edit</button>
              </form>
            </td>
          </tr>
        @empty
          <tr id="emptyRow">
            <td colspan="9" class="empty">
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
</script>
@endsection