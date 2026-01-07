<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Coming Soon</title>
    <style>
      :root{--bg:#f5f6fa;--card:#fff;--accent:#5f73ff;--muted:#6b7280;--border:#e7eaf3}
      *{box-sizing:border-box;font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial}
      body{margin:0;background:var(--bg);color:#111827}
      .wrap{max-width:900px;margin:32px auto;padding:0 16px}
      .card{background:var(--card);border-radius:14px;border:1px solid var(--border);box-shadow:0 10px 35px rgba(35,45,120,.08);padding:18px}
      .muted{color:var(--muted)}
      .btn{padding:10px 12px;border-radius:10px;border:1px solid #eef0f6;background:#fff;cursor:pointer;font-weight:700}
      .btn.primary{background:var(--accent);border-color:var(--accent);color:#fff;box-shadow:0 6px 18px rgba(89,102,247,0.18)}
      a{text-decoration:none;color:inherit}
    </style>
  </head>
  <body>
    <div class="wrap">
      <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:12px">
        <div>
          <h1 style="margin:0;font-size:22px">Fitur belum tersedia</h1>
          <div class="muted" style="font-size:12px">Halaman ini masih coming soon.</div>
        </div>
        @include('partials.back', ['fallback' => route('dashboard')])
      </div>

      <div class="card">
        <p style="margin:0" class="muted">
          Modul master data (Biro/Jabatan/Role) belum dibuat. Kalau kamu setuju, aku bisa langsung bikinin CRUD sederhana:
          <strong>add + view + edit + delete</strong> + validasi, dan pakai data yang sudah di-seed.
        </p>
      </div>
    </div>
  </body>
</html>
