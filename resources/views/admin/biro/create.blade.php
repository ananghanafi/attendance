<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Biro</title>
    <style>
      :root{--bg:#f4f7fa;--card:#fff;--accent:#5966f7;--muted:#6b7280}
      *{box-sizing:border-box;font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial}
      body{margin:0;background:var(--bg)}
      .wrap{max-width:900px;margin:32px auto;padding:0 16px}
      .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:12px;flex-wrap:wrap}
      .top h1{margin:0;font-size:22px}
      .btn{padding:10px 12px;border-radius:10px;border:1px solid #eef0f6;background:#fff;cursor:pointer;font-weight:600}
      .btn.primary{background:var(--accent);border-color:var(--accent);color:#fff;box-shadow:0 6px 18px rgba(89,102,247,0.18)}
      .card{background:var(--card);border-radius:10px;box-shadow:0 6px 20px rgba(20,20,60,0.06);padding:18px}
      label{display:block;font-size:12px;color:var(--muted);margin-bottom:6px}
      input,select{width:100%;padding:10px 12px;border-radius:10px;border:1px solid #eef0f6}
      .grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
      @media(max-width:700px){.grid{grid-template-columns:1fr}}
      .error{margin:0 0 12px;color:#991b1b;background:#fff1f2;border:1px solid #fecdd3;padding:10px;border-radius:8px}
      a{color:inherit;text-decoration:none}
    </style>
  </head>
  <body>
    <div class="wrap">
      <div class="top">
        <div>
          <h1>Tambah Biro</h1>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap">
          @include('partials.back', ['fallback' => route('settings.biro')])
        </div>
      </div>

      <div class="card">
        @if($errors->any())
          <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('biro.store') }}">
          @csrf

          <div style="margin-bottom:12px">
            <label>Nama Biro</label>
            <input type="text" name="biro_name" value="{{ old('biro_name') }}" required>
          </div>

          <div class="grid">
            <div>
              <label>Divisi</label>
              <input type="text" name="divisi" value="{{ old('divisi') }}" required>
            </div>
            <div>
              <label>Apakah Proyek?</label>
              <select name="is_proyek" required>
                <option value="1" {{ old('is_proyek','1')==='1' ? 'selected' : '' }}>Ya</option>
                <option value="0" {{ old('is_proyek')==='0' ? 'selected' : '' }}>Tidak</option>
              </select>
            </div>
          </div>

          <div style="display:flex;justify-content:flex-end;margin-top:14px">
            <button class="btn primary" type="submit">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
