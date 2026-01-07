<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <style>
      body{font-family:Inter,system-ui,Arial;margin:0;background:#f4f7fa}
      .wrap{max-width:900px;margin:48px auto;padding:24px}
      .card{background:#fff;padding:20px;border-radius:8px;box-shadow:0 6px 20px rgba(20,20,60,0.06)}
      .meta{display:flex;justify-content:space-between;align-items:center}
      .meta .who{font-size:18px}
      .logout button{padding:8px 12px;border-radius:6px;border:none;background:#e11d48;color:#fff;cursor:pointer}
      .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-top:14px}
      @media(max-width:900px){.grid{grid-template-columns:1fr}}
      .tile{display:block;text-decoration:none;color:#111;border:1px solid #eef0f6;border-radius:10px;padding:14px;background:#fff}
      .tile:hover{border-color:#cdd3f7;box-shadow:0 6px 18px rgba(89,102,247,0.12)}
      .tile .t{font-weight:700;margin-bottom:6px}
      .tile .d{color:#6b7280;font-size:13px}
      .pill{display:inline-block;font-size:12px;padding:3px 8px;border-radius:100px;background:#eef2ff;color:#3730a3;margin-left:8px}
      .sectionTitle{margin:10px 0 0;font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:.08em}
    </style>
  </head>
  <body>
    <div class="wrap">
      <div class="card">
        <div class="meta">
          <div class="who">
            Halo, <strong>{{ $user->nama ?? $user->username }}</strong> — role: <strong>{{ $role }}</strong>
          </div>
          <div class="logout">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit">Logout</button>
            </form>
          </div>
        </div>

        <hr style="margin:16px 0">

        @if(session('status'))
          <div style="margin:0 0 12px;color:#065f46;background:#ecfdf5;border:1px solid #a7f3d0;padding:10px;border-radius:8px">{{ session('status') }}</div>
        @endif

        <p style="margin:0 0 12px;color:#6b7280">Pilih menu yang ingin dibuka:</p>

        <div class="grid">
          @if(in_array(($role ?? ''), ['admin', 'ADMIN'], true))
            <a class="tile" href="{{ route('admin.kalender') }}">
              <div class="t">Kalender Kerja <span class="pill">Admin</span></div>
              <div class="d">Input periode (minggu Senin–Minggu) dan lihat data kalender kerja.</div>
            </a>

            <a class="tile" href="{{ route('settings.index') }}">
              <div class="t">Setting User <span class="pill">Admin</span></div>
              <div class="d">Kelola user, biro, jabatan, dan role dalam satu tempat.</div>
            </a>
          @endif

          <a class="tile" href="#" onclick="return false;">
            <div class="t">(Coming soon) Fitur lainnya</div>
            <div class="d">Nanti bisa ditambah menu lain di sini (absensi, izin, dsb.).</div>
          </a>
        </div>
      </div>
    </div>
  </body>
</html>
