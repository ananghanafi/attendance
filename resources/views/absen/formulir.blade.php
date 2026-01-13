@extends('layouts.app')

@section('title', $tipe === 'masuk' ? 'Absen Masuk' : 'Absen Pulang')

@section('styles')
<style>
    .page-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 24px;
        color: var(--text);
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
        font-size: 14px;
    }

    .breadcrumb a {
        color: #3b82f6;
        text-decoration: none;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .breadcrumb span {
        color: #64748b;
    }

    .card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e7eaf3;
        box-shadow: 0 4px 20px rgba(35, 45, 120, .08);
        padding: 24px;
        margin-bottom: 24px;
    }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e7eaf3;
    }

    .info-banner {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .info-banner.masuk {
        background: #ecfdf5;
        border-color: #a7f3d0;
    }

    .info-banner.pulang {
        background: #fef2f2;
        border-color: #fecaca;
    }

    .info-banner.warning {
        background: #fef3c7;
        border-color: #fcd34d;
    }

    .info-banner .icon {
        font-size: 24px;
    }

    .info-banner .text h4 {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .info-banner .text p {
        font-size: 13px;
        color: #64748b;
    }

    .form-row {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 8px;
    }

    .form-label.required::after {
        content: '*';
        color: #dc2626;
        margin-left: 4px;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 12px 16px;
        font-size: 14px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
        transition: all 0.2s ease;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #3b82f6;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-input:disabled,
    .form-select:disabled {
        background: #e2e8f0;
        cursor: not-allowed;
    }

    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }

    .form-hint {
        font-size: 12px;
        color: #64748b;
        margin-top: 6px;
    }

    .radio-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .radio-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .radio-item:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .radio-item.selected {
        background: #eff6ff;
        border-color: #3b82f6;
    }

    .radio-item input[type="radio"] {
        width: 18px;
        height: 18px;
        accent-color: #3b82f6;
    }

    .radio-item .radio-content {
        flex: 1;
    }

    .radio-item .radio-title {
        font-weight: 600;
        color: var(--text);
        font-size: 14px;
    }

    .radio-item .radio-desc {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
    }

    .btn-row {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e7eaf3;
    }

    .btn {
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 600;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .btn-primary.masuk {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .btn-primary.masuk:hover {
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
    }

    .btn-primary.pulang {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    }

    .btn-primary.pulang:hover {
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
    }

    .error-text {
        color: #dc2626;
        font-size: 12px;
        margin-top: 6px;
    }

    .summary-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px dashed #e2e8f0;
    }

    .summary-item:last-child {
        border-bottom: none;
    }

    .summary-label {
        color: #64748b;
        font-size: 13px;
    }

    .summary-value {
        font-weight: 600;
        color: var(--text);
        font-size: 13px;
    }

    /* WFA Fields */
    .wfa-fields {
        display: none;
        margin-top: 20px;
        padding: 16px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 12px;
    }

    .wfa-fields.show {
        display: block;
    }

    .wfa-detail-field {
        display: none;
        margin-top: 16px;
    }

    .wfa-detail-field.show {
        display: block;
    }

    /* Test Info */
    .test-info {
        background: #fef3c7;
        border: 2px dashed #f59e0b;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #92400e;
    }

    .test-info code {
        background: #fde68a;
        padding: 2px 6px;
        border-radius: 4px;
        font-family: monospace;
    }

    @media (max-width: 768px) {
        .btn-row {
            flex-direction: column;
        }

        .btn {
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="breadcrumb">
    <a href="{{ route('absen.index') }}">Absen</a>
    <span>/</span>
    <span>{{ $tipe === 'masuk' ? 'Absen Masuk' : 'Absen Pulang' }}</span>
</div>

<h1 class="page-title">
    @if($tipe === 'masuk')
    🕐 ABSEN MASUK
    @else
    🏃 ABSEN PULANG
    @endif
</h1>

@if($errors->any())
<div class="alert alert-error">
    ❌ Terdapat kesalahan pada form. Silakan periksa kembali.
</div>
@endif

<!-- Test Info -->
<div class="test-info">
    🧪 Form Type: <code>{{ $formType }}</code> |
    Jadwal: <code>{{ $jadwal['is_wfo'] ? 'WFO' : 'WFA' }}</code> |
    Posisi: <code>{{ $info['posisi_label'] }}</code>
    @if($tipe === 'pulang')
    | Needs Approval: <code>{{ $needsApproval ? 'Ya' : 'Tidak' }}</code>
    @endif
</div>

@if($tipe === 'pulang')
{{-- ==================== FORM ABSEN PULANG ==================== --}}

@if($needsApproval)
<!-- Info: Perlu Approval -->
<div class="info-banner warning">
    <div class="icon">⚠️</div>
    <div class="text">
        <h4>Absen Pulang Perlu Persetujuan</h4>
        <p>Anda terjadwal WFO tetapi saat ini berada di luar kantor. Absen pulang akan masuk ke daftar pending dan memerlukan persetujuan atasan.</p>
    </div>
</div>

<div class="card">
    <h3 class="card-title">Form Alasan Absen Pulang di Luar Kantor</h3>

    <div class="summary-box">
        <div class="summary-item">
            <span class="summary-label">Tanggal</span>
            <span class="summary-value">{{ now()->format('d M Y') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Jam Saat Ini</span>
            <span class="summary-value">{{ $info['jam'] }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Jadwal Hari Ini</span>
            <span class="summary-value">WFO</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Posisi Saat Ini</span>
            <span class="summary-value" style="color: #dc2626;">Di Luar Kantor</span>
        </div>
    </div>

    <form action="{{ route('absen.storePulang') }}" method="POST">
        @csrf

        <div class="form-row">
            <label class="form-label required" for="alasan_pulang">Alasan Absen Pulang di Luar Kantor</label>
            <textarea
                name="alasan_pulang"
                id="alasan_pulang"
                class="form-textarea"
                placeholder="Jelaskan alasan kenapa Anda absen pulang dari luar kantor..."
                required>{{ old('alasan_pulang') }}</textarea>
            <p class="form-hint">Wajib diisi. Absen akan menunggu persetujuan atasan.</p>
            @error('alasan_pulang')
            <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <div class="btn-row">
            <a href="{{ route('absen.index') }}" class="btn btn-secondary">
                ← Kembali
            </a>
            <button type="submit" class="btn btn-primary pulang">
                📤 Ajukan Absen Pulang
            </button>
        </div>
    </form>
</div>
@else
<!-- Absen Pulang Langsung (WFA atau WFO di kantor) -->
<div class="info-banner pulang">
    <div class="icon">🏃</div>
    <div class="text">
        <h4>Konfirmasi Absen Pulang</h4>
        <p>Klik tombol di bawah untuk konfirmasi absen pulang.</p>
    </div>
</div>

<div class="card">
    <h3 class="card-title">Konfirmasi Absen Pulang</h3>

    <div class="summary-box">
        <div class="summary-item">
            <span class="summary-label">Tanggal</span>
            <span class="summary-value">{{ now()->format('d M Y') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Jam Saat Ini</span>
            <span class="summary-value">{{ $info['jam'] }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Jadwal Hari Ini</span>
            <span class="summary-value">{{ $jadwal['is_wfo'] ? 'WFO' : 'WFA' }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Posisi</span>
            <span class="summary-value">{{ $info['posisi_label'] }}</span>
        </div>
    </div>

    <form action="{{ route('absen.storePulang') }}" method="POST">
        @csrf
        <div class="btn-row">
            <a href="{{ route('absen.index') }}" class="btn btn-secondary">
                ← Kembali
            </a>
            <button type="submit" class="btn btn-primary pulang">
                🏃 Konfirmasi Absen Pulang
            </button>
        </div>
    </form>
</div>
@endif

@else
{{-- ==================== FORM ABSEN MASUK ==================== --}}

<!-- Info Banner -->
<div class="info-banner masuk">
    <div class="icon">🏢</div>
    <div class="text">
        <h4>Formulir {{ $formType === 'masuk' ? 'Masuk' : 'Izin' }}</h4>
        <p>
            @if($formType === 'masuk')
            Anda terjadwal WFO dan berada di kantor. Pilih status kehadiran Anda.
            @else
            Anda {{ $jadwal['is_wfo'] ? 'terjadwal WFO tapi di luar kantor' : 'terjadwal WFA' }}. Lengkapi formulir dibawah.
            @endif
        </p>
    </div>
</div>

<div class="card">
    <h3 class="card-title">
        @if($formType === 'masuk')
        Formulir Masuk
        @else
        Formulir Izin
        @endif
    </h3>

    <form action="{{ route('absen.storeMasuk') }}" method="POST">
        @csrf

        <!-- Summary Info -->
        <div class="summary-box">
            <div class="summary-item">
                <span class="summary-label">Jam</span>
                <span class="summary-value">{{ $info['jam'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Jadwal</span>
                <span class="summary-value">{{ $jadwal['is_wfo'] ? 'WFO' : 'WFA' }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Posisi</span>
                <span class="summary-value">{{ $info['posisi_label'] }}</span>
            </div>
        </div>

        <!-- Status Selection -->
        <div class="form-row">
            <label class="form-label required">Pilih Status</label>
            <div class="radio-group">
                @foreach($statusOptions as $option)
                <label class="radio-item {{ old('status') === $option['value'] ? 'selected' : '' }}">
                    <input
                        type="radio"
                        name="status"
                        value="{{ $option['value'] }}"
                        {{ old('status') === $option['value'] ? 'checked' : '' }}
                        onchange="handleStatusChange(this)">
                    <div class="radio-content">
                        <div class="radio-title">{{ $option['icon'] }} {{ $option['label'] }}</div>
                        <div class="radio-desc">{{ $option['desc'] }}</div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('status')
            <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- Tanggal Periode (hanya untuk dinas/sakit_izin) -->
        <div class="form-row" id="tanggalRow" style="display: none;">
            <label class="form-label required">Tanggal Periode</label>
            <div style="display: flex; gap: 12px; align-items: center;">
                <div style="flex: 1;">
                    <label class="form-label" for="tanggal_from" style="font-size: 12px; margin-bottom: 4px;">Dari</label>
                    <input 
                        type="date" 
                        name="tanggal_from" 
                        id="tanggal_from" 
                        class="form-input"
                        value="{{ old('tanggal_from', now()->format('Y-m-d')) }}"
                        min="{{ now()->format('Y-m-d') }}">
                </div>
                <div style="flex: 1;">
                    <label class="form-label" for="tanggal_to" style="font-size: 12px; margin-bottom: 4px;">Sampai</label>
                    <input 
                        type="date" 
                        name="tanggal_to" 
                        id="tanggal_to" 
                        class="form-input"
                        value="{{ old('tanggal_to', now()->format('Y-m-d')) }}"
                        min="{{ now()->format('Y-m-d') }}">
                </div>
            </div>
            <p class="form-hint">Pilih tanggal periode.</p>
            @error('tanggal_from')
            <span class="error-text">{{ $message }}</span>
            @enderror
            @error('tanggal_to')
            <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- Alasan (wajib untuk dinas/Sakit/Izin) -->
        <div class="form-row" id="alasanRow" style="display: none;">
            <label class="form-label required" for="alasan">Alasan / Keterangan</label>
            <textarea
                name="alasan"
                id="alasan"
                class="form-textarea"
                placeholder="Jelaskan alasan...">{{ old('alasan') }}</textarea>
            <p class="form-hint">Alasan wajib diisi dengan jelas!</p>
            @error('alasan')
            <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <!-- WFA Fields -->
        <div class="wfa-fields" id="wfaFields">
            <h4 style="margin-bottom: 16px; color: #065f46;">🏠 Detail WFA</h4>

            <div class="form-row">
                <label class="form-label required" for="wfa_lokasi">Lokasi WFA</label>
                <select name="wfa_lokasi" id="wfa_lokasi" class="form-select" onchange="handleWfaLokasiChange(this)">
                    <option value="">-- Pilih Lokasi --</option>
                    <option value="1" {{ old('wfa_lokasi') === '1' ? 'selected' : '' }}>🏠 Di Rumah</option>
                    <option value="2" {{ old('wfa_lokasi') === '2' ? 'selected' : '' }}>🌍 Di Luar Rumah</option>
                </select>
                @error('wfa_lokasi')
                <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="wfa-detail-field" id="wfaDetailField">
                <div class="form-row">
                    <label class="form-label required" for="wfa_detail">Detail Lokasi</label>
                    <input
                        type="text"
                        name="wfa_detail"
                        id="wfa_detail"
                        class="form-input"
                        placeholder="Contoh: Cafe XYZ, Coworking Space ABC, dll"
                        value="{{ old('wfa_detail') }}">
                    <p class="form-hint">Sebutkan lokasi spesifik Anda bekerja.</p>
                    @error('wfa_detail')
                    <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <label class="form-label required" for="wfa_alasan">Alasan WFA</label>
                <textarea
                    name="wfa_alasan"
                    id="wfa_alasan"
                    class="form-textarea"
                    placeholder="Jelaskan alasan bekerja dari luar kantor...">{{ old('wfa_alasan') }}</textarea>
                @error('wfa_alasan')
                <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Hidden fields -->
        <input type="hidden" name="form_type" value="{{ $formType }}">

        <div class="btn-row">
            <a href="{{ route('absen.index') }}" class="btn btn-secondary">
                ← Kembali
            </a>
            <button type="submit" class="btn btn-primary masuk">
                ✓ Submit Absen Masuk
            </button>
        </div>
    </form>
</div>
@endif

<script>
    function handleStatusChange(radio) {
        // Update selected style
        document.querySelectorAll('.radio-item').forEach(item => {
            item.classList.remove('selected');
        });
        radio.closest('.radio-item').classList.add('selected');

        const statusVal = radio.value;
        const tanggalRow = document.getElementById('tanggalRow');
        const alasanRow = document.getElementById('alasanRow');
        const wfaFields = document.getElementById('wfaFields');

        // Reset visibility
        if (tanggalRow) tanggalRow.style.display = 'none';
        alasanRow.style.display = 'none';
        wfaFields.classList.remove('show');

        // Show appropriate fields
        if (statusVal === 'dinas' || statusVal === 'sakit_izin') {
            // Dinas atau Sakit/Izin -> tampilkan tanggal dan alasan
            if (tanggalRow) tanggalRow.style.display = 'block';
            alasanRow.style.display = 'block';
        } else if (statusVal === 'wfa') {
            // WFA -> langsung tanpa tanggal (hanya detail WFA)
            wfaFields.classList.add('show');
        }
    }

    function handleWfaLokasiChange(select) {
        const wfaDetailField = document.getElementById('wfaDetailField');

        if (select.value === '2') {
            // Di luar rumah - tampilkan field detail
            wfaDetailField.classList.add('show');
        } else {
            // Di rumah - sembunyikan field detail
            wfaDetailField.classList.remove('show');
        }
    }

    // Sync tanggal_to dengan tanggal_from jika tanggal_to kosong atau lebih kecil
    document.getElementById('tanggal_from')?.addEventListener('change', function() {
        const toField = document.getElementById('tanggal_to');
        if (toField && (!toField.value || toField.value < this.value)) {
            toField.value = this.value;
        }
        toField.min = this.value;
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const checkedRadio = document.querySelector('input[name="status"]:checked');
        if (checkedRadio) {
            handleStatusChange(checkedRadio);
        }

        const wfaLokasiSelect = document.getElementById('wfa_lokasi');
        if (wfaLokasiSelect && wfaLokasiSelect.value) {
            handleWfaLokasiChange(wfaLokasiSelect);
        }
    });
</script>
@endsection