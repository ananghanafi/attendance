@extends('layouts.app')

@section('content')
<style>
  .alert {
    padding: 1rem 1.25rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
  }
  
  .alert-success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #86efac;
  }
  
  .alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
  }
  
  .header-section {
    margin-bottom: 2rem;
  }
  
  .header-section h1 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 1.5rem;
  }
  
  .back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: var(--primary);
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 1.5rem;
    transition: all 0.2s;
  }
  
  .back-button:hover {
    background: #4f46e5;
    transform: translateY(-1px);
  }
  
  .info-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e7eaf3;
    box-shadow: 0 4px 20px rgba(35,45,120,.08);
    padding: 24px;
    margin-bottom: 24px;
  }
  
  .info-grid {
    display: grid;
    gap: 1rem;
  }
  
  .info-item {
    background: #f1f5f9;
    padding: 1rem;
    border-radius: 8px;
  }
  
  .info-label {
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 0.25rem;
  }
  
  .info-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text);
  }
  
  /* Disabled radio button styling */
  input[type="radio"]:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  
  input[type="radio"]:disabled + label,
  .radio-cell input[type="radio"]:disabled {
    opacity: 0.6;
  }

  /* Holiday styling */
  .libur-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #fef2f2;
    color: #991b1b;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
    border: 1px solid #fecaca;
    margin-top: 2px;
  }
  
  .holiday-col {
    background: #fee2e2 !important;
    position: relative;
  }
  
  .holiday-header {
    background: #fecaca !important;
  }
  
  .table-section {
    background: white;
    border-radius: 16px;
    border: 1px solid #e7eaf3;
    box-shadow: 0 4px 20px rgba(35,45,120,.08);
    padding: 24px;
  }
  
  .table-section h2 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--text);
  }
  
  .tableScroll {
    overflow-x: auto;
    margin-bottom: 1rem;
  }
  
  table {
    width: auto;
    min-width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
    table-layout: fixed;
  }
  
  /* Column widths via classes */
  .col-nip { width: 110px !important; min-width: 110px !important; max-width: 110px !important; }
  .col-nama { 
    width: 150px !important; 
    min-width: 150px !important; 
    max-width: 150px !important;
    white-space: normal;
    word-wrap: break-word;
    line-height: 1.3;
  }
  .col-day { 
    width: 70px !important; 
    min-width: 70px !important; 
    max-width: 70px !important; 
  }
  .col-day-header {
    width: 140px !important;
    min-width: 140px !important;
    max-width: 140px !important;
  }
  
  thead {
    background: #f8fafc;
  }
  
  th {
    padding: 0.85rem 0.6rem;
    text-align: center;
    font-weight: 600;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-size: 0.875rem;
  }
  
  th.text-left {
    text-align: left;
  }
  
  /* Color coding for day headers */
  .senin-col { background: #c6f6d5 !important; }
  .selasa-col { background: #bee3f8 !important; }
  .rabu-col { background: #fef08a !important; }
  .kamis-col { background: #fbcfe8 !important; }
  .jumat-col { background: #bfdbfe !important; }
  
  td {
    padding: 0.85rem 0.6rem;
    border: 1px solid #e2e8f0;
    text-align: center;
    font-size: 0.875rem;
  }
  
  td.text-left {
    text-align: left;
  }
  
  td.radio-cell {
    text-align: center;
    padding: 0.85rem 0.6rem; /* Consistent padding */
  }
  
  /* Apply day colors to data cells */
  tbody tr:not(.summary-row) td.senin-col { background: #dcfce7; }
  tbody tr:not(.summary-row) td.selasa-col { background: #dbeafe; }
  tbody tr:not(.summary-row) td.rabu-col { background: #fef9c3; }
  tbody tr:not(.summary-row) td.kamis-col { background: #fce7f3; }
  tbody tr:not(.summary-row) td.jumat-col { background: #e0f2fe; }
  
  tbody tr:hover td {
    opacity: 0.9;
  }
  
  .summary-row {
    font-weight: 600;
    background: #f8fafc !important;
  }
  
  .summary-row td {
    background: #f8fafc !important;
    font-weight: 600;
  }
  
  .summary-row.totals td {
    color: #1e293b;
  }
  
  .percentage-cell {
    font-weight: 600;
  }
  
  .percentage-cell.wfo {
    color: #16a34a;
  }
  
  .percentage-cell.wfa {
    color: #16a34a;
  }
  
  .percentage-cell.over-target {
    color: #dc2626 !important;
  }
  
  input[type="radio"] {
    cursor: pointer;
    width: 16px;
    height: 16px;
  }
  
  .save-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 1rem;
  }
  
  .save-button:hover {
    background: #4f46e5;
    transform: translateY(-1px);
  }
  
  .save-button:disabled {
    background: #94a3b8;
    cursor: not-allowed;
    transform: none;
  }
</style>

@if(session('success'))
  <div class="alert alert-success">
    ✅ {{ session('success') }}
  </div>
@endif

@if(session('error'))
  <div class="alert alert-error">
    ❌ {{ session('error') }}
  </div>
@endif

<div class="header-section">
  <a href="{{ route('pengajuan.index') }}" class="back-button">
    ← Kembali ke List Pengajuan WFO
  </a>
  
  <h1>VIEW PENGAJUAN PEGAWAI WAO MINGGU {{ $pengajuan->minggu ?? '-' }} BULAN {{ $pengajuan->bulan ?? '-' }} TAHUN {{ $pengajuan->tahun ?? '-' }}</h1>
  
  <div class="info-card">
    <div class="info-grid">
      <div class="info-item">
        <div class="info-label">Unit Kerja</div>
        <div class="info-value">{{ $pengajuan->biro_name }}</div>
      </div>
      
      <div class="info-item">
        <div class="info-label">Tahun</div>
        <div class="info-value">{{ $pengajuan->tahun ?? '-' }}</div>
      </div>
      
      <div class="info-item">
        <div class="info-label">Bulan</div>
        <div class="info-value">{{ $pengajuan->bulan ?? '-' }}</div>
      </div>
      
      <div class="info-item">
        <div class="info-label">Minggu</div>
        <div class="info-value">{{ $pengajuan->minggu ?? '-' }}</div>
      </div>
      
      <div class="info-item">
        <div class="info-label">Periode</div>
        <div class="info-value">
          @if($pengajuan->tgl_awal && $pengajuan->tgl_akhir)
            {{ \Carbon\Carbon::parse($pengajuan->tgl_awal)->format('d-M-Y') }} sd {{ \Carbon\Carbon::parse($pengajuan->tgl_akhir)->format('d-M-Y') }}
          @else
            -
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<div class="table-section">
  <h2>Pengajuan Pegawai</h2>
  
  <form id="pengajuanForm" method="POST" action="{{ route('pengajuan.update') }}">
    @csrf
    @method('PUT')
    
    <div class="tableScroll">
      <table>
        <colgroup>
          <col style="width: 110px;"> <!-- NIP -->
          <col style="width: 150px;"> <!-- Nama Pegawai -->
          <col style="width: 70px;"> <!-- Senin WFO -->
          <col style="width: 70px;"> <!-- Senin WFA -->
          <col style="width: 70px;"> <!-- Selasa WFO -->
          <col style="width: 70px;"> <!-- Selasa WFA -->
          <col style="width: 70px;"> <!-- Rabu WFO -->
          <col style="width: 70px;"> <!-- Rabu WFA -->
          <col style="width: 70px;"> <!-- Kamis WFO -->
          <col style="width: 70px;"> <!-- Kamis WFA -->
          <col style="width: 70px;"> <!-- Jumat WFO -->
          <col style="width: 70px;"> <!-- Jumat WFA -->
          <col style="width: 70px;"> <!-- Jumlah WFO -->
          <col style="width: 70px;"> <!-- Jumlah WFA -->
          <col style="width: 70px;"> <!-- % WFO -->
          <col style="width: 70px;"> <!-- % WFA -->
        </colgroup>
        <thead>
          <tr>
            <th rowspan="2" class="text-left col-nip">NIP</th>
            <th rowspan="2" class="text-left col-nama">Nama Pegawai</th>
            <th colspan="2" class="senin-col col-day-header {{ ($hariLibur['senin'] ?? false) ? 'holiday-header' : '' }}">
              Senin
              @if($hariLibur['senin'] ?? false)
                <div class="libur-badge">LIBUR</div>
              @endif
            </th>
            <th colspan="2" class="selasa-col col-day-header {{ ($hariLibur['selasa'] ?? false) ? 'holiday-header' : '' }}">
              Selasa
              @if($hariLibur['selasa'] ?? false)
                <div class="libur-badge">LIBUR</div>
              @endif
            </th>
            <th colspan="2" class="rabu-col col-day-header {{ ($hariLibur['rabu'] ?? false) ? 'holiday-header' : '' }}">
              Rabu
              @if($hariLibur['rabu'] ?? false)
                <div class="libur-badge">LIBUR</div>
              @endif
            </th>
            <th colspan="2" class="kamis-col col-day-header {{ ($hariLibur['kamis'] ?? false) ? 'holiday-header' : '' }}">
              Kamis
              @if($hariLibur['kamis'] ?? false)
                <div class="libur-badge">LIBUR</div>
              @endif
            </th>
            <th colspan="2" class="jumat-col col-day-header {{ ($hariLibur['jumat'] ?? false) ? 'holiday-header' : '' }}">
              Jum'at
              @if($hariLibur['jumat'] ?? false)
                <div class="libur-badge">LIBUR</div>
              @endif
            </th>
            <th colspan="2" class="col-day-header">Jumlah</th>
            <th colspan="2" class="col-day-header">%</th>
          </tr>
          <tr>
            <th class="senin-col col-day {{ ($hariLibur['senin'] ?? false) ? 'holiday-header' : '' }}">WFO</th>
            <th class="senin-col col-day {{ ($hariLibur['senin'] ?? false) ? 'holiday-header' : '' }}">WFA</th>
            <th class="selasa-col col-day {{ ($hariLibur['selasa'] ?? false) ? 'holiday-header' : '' }}">WFO</th>
            <th class="selasa-col col-day {{ ($hariLibur['selasa'] ?? false) ? 'holiday-header' : '' }}">WFA</th>
            <th class="rabu-col col-day {{ ($hariLibur['rabu'] ?? false) ? 'holiday-header' : '' }}">WFO</th>
            <th class="rabu-col col-day {{ ($hariLibur['rabu'] ?? false) ? 'holiday-header' : '' }}">WFA</th>
            <th class="kamis-col col-day {{ ($hariLibur['kamis'] ?? false) ? 'holiday-header' : '' }}">WFO</th>
            <th class="kamis-col col-day {{ ($hariLibur['kamis'] ?? false) ? 'holiday-header' : '' }}">WFA</th>
            <th class="jumat-col col-day {{ ($hariLibur['jumat'] ?? false) ? 'holiday-header' : '' }}">WFO</th>
            <th class="jumat-col col-day {{ ($hariLibur['jumat'] ?? false) ? 'holiday-header' : '' }}">WFA</th>
            <th class="col-day">WFO</th>
            <th class="col-day">WFA</th>
            <th class="col-day">WFO</th>
            <th class="col-day">WFA</th>
          </tr>
        </thead>
        <tbody>
          @php
            $totalPegawai = $details->count();
            $wfoCount = ['senin' => 0, 'selasa' => 0, 'rabu' => 0, 'kamis' => 0, 'jumat' => 0];
            $wfaCount = ['senin' => 0, 'selasa' => 0, 'rabu' => 0, 'kamis' => 0, 'jumat' => 0];
          @endphp
          
          <!-- Summary Row: Jumlah -->
          <tr class="summary-row totals">
            <td colspan="2" class="text-left col-nama">Jumlah</td>
            <td class="senin-wfo-total senin-col col-day {{ ($hariLibur['senin'] ?? false) ? 'holiday-col' : '' }}">0</td>
            <td class="senin-wfa-total senin-col col-day {{ ($hariLibur['senin'] ?? false) ? 'holiday-col' : '' }}">0</td>
            <td class="selasa-wfo-total selasa-col col-day {{ ($hariLibur['selasa'] ?? false) ? 'holiday-col' : '' }}">0</td>
            <td class="selasa-wfa-total selasa-col col-day {{ ($hariLibur['selasa'] ?? false) ? 'holiday-col' : '' }}">0</td>
            <td class="rabu-wfo-total rabu-col col-day {{ ($hariLibur['rabu'] ?? false) ? 'holiday-col' : '' }}">0</td>
            <td class="rabu-wfa-total rabu-col col-day {{ ($hariLibur['rabu'] ?? false) ? 'holiday-col' : '' }}">0</td>
            <td class="kamis-wfo-total kamis-col col-day {{ ($hariLibur['kamis'] ?? false) ? 'holiday-col' : '' }}">0</td>
            <td class="kamis-wfa-total kamis-col col-day {{ ($hariLibur['kamis'] ?? false) ? 'holiday-col' : '' }}">0</td>
            <td class="jumat-wfo-total jumat-col col-day {{ ($hariLibur['jumat'] ?? false) ? 'holiday-col' : '' }}">0</td>
            <td class="jumat-wfa-total jumat-col col-day {{ ($hariLibur['jumat'] ?? false) ? 'holiday-col' : '' }}">0</td>
            <td class="col-day">-</td>
            <td class="col-day">-</td>
            <td class="col-day">-</td>
            <td class="col-day">-</td>
          </tr>
          
          <!-- Summary Row: Persentase -->
          <tr class="summary-row percentages">
            <td colspan="2" class="text-left col-nama">Persentase</td>
            <td class="percentage-cell wfo senin-wfo-percentage senin-col col-day {{ ($hariLibur['senin'] ?? false) ? 'holiday-col' : '' }}">0%</td>
            <td class="percentage-cell wfa senin-wfa-percentage senin-col col-day {{ ($hariLibur['senin'] ?? false) ? 'holiday-col' : '' }}">0%</td>
            <td class="percentage-cell wfo selasa-wfo-percentage selasa-col col-day {{ ($hariLibur['selasa'] ?? false) ? 'holiday-col' : '' }}">0%</td>
            <td class="percentage-cell wfa selasa-wfa-percentage selasa-col col-day {{ ($hariLibur['selasa'] ?? false) ? 'holiday-col' : '' }}">0%</td>
            <td class="percentage-cell wfo rabu-wfo-percentage rabu-col col-day {{ ($hariLibur['rabu'] ?? false) ? 'holiday-col' : '' }}">0%</td>
            <td class="percentage-cell wfa rabu-wfa-percentage rabu-col col-day {{ ($hariLibur['rabu'] ?? false) ? 'holiday-col' : '' }}">0%</td>
            <td class="percentage-cell wfo kamis-wfo-percentage kamis-col col-day {{ ($hariLibur['kamis'] ?? false) ? 'holiday-col' : '' }}">0%</td>
            <td class="percentage-cell wfa kamis-wfa-percentage kamis-col col-day {{ ($hariLibur['kamis'] ?? false) ? 'holiday-col' : '' }}">0%</td>
            <td class="percentage-cell wfo jumat-wfo-percentage jumat-col col-day {{ ($hariLibur['jumat'] ?? false) ? 'holiday-col' : '' }}">0%</td>
            <td class="percentage-cell wfa jumat-wfa-percentage jumat-col col-day {{ ($hariLibur['jumat'] ?? false) ? 'holiday-col' : '' }}">0%</td>
            <td class="col-day">-</td>
            <td class="col-day">-</td>
            <td class="col-day">-</td>
            <td class="col-day">-</td>
          </tr>
          
          @forelse($details as $detail)
            @php
              // Count for summary
              if($detail->senin) $wfoCount['senin']++; else $wfaCount['senin']++;
              if($detail->selasa) $wfoCount['selasa']++; else $wfaCount['selasa']++;
              if($detail->rabu) $wfoCount['rabu']++; else $wfaCount['rabu']++;
              if($detail->kamis) $wfoCount['kamis']++; else $wfaCount['kamis']++;
              if($detail->jumat) $wfoCount['jumat']++; else $wfaCount['jumat']++;
              
              // Calculate individual totals
              $wfoTotal = ($detail->senin ? 1 : 0) + ($detail->selasa ? 1 : 0) + ($detail->rabu ? 1 : 0) + ($detail->kamis ? 1 : 0) + ($detail->jumat ? 1 : 0);
              $wfaTotal = 5 - $wfoTotal;
              $wfoPercentage = ($wfoTotal / 5) * 100;
              $wfaPercentage = 100 - $wfoPercentage;
            @endphp
            
            <tr class="employee-row" data-nip="{{ $detail->nip }}">
              <td class="text-left col-nip">{{ $detail->nip }}</td>
              <td class="text-left col-nama">{{ strtoupper($detail->nama) }}</td>
              
              <!-- Senin -->
              <td class="radio-cell senin-col col-day {{ ($hariLibur['senin'] ?? false) ? 'holiday-col' : '' }}">
                <input type="radio" name="attendance[{{ $detail->nip }}][senin]" value="1" {{ $detail->senin ? 'checked' : '' }} class="day-radio" data-day="senin" {{ $readOnly || ($hariLibur['senin'] ?? false) ? 'disabled' : '' }}>
              </td>
              <td class="radio-cell senin-col col-day {{ ($hariLibur['senin'] ?? false) ? 'holiday-col' : '' }}">
                <input type="radio" name="attendance[{{ $detail->nip }}][senin]" value="0" {{ !$detail->senin ? 'checked' : '' }} class="day-radio" data-day="senin" {{ $readOnly || ($hariLibur['senin'] ?? false) ? 'disabled' : '' }}>
              </td>
              
              <!-- Selasa -->
              <td class="radio-cell selasa-col col-day {{ ($hariLibur['selasa'] ?? false) ? 'holiday-col' : '' }}">
                <input type="radio" name="attendance[{{ $detail->nip }}][selasa]" value="1" {{ $detail->selasa ? 'checked' : '' }} class="day-radio" data-day="selasa" {{ $readOnly || ($hariLibur['selasa'] ?? false) ? 'disabled' : '' }}>
              </td>
              <td class="radio-cell selasa-col col-day {{ ($hariLibur['selasa'] ?? false) ? 'holiday-col' : '' }}">
                <input type="radio" name="attendance[{{ $detail->nip }}][selasa]" value="0" {{ !$detail->selasa ? 'checked' : '' }} class="day-radio" data-day="selasa" {{ $readOnly || ($hariLibur['selasa'] ?? false) ? 'disabled' : '' }}>
              </td>
              
              <!-- Rabu -->
              <td class="radio-cell rabu-col col-day {{ ($hariLibur['rabu'] ?? false) ? 'holiday-col' : '' }}">
                <input type="radio" name="attendance[{{ $detail->nip }}][rabu]" value="1" {{ $detail->rabu ? 'checked' : '' }} class="day-radio" data-day="rabu" {{ $readOnly || ($hariLibur['rabu'] ?? false) ? 'disabled' : '' }}>
              </td>
              <td class="radio-cell rabu-col col-day {{ ($hariLibur['rabu'] ?? false) ? 'holiday-col' : '' }}">
                <input type="radio" name="attendance[{{ $detail->nip }}][rabu]" value="0" {{ !$detail->rabu ? 'checked' : '' }} class="day-radio" data-day="rabu" {{ $readOnly || ($hariLibur['rabu'] ?? false) ? 'disabled' : '' }}>
              </td>
              
              <!-- Kamis -->
              <td class="radio-cell kamis-col col-day {{ ($hariLibur['kamis'] ?? false) ? 'holiday-col' : '' }}">
                <input type="radio" name="attendance[{{ $detail->nip }}][kamis]" value="1" {{ $detail->kamis ? 'checked' : '' }} class="day-radio" data-day="kamis" {{ $readOnly || ($hariLibur['kamis'] ?? false) ? 'disabled' : '' }}>
              </td>
              <td class="radio-cell kamis-col col-day {{ ($hariLibur['kamis'] ?? false) ? 'holiday-col' : '' }}">
                <input type="radio" name="attendance[{{ $detail->nip }}][kamis]" value="0" {{ !$detail->kamis ? 'checked' : '' }} class="day-radio" data-day="kamis" {{ $readOnly || ($hariLibur['kamis'] ?? false) ? 'disabled' : '' }}>
              </td>
              
              <!-- Jumat -->
              <td class="radio-cell jumat-col col-day {{ ($hariLibur['jumat'] ?? false) ? 'holiday-col' : '' }}">
                <input type="radio" name="attendance[{{ $detail->nip }}][jumat]" value="1" {{ $detail->jumat ? 'checked' : '' }} class="day-radio" data-day="jumat" {{ $readOnly || ($hariLibur['jumat'] ?? false) ? 'disabled' : '' }}>
              </td>
              <td class="radio-cell jumat-col col-day {{ ($hariLibur['jumat'] ?? false) ? 'holiday-col' : '' }}">
                <input type="radio" name="attendance[{{ $detail->nip }}][jumat]" value="0" {{ !$detail->jumat ? 'checked' : '' }} class="day-radio" data-day="jumat" {{ $readOnly || ($hariLibur['jumat'] ?? false) ? 'disabled' : '' }}>
              </td>
              
              <!-- Jumlah WFO/WFA -->
              <td class="wfo-count col-day">{{ $wfoTotal }}</td>
              <td class="wfa-count col-day">{{ $wfaTotal }}</td>
              
              <!-- Persentase WFO/WFA -->
              <td class="wfo-percentage col-day">{{ number_format($wfoPercentage, 0) }}%</td>
              <td class="wfa-percentage col-day">{{ number_format($wfaPercentage, 0) }}%</td>
            </tr>
          @empty
            <tr>
              <td colspan="16" style="text-align: center; color: #94a3b8; padding: 2rem;">
                Tidak ada data pegawai
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    
    @if($details->count() > 0 && !$readOnly)
      <div id="overTargetWarning" class="alert alert-error" style="display: none; margin-top: 1rem;">
        ⚠️ <strong>Tidak dapat menyimpan!</strong> Persentase WFO melebihi batas maksimal {{ $pengajuan->persentase_decimal ?? 50 }}%. Silakan kurangi jumlah WFO pada hari yang berwarna merah.
      </div>
      <button type="submit" class="save-button">
        💾 Simpan Perubahan
      </button>
    @endif
  </form>
</div>

<script>
  // Real-time calculation when radio buttons change
  const form = document.getElementById('pengajuanForm');
  const totalPegawai = {{ $totalPegawai }};
  const targetPercentage = {{ $pengajuan->persentase_decimal ?? 50 }};
  const saveButton = document.querySelector('.save-button');
  const overTargetWarning = document.getElementById('overTargetWarning');
  
  // Initialize summary on page load
  document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
  });
  
  form.addEventListener('change', function(e) {
    if(e.target.classList.contains('day-radio')) {
      updateSummary();
    }
  });
  
  function updateSummary() {
    const days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
    let hasOverTarget = false;
    
    days.forEach(day => {
      // Count WFO/WFA for this day
      let wfoCount = 0;
      let wfaCount = 0;
      
      document.querySelectorAll(`input[name*="[${day}]"]`).forEach(radio => {
        if(radio.checked) {
          if(radio.value === '1') {
            wfoCount++;
          } else {
            wfaCount++;
          }
        }
      });
      
      // Update totals in summary row
      document.querySelector(`.${day}-wfo-total`).textContent = wfoCount;
      document.querySelector(`.${day}-wfa-total`).textContent = wfaCount;
      
      // Calculate and update percentages
      const wfoPercentage = totalPegawai > 0 ? (wfoCount / totalPegawai) * 100 : 0;
      const wfaPercentage = 100 - wfoPercentage;
      
      const wfoCell = document.querySelector(`.${day}-wfo-percentage`);
      const wfaCell = document.querySelector(`.${day}-wfa-percentage`);
      
      wfoCell.textContent = Math.round(wfoPercentage) + '%';
      wfaCell.textContent = Math.round(wfaPercentage) + '%';
      
      // Color validation - red if over target
      if(wfoPercentage > targetPercentage) {
        wfoCell.classList.add('over-target');
        hasOverTarget = true;
      } else {
        wfoCell.classList.remove('over-target');
      }
    });
    
    // Disable/Enable save button based on validation
    if(saveButton) {
      if(hasOverTarget) {
        saveButton.disabled = true;
        saveButton.style.opacity = '0.5';
        saveButton.style.cursor = 'not-allowed';
        saveButton.title = `Persentase WFO melebihi batas maksimal ${targetPercentage}%`;
        if(overTargetWarning) overTargetWarning.style.display = 'flex';
      } else {
        saveButton.disabled = false;
        saveButton.style.opacity = '1';
        saveButton.style.cursor = 'pointer';
        saveButton.title = '';
        if(overTargetWarning) overTargetWarning.style.display = 'none';
      }
    }
    
    // Update individual row totals
    document.querySelectorAll('.employee-row').forEach(row => {
      let wfoTotal = 0;
      row.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
        if(radio.value === '1') wfoTotal++;
      });
      
      const wfaTotal = 5 - wfoTotal;
      const wfoPercentage = (wfoTotal / 5) * 100;
      const wfaPercentage = 100 - wfoPercentage;
      
      row.querySelector('.wfo-count').textContent = wfoTotal;
      row.querySelector('.wfa-count').textContent = wfaTotal;
      row.querySelector('.wfo-percentage').textContent = Math.round(wfoPercentage) + '%';
      row.querySelector('.wfa-percentage').textContent = Math.round(wfaPercentage) + '%';
    });
  }
</script>
@endsection
