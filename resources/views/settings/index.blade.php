<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setting User</title>
    <style>
      :root{--bg:#f4f7fa;--card:#fff;--accent:#5966f7;--muted:#6b7280}
      *{box-sizing:border-box;font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial}
      body{margin:0;background:var(--bg)}
      .wrap{max-width:900px;margin:32px auto;padding:0 16px}
      .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:12px;flex-wrap:wrap}
      h1{margin:0;font-size:22px}
      .muted{color:var(--muted)}
      .btn{padding:10px 12px;border-radius:10px;border:1px solid #eef0f6;background:#fff;cursor:pointer;font-weight:600}
      .btn.primary{background:var(--accent);border-color:var(--accent);color:#fff;box-shadow:0 6px 18px rgba(89,102,247,0.18)}
      .card{background:var(--card);border-radius:10px;box-shadow:0 6px 20px rgba(20,20,60,0.06);padding:18px}
      .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
      @media(max-width:900px){.grid{grid-template-columns:1fr}}
      a{text-decoration:none;color:inherit}
      .tile{border:1px solid #eef0f6;border-radius:10px;padding:14px;background:#fff}
      .tile:hover{border-color:#cdd3f7;box-shadow:0 6px 18px rgba(89,102,247,0.12)}
      .t{font-weight:800;margin-bottom:6px}
      .d{color:var(--muted);font-size:13px}
    </style>
  </head>
  <body>
    <div class="wrap">
      <div class="top">
        <div>
          <h1>Setting User</h1>
          <div class="muted" style="font-size:12px">Pilih modul yang mau dikelola.</div>
        </div>
        @include('partials.back', ['fallback' => route('dashboard')])
      </div>

      <div class="card">
        <div class="grid">
          <a class="tile" href="{{ route('settings.user') }}">
            <div class="t">User</div>
            <div class="d">Add user, lihat/edit/hapus user.</div>
          </a>

          <a class="tile" href="{{ route('settings.biro') }}">
            <div class="t">Biro</div>
            <div class="d">Add biro, lihat/edit/hapus biro.</div>
          </a>

          <a class="tile" href="{{ route('settings.jabatan') }}">
            <div class="t">Jabatan</div>
            <div class="d">Add jabatan, lihat/edit/hapus jabatan.</div>
          </a>

          <a class="tile" href="{{ route('settings.role') }}">
            <div class="t">Role</div>
            <div class="d">Add role, lihat/edit/hapus role.</div>
          </a>
        </div>
      </div>
    </div>
  </body>
</html>
