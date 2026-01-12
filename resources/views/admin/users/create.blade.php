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
      
      /* Password Strength Indicator */
      .password-wrapper{position:relative}
      .password-strength{
        margin-top:8px;
        padding:12px;
        background:#f8fafc;
        border-radius:8px;
        border:1px solid #e2e8f0;
        max-height:0;
        overflow:hidden;
        opacity:0;
        transition: max-height 0.4s ease, opacity 0.3s ease, padding 0.3s ease, margin 0.3s ease;
        padding:0 12px;
        margin-top:0;
      }
      .password-strength.show{
        max-height:300px;
        opacity:1;
        padding:12px;
        margin-top:8px;
      }
      .password-strength.hide-complete{
        max-height:0;
        opacity:0;
        padding:0 12px;
        margin-top:0;
      }
      .strength-title{font-size:12px;font-weight:600;color:#374151;margin-bottom:8px}
      .strength-bar{height:6px;background:#e5e7eb;border-radius:3px;overflow:hidden;margin-bottom:10px}
      .strength-bar-fill{height:100%;width:0%;transition:width 0.4s cubic-bezier(0.4, 0, 0.2, 1), background 0.3s ease;border-radius:3px}
      .strength-bar-fill.weak{background:#ef4444;width:20%}
      .strength-bar-fill.fair{background:#f97316;width:40%}
      .strength-bar-fill.good{background:#eab308;width:60%}
      .strength-bar-fill.strong{background:#22c55e;width:80%}
      .strength-bar-fill.excellent{background:#10b981;width:100%}
      .strength-list{display:flex;flex-direction:column;gap:4px}
      .strength-item{
        display:flex;
        align-items:center;
        gap:8px;
        font-size:12px;
        color:#6b7280;
        transition: opacity 0.3s ease, transform 0.3s ease, color 0.3s ease;
        transform: translateX(0);
      }
      .strength-item.valid{color:#16a34a;transform:translateX(4px)}
      .strength-item.invalid{color:#dc2626}
      .strength-icon{
        width:16px;
        height:16px;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:11px;
        transition: transform 0.3s ease, color 0.3s ease;
      }
      .strength-icon.valid{color:#16a34a;transform:scale(1.2)}
      .strength-icon.invalid{color:#dc2626;transform:scale(1)}
      .strength-label{
        font-size:12px;
        font-weight:500;
        margin-top:8px;
        transition: color 0.3s ease;
      }
      .strength-label.weak{color:#ef4444}
      .strength-label.fair{color:#f97316}
      .strength-label.good{color:#eab308}
      .strength-label.strong{color:#22c55e}
      .strength-label.excellent{color:#10b981}
      
      /* Success checkmark animation */
      @keyframes checkPop {
        0% { transform: scale(0); }
        50% { transform: scale(1.4); }
        100% { transform: scale(1.2); }
      }
      .strength-icon.valid.animate {
        animation: checkPop 0.3s ease forwards;
      }
      
      /* Complete state - success message */
      .password-complete {
        display: none;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
        padding: 10px 12px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        border-radius: 8px;
        color: #065f46;
        font-size: 13px;
        font-weight: 500;
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity 0.3s ease, transform 0.3s ease;
      }
      .password-complete.show {
        display: flex;
        opacity: 1;
        transform: translateY(0);
      }
      .password-complete-icon {
        font-size: 16px;
      }
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
              <div class="password-wrapper">
                <input type="password" id="password" name="password" required minlength="8" maxlength="255" placeholder="min 8 karakter" autocomplete="new-password">
                <div class="password-strength" id="passwordStrength">
                  <div class="strength-title">Kekuatan Password</div>
                  <div class="strength-bar">
                    <div class="strength-bar-fill" id="strengthBarFill"></div>
                  </div>
                  <div class="strength-label" id="strengthLabel">Sangat Lemah</div>
                  <div class="strength-list">
                    <div class="strength-item" id="check-length">
                      <span class="strength-icon invalid" id="icon-length">✗</span>
                      <span>Minimal 8 karakter</span>
                    </div>
                    <div class="strength-item" id="check-lowercase">
                      <span class="strength-icon invalid" id="icon-lowercase">✗</span>
                      <span>Huruf kecil (a-z)</span>
                    </div>
                    <div class="strength-item" id="check-uppercase">
                      <span class="strength-icon invalid" id="icon-uppercase">✗</span>
                      <span>Huruf besar (A-Z)</span>
                    </div>
                    <div class="strength-item" id="check-number">
                      <span class="strength-icon invalid" id="icon-number">✗</span>
                      <span>Angka (0-9)</span>
                    </div>
                    <div class="strength-item" id="check-symbol">
                      <span class="strength-icon invalid" id="icon-symbol">✗</span>
                      <span>Simbol (!@#$%^&*...)</span>
                    </div>
                  </div>
                </div>
                <div class="password-complete" id="passwordComplete">
                  <span class="password-complete-icon">✓</span>
                  <span>Password sudah memenuhi semua kriteria!</span>
                </div>
              </div>
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
              <input type="tel" id="telp" name="telp" required maxlength="25" value="{{ old('telp') }}" pattern="[+]?[0-9\s\-]+" title="Format: 08xx, 628xx, +62 8xx, atau dengan strip">
              <div style="font-size:11px;color:var(--text-muted);margin-top:4px"></div>
              <div style="font-size:11px;color:var(--text-muted);margin-top:4px"></div>
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

        // Password Strength Checker
        const passwordInput = document.getElementById('password');
        const strengthPanel = document.getElementById('passwordStrength');
        const strengthBarFill = document.getElementById('strengthBarFill');
        const strengthLabel = document.getElementById('strengthLabel');
        const passwordComplete = document.getElementById('passwordComplete');
        const submitBtn = document.querySelector('button[type="submit"]');
        let previousScore = 0;

        const checks = {
          length: { el: document.getElementById('check-length'), icon: document.getElementById('icon-length'), test: (p) => p.length >= 8 },
          lowercase: { el: document.getElementById('check-lowercase'), icon: document.getElementById('icon-lowercase'), test: (p) => /[a-z]/.test(p) },
          uppercase: { el: document.getElementById('check-uppercase'), icon: document.getElementById('icon-uppercase'), test: (p) => /[A-Z]/.test(p) },
          number: { el: document.getElementById('check-number'), icon: document.getElementById('icon-number'), test: (p) => /[0-9]/.test(p) },
          symbol: { el: document.getElementById('check-symbol'), icon: document.getElementById('icon-symbol'), test: (p) => /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?`~]/.test(p) }
        };

        function updateStrength() {
          const password = passwordInput.value;
          let score = 0;

          // Show panel when typing (only if not complete)
          if (password.length > 0) {
            strengthPanel.classList.add('show');
            strengthPanel.classList.remove('hide-complete');
          } else {
            strengthPanel.classList.remove('show');
            passwordComplete.classList.remove('show');
          }

          // Check each requirement
          for (const key in checks) {
            const check = checks[key];
            const passed = check.test(password);
            const wasValid = check.el.classList.contains('valid');
            
            if (passed) {
              score++;
              if (!wasValid) {
                // Animate when newly valid
                check.icon.classList.remove('animate');
                void check.icon.offsetWidth; // Trigger reflow
                check.icon.classList.add('animate');
              }
              check.el.classList.add('valid');
              check.el.classList.remove('invalid');
              check.icon.classList.add('valid');
              check.icon.classList.remove('invalid');
              check.icon.textContent = '✓';
            } else {
              check.el.classList.remove('valid');
              check.el.classList.add('invalid');
              check.icon.classList.remove('valid', 'animate');
              check.icon.classList.add('invalid');
              check.icon.textContent = '✗';
            }
          }

          // Update strength bar and label
          strengthBarFill.className = 'strength-bar-fill';
          strengthLabel.className = 'strength-label';

          if (score === 0) {
            strengthLabel.textContent = 'Sangat Lemah';
            strengthLabel.classList.add('weak');
          } else if (score === 1) {
            strengthBarFill.classList.add('weak');
            strengthLabel.textContent = 'Sangat Lemah';
            strengthLabel.classList.add('weak');
          } else if (score === 2) {
            strengthBarFill.classList.add('fair');
            strengthLabel.textContent = 'Lemah';
            strengthLabel.classList.add('fair');
          } else if (score === 3) {
            strengthBarFill.classList.add('good');
            strengthLabel.textContent = 'Cukup';
            strengthLabel.classList.add('good');
          } else if (score === 4) {
            strengthBarFill.classList.add('strong');
            strengthLabel.textContent = 'Kuat';
            strengthLabel.classList.add('strong');
          } else if (score === 5) {
            strengthBarFill.classList.add('excellent');
            strengthLabel.textContent = 'Sangat Kuat';
            strengthLabel.classList.add('excellent');
            
            // Hide checker and show success message after short delay
            setTimeout(() => {
              if (updateStrength.latestScore === 5) {
                strengthPanel.classList.remove('show');
                strengthPanel.classList.add('hide-complete');
                setTimeout(() => {
                  passwordComplete.classList.add('show');
                }, 200);
              }
            }, 600);
          }

          // If score drops from 5, show panel again
          if (previousScore === 5 && score < 5) {
            passwordComplete.classList.remove('show');
            setTimeout(() => {
              strengthPanel.classList.remove('hide-complete');
              strengthPanel.classList.add('show');
            }, 100);
          }

          previousScore = score;
          updateStrength.latestScore = score;
          return score;
        }

        passwordInput.addEventListener('input', updateStrength);
        passwordInput.addEventListener('focus', function() {
          const score = updateStrength();
          if (this.value.length > 0 && score < 5) {
            strengthPanel.classList.add('show');
            strengthPanel.classList.remove('hide-complete');
          }
        });

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
          const score = updateStrength();
          if (score < 5) {
            e.preventDefault();
            alert('Password harus memenuhi semua kriteria:\n- Minimal 8 karakter\n- Huruf kecil (a-z)\n- Huruf besar (A-Z)\n- Angka (0-9)\n- Simbol (!@#$%^&*...)');
            passwordInput.focus();
            passwordComplete.classList.remove('show');
            strengthPanel.classList.remove('hide-complete');
            strengthPanel.classList.add('show');
          }
        });
      });
    </script>
  </body>
</html>
