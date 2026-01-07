<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah User</title>
    <style>
      :root{--bg:#f4f7fa;--card:#fff;--accent:#5966f7;--muted:#6b7280}
      *{box-sizing:border-box;font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial}
      body{margin:0;background:var(--bg)}
      .wrap{max-width:900px;margin:32px auto;padding:0 16px}
      .card{background:var(--card);border-radius:10px;box-shadow:0 6px 20px rgba(20,20,60,0.06);padding:18px}
      label{display:block;font-size:12px;color:var(--muted);margin-bottom:6px}
      select,input{width:100%;padding:11px 12px;border:1px solid #eef0f6;border-radius:8px;background:#fff;outline:none;font-size:14px}
      select:focus,input:focus{box-shadow:0 0 0 3px rgba(89,102,247,0.08);border-color:var(--accent)}
      .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
      @media(max-width:900px){.grid{grid-template-columns:1fr}}
      .actions{margin-top:16px;display:flex;gap:10px;justify-content:space-between;align-items:center;flex-wrap:wrap}
      .btn{padding:11px 14px;border-radius:10px;border:none;cursor:pointer;font-weight:600}
      .btn.primary{background:var(--accent);color:#fff;box-shadow:0 6px 18px rgba(89,102,247,0.18)}
      .error{margin:10px 0 0;color:#991b1b;background:#fff1f2;border:1px solid #fecdd3;padding:10px;border-radius:8px}
      a{color:inherit}
      /* Select2 custom styling */
      .select2-container{width:100%!important}
      .select2-container .select2-selection--single{min-height:47px;height:47px;display:flex;align-items:center;padding:11px 12px;border:1px solid #eef0f6;border-radius:8px;background:#fff;font-size:14px;font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial;line-height:1.4}
      .select2-container--default .select2-selection--single .select2-selection__arrow{height:100%;top:0;right:8px}
      .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:1.4;padding:0;font-family:inherit;color:#000}
      .select2-container--default .select2-selection--single .select2-selection__placeholder{color:#9ca3af}
      .select2-container--default.select2-container--focus .select2-selection--single{box-shadow:0 0 0 3px rgba(89,102,247,0.08);border-color:var(--accent)}
      .select2-dropdown{border:1px solid #eef0f6;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.1);font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial}
      .select2-search--dropdown .select2-search__field{padding:8px;border:1px solid #eef0f6;border-radius:6px;font-family:inherit;font-size:14px}
      .select2-results__option{padding:8px 12px;font-family:inherit;font-size:14px}
      .select2-container--default .select2-results__option--highlighted[aria-selected]{background:var(--accent);color:#fff}
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  </head>
  <body>
    <div class="wrap">
      <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
          <div>
            <h1 style="margin:0;font-size:20px">Tambah User</h1>
            <div style="color:var(--muted);font-size:12px">Form untuk menambahkan user baru.</div>
          </div>
            <div>
              @include('partials.back', ['fallback' => route('settings.user')])
            </div>
        </div>

        @if(session('status'))
          <div style="margin-top:10px;color:#065f46;background:#ecfdf5;border:1px solid #a7f3d0;padding:10px;border-radius:8px">{{ session('status') }}</div>
        @endif

        @if($errors->any())
          <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('users.store') }}" style="margin-top:14px">
          @csrf

          <div class="grid">
            <div>
              <label for="nama">Nama</label>
              <input type="text" id="nama" name="nama" required maxlength="50" value="{{ old('nama') }}" placeholder="Nama lengkap">
            </div>

            <div>
              <label for="role_id">Role</label>
              <select id="role_id" name="role_id" required>
                <option value="" disabled selected>-</option>
                @foreach($roles as $r)
                  <option value="{{ $r->id }}" @selected(old('role_id') == $r->id)>{{ $r->role_name }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label for="biro_id">Biro</label>
              <select id="biro_id" name="biro_id" required>
                <option value="" disabled selected>-</option>
                @foreach($biros as $b)
                  <option value="{{ $b->id }}" @selected(old('biro_id') == $b->id)>{{ $b->biro_name }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label for="username">Username</label>
              <input type="text" id="username" name="username" required maxlength="35" value="{{ old('username') }}">
            </div>

            <div>
              <label for="password">Password</label>
              <input type="password" id="password" name="password" required minlength="8" maxlength="255" placeholder="min 8 karakter">
            </div>

            <div>
              <label for="nip">NIP</label>
              <input type="text" id="nip" name="nip" required maxlength="35" value="{{ old('nip') }}">
            </div>

            <div>
              <label for="nip_atasan">NIP Atasan</label>
              <select id="nip_atasan" name="nip_atasan" required>
                <option value="" disabled selected>-</option>
                @foreach($nipAtasanOptions as $nip)
                  <option value="{{ $nip }}" @selected(old('nip_atasan') == $nip)>{{ $nip }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label for="email">Email</label>
              <input type="text" id="email" name="email" required maxlength="50" value="{{ old('email') }}">
            </div>

            <div>
              <label for="telp">No. Telp</label>
              <input type="tel" id="telp" name="telp" required maxlength="20" value="{{ old('telp') }}" pattern="[0-9]+" title="Hanya angka yang diperbolehkan">
            </div>

            <div>
              <label for="tgl_lahir">Tanggal Lahir</label>
              <input type="date" id="tgl_lahir" name="tgl_lahir" required value="{{ old('tgl_lahir') }}" placeholder="yyyy-mm-dd">
            </div>

            <div>
              <label for="jabatan_id">Jabatan</label>
              <select id="jabatan_id" name="jabatan_id" required>
                <option value="" disabled selected>-</option>
                @foreach($jabatans as $j)
                  <option value="{{ $j->id }}" @selected(old('jabatan_id') == $j->id)>{{ $j->jabatan }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label for="isdel">Mode Kerja</label>
              <select id="isdel" name="isdel" required>
                <option value="1" @selected(old('isdel', '0') == '1')>WFO</option>
                <option value="0" @selected(old('isdel', '0') == '0')>WFA</option>
              </select>
            </div>

            <div>
              <label for="is_kirim">Kirim Notif Absensi</label>
              <select id="is_kirim" name="is_kirim" required>
                <option value="1" @selected(old('is_kirim', '0') == '1')>Ya (kirim)</option>
                <option value="0" @selected(old('is_kirim', '0') == '0')>Tidak</option>
              </select>
            </div>

            <div>
              <label for="is_pulang">Status Pulang</label>
              <select id="is_pulang" name="is_pulang" required>
                <option value="1" @selected(old('is_pulang', '0') == '1')>Pulang</option>
                <option value="0" @selected(old('is_pulang', '0') == '0')>Belum</option>
              </select>
            </div>
          </div>

          <div class="actions">
            <div></div>
            <button type="submit" class="btn primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
      $(document).ready(function() {
        
        $('#role_id, #biro_id, #jabatan_id, #isdel, #is_pulang').select2({
          placeholder: "Pilih...",
          allowClear: false,
          width: '100%'
        });
        
        $(document).on('select2:open', () => {
          setTimeout(() => {
            document.querySelector('.select2-search__field').focus();
          }, 100);
        });
      });
    </script>
  </body>
</html>
