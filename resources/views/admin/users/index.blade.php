<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data User</title>
    <style>
      :root{--bg:#f4f7fa;--card:#fff;--accent:#5966f7;--muted:#6b7280}
      *{box-sizing:border-box;font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial}
      body{margin:0;background:var(--bg)}
      .wrap{max-width:1100px;margin:32px auto;padding:0 16px}
      .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:12px;flex-wrap:wrap}
      .top h1{margin:0;font-size:22px}
      .btn{padding:10px 12px;border-radius:10px;border:1px solid #eef0f6;background:#fff;cursor:pointer;font-weight:600}
      .btn.primary{background:var(--accent);border-color:var(--accent);color:#fff;box-shadow:0 6px 18px rgba(89,102,247,0.18)}
      .card{background:var(--card);border-radius:10px;box-shadow:0 6px 20px rgba(20,20,60,0.06);padding:18px}
      .status{margin:0 0 12px;color:#065f46;background:#ecfdf5;border:1px solid #a7f3d0;padding:10px;border-radius:8px}
      .grid{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
      input{padding:10px 12px;border-radius:10px;border:1px solid #eef0f6;min-width:260px}
      .tableScroll{width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}
      .tableScroll table{min-width:900px}
      table{width:100%;border-collapse:collapse;margin-top:12px}
      th,td{padding:10px 8px;border-bottom:1px solid #eef0f6;text-align:left;font-size:14px;vertical-align:top}
      th{white-space:nowrap}
      .actions{display:flex;gap:8px;flex-wrap:wrap}
      .danger{border-color:#fecdd3;background:#fff1f2;color:#991b1b}
      a{color:inherit;text-decoration:none}
      .muted{color:var(--muted)}

      @media (max-width:640px){
        input{min-width:0;flex:1}
      }
    </style>
  </head>
  <body>
    <div class="wrap">
      <div class="top">
        <div>
          <h1>Data User</h1>
          <div class="muted" style="font-size:12px">View + search + edit + delete user.</div>
        </div>
        <div class="grid">
          @include('partials.back', ['fallback' => route('settings.user')])
          <a href="{{ route('users.create') }}"><button class="btn primary" type="button">Tambah User</button></a>
        </div>
      </div>

      <div class="card">
        @if(session('status'))
          <div class="status">{{ session('status') }}</div>
        @endif

        <form method="GET" action="{{ route('users.index') }}">
          <div class="grid">
            <input type="text" name="q" value="{{ $q }}" placeholder="Cari username / nama / nip">
            <button class="btn" type="submit">Cari</button>
            <a href="{{ route('users.index') }}"><button class="btn" type="button">Reset</button></a>
          </div>
        </form>

        <div class="tableScroll">
          <table>
            <thead>
              <tr>
                <th>No</th>
                <th>Username</th>
                <th>Nama</th>
                <th>NIP</th>
                <th>Role</th>
                <th>Biro</th>
                <th>Telp</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $u)
                <tr>
                  <td>{{ $users->firstItem() + $loop->index }}</td>
                  <td>{{ $u->username }}</td>
                  <td>{{ $u->nama }}</td>
                  <td>{{ $u->nip }}</td>
                  <td>{{ $u->role_name }}</td>
                  <td>{{ $u->biro_name }}</td>
                  <td>{{ $u->telp }}</td>
                  <td>
                    <div class="actions">
                      <a href="{{ route('users.edit', ['id' => $u->id]) }}"><button class="btn" type="button">Edit</button></a>
                      <form method="POST" action="{{ route('users.destroy', ['id' => $u->id]) }}" onsubmit="return confirm('Yakin hapus user ini?');" style="margin:0">
                        @csrf
                        @method('DELETE')
                        <button class="btn danger" type="submit">Hapus</button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="muted">Belum ada data.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div style="margin-top:12px">
          {{ $users->links() }}
        </div>
      </div>
    </div>
  </body>
</html>
