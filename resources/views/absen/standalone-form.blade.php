<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Absen - {{ $user->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            padding: 32px;
            max-width: 480px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 24px;
            color: #1a202c;
            margin-bottom: 8px;
        }

        .header p {
            color: #718096;
            font-size: 14px;
        }

        .user-info {
            background: #f7fafc;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
        }

        .user-detail .user-name {
            font-weight: 600;
            color: #2d3748;
        }

        .user-detail .user-nip {
            font-size: 13px;
            color: #718096;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
        }

        .form-label.required::after {
            content: ' *';
            color: #e53e3e;
        }

        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .radio-item {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .radio-item:hover {
            border-color: #667eea;
            background: #f7fafc;
        }

        .radio-item.selected {
            border-color: #667eea;
            background: #ebf4ff;
        }

        .radio-item input[type="radio"] {
            display: none;
        }

        .radio-content {
            margin-left: 8px;
        }

        .radio-title {
            font-weight: 600;
            color: #2d3748;
            font-size: 15px;
        }

        .radio-desc {
            font-size: 12px;
            color: #718096;
            margin-top: 2px;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-textarea {
            min-height: 80px;
            resize: vertical;
        }

        .date-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .hidden {
            display: none;
        }

        .btn {
            width: 100%;
            padding: 14px 24px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .error-text {
            color: #e53e3e;
            font-size: 12px;
            margin-top: 4px;
        }

        @media (max-width: 480px) {
            .card {
                padding: 24px;
            }

            .date-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>Form Absen</h1>
            <p>{{ $tanggalFormatted }}</p>
        </div>

        <div class="user-info">
            <div class="user-avatar">{{ strtoupper(substr($user->nama, 0, 1)) }}</div>
            <div class="user-detail">
                <div class="user-name">{{ $user->nama }}</div>
                <div class="user-nip">NIP: {{ $user->nip }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('absen.store.standalone', ['nip' => $nip, 'tanggal' => $tanggal]) }}" id="absenForm">
            @csrf

            <!-- Status Selection -->
            <div class="form-group">
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
                            <div class="radio-title">{{ $option['label'] }}</div>
                            <div class="radio-desc">{{ $option['desc'] }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('status')
                <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- WFA Fields -->
            <div id="wfaFields" class="hidden">
                <div class="form-group">
                    <label class="form-label required">Lokasi WFA</label>
                    <select name="wfa_lokasi" class="form-select" onchange="handleWfaLokasiChange(this)">
                        <option value="">-- Pilih Lokasi --</option>
                        <option value="1" {{ old('wfa_lokasi') === '1' ? 'selected' : '' }}>Di Rumah</option>
                        <option value="2" {{ old('wfa_lokasi') === '2' ? 'selected' : '' }}>Di Luar Rumah</option>
                    </select>
                    @error('wfa_lokasi')
                    <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group hidden" id="wfaDetailField">
                    <label class="form-label required">Detail Lokasi</label>
                    <input type="text" name="wfa_detail" class="form-input" placeholder="Contoh: Kafe XYZ, Coworking Space ABC" value="{{ old('wfa_detail') }}">
                    @error('wfa_detail')
                    <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label required">Alasan/Keterangan WFA</label>
                    <textarea name="wfa_alasan" class="form-textarea" placeholder="Jelaskan alasan WFA...">{{ old('wfa_alasan') }}</textarea>
                    @error('wfa_alasan')
                    <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Izin Fields (Dinas/Sakit) -->
            <div id="izinFields" class="hidden">
                <div class="form-group">
                    <label class="form-label required">Tanggal Periode</label>
                    <div class="date-row">
                        <div>
                            <input type="date" name="tanggal_from" class="form-input" value="{{ old('tanggal_from', date('Y-m-d')) }}">
                            @error('tanggal_from')
                            <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <input type="date" name="tanggal_to" class="form-input" value="{{ old('tanggal_to', date('Y-m-d')) }}">
                            @error('tanggal_to')
                            <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label required">Alasan</label>
                    <textarea name="alasan" class="form-textarea" placeholder="Jelaskan alasan...">{{ old('alasan') }}</textarea>
                    @error('alasan')
                    <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                Kirim Absen
            </button>
        </form>
    </div>

    <script>
        function handleStatusChange(radio) {
            const wfaFields = document.getElementById('wfaFields');
            const izinFields = document.getElementById('izinFields');
            const submitBtn = document.getElementById('submitBtn');

            // Reset
            wfaFields.classList.add('hidden');
            izinFields.classList.add('hidden');

            // Update selected style
            document.querySelectorAll('.radio-item').forEach(item => {
                item.classList.remove('selected');
            });
            radio.closest('.radio-item').classList.add('selected');

            // Show appropriate fields
            const statusVal = radio.value;
            if (statusVal === 'wfa') {
                wfaFields.classList.remove('hidden');
            } else if (statusVal === 'dinas' || statusVal === 'sakit_izin') {
                izinFields.classList.remove('hidden');
            }

            // Enable submit button
            submitBtn.disabled = false;
        }

        function handleWfaLokasiChange(select) {
            const wfaDetailField = document.getElementById('wfaDetailField');
            if (select.value === '2') {
                wfaDetailField.classList.remove('hidden');
            } else {
                wfaDetailField.classList.add('hidden');
            }
        }

        // Initialize on page load (for validation errors)
        document.addEventListener('DOMContentLoaded', function() {
            const checkedRadio = document.querySelector('input[name="status"]:checked');
            if (checkedRadio) {
                handleStatusChange(checkedRadio);
            }

            const wfaLokasiSelect = document.querySelector('select[name="wfa_lokasi"]');
            if (wfaLokasiSelect && wfaLokasiSelect.value) {
                handleWfaLokasiChange(wfaLokasiSelect);
            }
        });
    </script>
</body>
</html>
