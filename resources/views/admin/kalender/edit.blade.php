@extends('layouts.app')

@section('title', 'Edit Kalender Kerja')

@section('styles')
.card{background:#fff;border-radius:18px;border:1px solid #e7eaf3;box-shadow:0 10px 35px rgba(35,45,120,.08);padding:18px}
label{display:block;font-size:12px;color:var(--text-muted);margin-bottom:6px}
select,input{width:100%;padding:11px 12px;border:1px solid #eef0f6;border-radius:12px;background:#fff;outline:none;font-size:14px}
select:focus,input:focus{box-shadow:0 0 0 3px rgba(89,102,247,0.08);border-color:var(--primary)}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
@media(max-width:900px){.grid{grid-template-columns:1fr}}
.row2{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-top:14px}
@media(max-width:900px){.row2{grid-template-columns:1fr}}
.actions{margin-top:16px;display:flex;gap:10px;justify-content:space-between;align-items:center;flex-wrap:wrap}
.btn{padding:11px 14px;border-radius:12px;border:none;cursor:pointer;font-weight:800;background:#fff;border:1px solid #eef0f6}
.btn.primary{background:var(--primary);border-color:var(--primary);color:#fff;box-shadow:0 6px 18px rgba(89,102,247,0.18)}
.error{margin:10px 0 0;color:#991b1b;background:#fff1f2;border:1px solid #fecdd3;padding:10px;border-radius:12px}
@endsection

@section('content')
<div class="card">
  @if($errors->any())
    <div class="error">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('kalender.update', ['id' => $row->id]) }}" style="margin-top:14px">
    @csrf
    @method('PUT')

    <div class="grid">
      <div>
        <label for="minggu">Minggu ke-</label>
        <select id="minggu" name="minggu" required>
          @php
            $parsedMinggu = null;
            if (!empty($row->periode) && preg_match('/(\d+)/', $row->periode, $m)) {
              $parsedMinggu = (int) $m[1];
            }
          @endphp
          @for($i=1;$i<=6;$i++)
            <option value="{{ $i }}" @selected(($parsedMinggu ?? 1) === $i)>{{ $i }}</option>
          @endfor
        </select>
      </div>
      <div>
        <label for="wfo_maks">WFO Maksimal (%)</label>
        @php
          $wfoVal = $row->persentase_decimal;
          if ($wfoVal === null && $row->persentase !== null) {
            $wfoVal = $row->persentase;
          }
        @endphp
        <input type="number" id="wfo_maks" name="wfo_maks" min="0" max="100" step="0.01" required value="{{ $wfoVal }}">
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
        <label for="tgl_awal">Tanggal Awal</label>
        <input type="date" id="tgl_awal" name="tgl_awal" required value="{{ optional($row->tgl_awal)->format('Y-m-d') }}">
      </div>
      <div>
        <label for="tgl_akhir">Tanggal Akhir</label>
        <input type="date" id="tgl_akhir" name="tgl_akhir" required value="{{ optional($row->tgl_akhir)->format('Y-m-d') }}">
      </div>
    </div>

    <div class="actions">
      <button type="submit" class="btn primary">Simpan Perubahan</button>
    </div>
  </form>
</div>
@endsection

@section('scripts')
  // Initialize bulan/tahun selects for edit form (no <script> wrapper here — layout provides the script tag)
  const bulanEl = document.getElementById('bulan');
  const tahunEl = document.getElementById('tahun');
  const tglAwalEl = document.getElementById('tgl_awal');
  const tglAkhirEl = document.getElementById('tgl_akhir');

  const bulanNames = [
    'Januari','Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
  ];

  function initBulan() {
    if (!bulanEl) return;
    bulanEl.innerHTML = '';
    bulanNames.forEach((n, i) => {
      const opt = document.createElement('option');
      opt.value = String(i + 1);
      opt.textContent = n;
      bulanEl.appendChild(opt);
    });
  }

  function initTahun() {
    if (!tahunEl) return;
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
  }

  initBulan();
  initTahun();

  // bulan/tahun auto dari tgl_awal (fallback: tahun sekarang)
  if (tglAwalEl && tglAwalEl.value) {
    const d = new Date(tglAwalEl.value + 'T00:00:00');
    if (bulanEl) bulanEl.value = String(d.getMonth() + 1);
    if (tahunEl) tahunEl.value = String(d.getFullYear());
  } else {
    const now = new Date();
    if (bulanEl) bulanEl.value = String(now.getMonth() + 1);
    if (tahunEl) tahunEl.value = String(now.getFullYear());
  }

  // validasi cepat client side
  if (tglAkhirEl) {
    tglAkhirEl.addEventListener('change', () => {
      if (tglAwalEl && tglAwalEl.value && tglAkhirEl.value && tglAkhirEl.value < tglAwalEl.value) {
        alert('Tanggal akhir harus lebih besar atau sama dengan tanggal awal.');
        tglAkhirEl.value = '';
      }
    });
  }
@endsection
