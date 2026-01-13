<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Data Biro</title>
  <style>
    :root {
      --bg: #f4f7fa;
      --card: #fff;
      --accent: #5966f7;
      --muted: #6b7280
    }

    * {
      box-sizing: border-box;
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial
    }

    body {
      margin: 0;
      background: var(--bg)
    }

    .wrap {
      max-width: 1100px;
      margin: 32px auto;
      padding: 0 16px
    }

    .top {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 16px;
      gap: 12px;
      flex-wrap: wrap
    }

    .top h1 {
      margin: 0;
      font-size: 22px
    }

    .btn {
      padding: 10px 12px;
      border-radius: 10px;
      border: 1px solid #eef0f6;
      background: #fff;
      cursor: pointer;
      font-weight: 600
    }

    .btn.primary {
      background: var(--accent);
      border-color: var(--accent);
      color: #fff;
      box-shadow: 0 6px 18px rgba(89, 102, 247, 0.18)
    }

    .card {
      background: var(--card);
      border-radius: 10px;
      box-shadow: 0 6px 20px rgba(20, 20, 60, 0.06);
      padding: 18px;
      min-width: 0;
      overflow: hidden
    }

    .status {
      margin: 0 0 12px;
      color: #065f46;
      background: #ecfdf5;
      border: 1px solid #a7f3d0;
      padding: 10px;
      border-radius: 8px
    }

    .tableScroll {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch
    }

    .tableScroll table {
      min-width: 900px
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 12px
    }

    th,
    td {
      padding: 10px 8px;
      border-bottom: 1px solid #eef0f6;
      text-align: left;
      font-size: 14px;
      vertical-align: top;
      white-space: nowrap
    }

    th {
      white-space: nowrap
    }

    .actions {
      display: flex;
      gap: 8px;
      flex-wrap: wrap
    }

    .danger {
      border-color: #fecdd3;
      background: #fff1f2;
      color: #991b1b
    }

    a {
      color: inherit;
      text-decoration: none
    }

    .muted {
      color: var(--muted)
    }
  </style>
</head>

<body>
  <div class="wrap">
    <div class="top">
      <div>
        <h1>Data Biro</h1>
        <div class="muted" style="font-size:12px">Tambah + edit + delete biro.</div>
      </div>
      <div style="display:flex;gap:10px;flex-wrap:wrap">
        @include('partials.back', ['fallback' => route('settings.biro')])
        <a href="{{ route('biro.create') }}"><button class="btn primary" type="button">Tambah Biro</button></a>
      </div>
    </div>

    <div class="card">
      @if(session('status'))
      <div class="status">{{ session('status') }}</div>
      @endif

      <div class="tableScroll">
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Biro</th>
              <th>Divisi</th>
              <th>Apakah sedang proyek?</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rows as $r)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $r->biro_name }}</td>
              <td>{{ $r->divisi }}</td>
              <td>{{ $r->is_proyek ? 'Ya' : 'Tidak' }}</td>
              <td>
                <div class="actions">
                  <a href="{{ route('biro.edit', ['id' => $r->id]) }}"><button class="btn" type="button">Edit</button></a>
                  <form method="POST" action="{{ route('biro.destroy', ['id' => $r->id]) }}" onsubmit="return confirm('Yakin hapus biro ini?');" style="margin:0">
                    @csrf
                    @method('DELETE')
                    <button class="btn danger" type="submit">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="muted">Belum ada data.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

</html>