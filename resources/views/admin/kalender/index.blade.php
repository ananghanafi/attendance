<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kalender Kerja</title>
    <style>
      :root{--bg:#f4f7fa;--card:#fff;--accent:#5966f7;--muted:#6b7280}
      *{box-sizing:border-box;font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial}
      body{margin:0;background:var(--bg)}
      .wrap{max-width:1100px;margin:32px auto;padding:0 16px}
      .top{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
      .top h1{margin:0;font-size:22px}
      .logout button{padding:8px 12px;border-radius:6px;border:none;background:#e11d48;color:#fff;cursor:pointer}

  .layout{display:grid;grid-template-columns:240px 1fr;gap:16px;align-items:start}
  @media(max-width:900px){.layout{grid-template-columns:1fr}}

  .sidebar{background:var(--card);border-radius:10px;box-shadow:0 6px 20px rgba(20,20,60,0.06);padding:12px}
  .side-title{font-size:12px;color:var(--muted);margin:0 0 10px}
  .tabs{display:flex;flex-direction:column;gap:8px}
  .tab{width:100%;text-align:left;padding:10px 12px;border-radius:10px;border:1px solid #e9ecf5;background:#fff;color:#111;cursor:pointer}
  .tab.active{background:var(--accent);border-color:var(--accent);color:#fff}

      .panel{display:none}
      .panel.active{display:block}

      .card{background:var(--card);border-radius:10px;box-shadow:0 6px 20px rgba(20,20,60,0.06);padding:18px}
  .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
  @media(max-width:900px){.grid{grid-template-columns:1fr}}

      label{display:block;font-size:12px;color:var(--muted);margin-bottom:6px}
      select,input{width:100%;padding:11px 12px;border:1px solid #eef0f6;border-radius:8px;background:#fff;outline:none;font-size:14px}
      select:focus,input:focus{box-shadow:0 0 0 3px rgba(89,102,247,0.08);border-color:var(--accent)}

      .row2{display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-top:14px}
      @media(max-width:900px){.row2{grid-template-columns:1fr}}

      .calendar{margin-top:14px;display:grid;grid-template-columns:repeat(7,1fr);gap:8px}
      .cal-head{font-size:12px;color:var(--muted);text-align:center}
      .date{padding:10px 0;border:1px solid #eef0f6;border-radius:8px;background:#fff;text-align:center;cursor:pointer;user-select:none}
      .date.disabled{opacity:.35;cursor:not-allowed}
      .date.selected{background:rgba(89,102,247,0.12);border-color:rgba(89,102,247,0.5)}

      .actions{margin-top:16px;display:flex;gap:10px;justify-content:flex-end}
      .btn{padding:11px 14px;border-radius:10px;border:none;cursor:pointer;font-weight:600}
      .btn.primary{background:var(--accent);color:#fff;box-shadow:0 6px 18px rgba(89,102,247,0.18)}

      table{width:100%;border-collapse:collapse}
      th,td{padding:10px 8px;border-bottom:1px solid #eef0f6;text-align:left;font-size:14px}
      th{color:#111}
      .status{margin:10px 0 0;color:#065f46;background:#ecfdf5;border:1px solid #a7f3d0;padding:10px;border-radius:8px}
      .error{margin:10px 0 0;color:#991b1b;background:#fff1f2;border:1px solid #fecdd3;padding:10px;border-radius:8px}
      .hint{font-size:12px;color:var(--muted);margin-top:8px}
    </style>
  </head>
  <body>
    <div class="wrap">
      <div class="top">
        <h1>Dashboard Kalender Kerja</h1>
        <div class="logout">
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
          </form>
        </div>
      </div>

      <div class="layout">
        <aside class="sidebar">
          <div class="side-title">Menu</div>
          <div class="tabs" role="tablist">
            <button type="button" class="tab active" data-tab="form">Form Input Kalender Kerja</button>
            <button type="button" class="tab" data-tab="data">Data Kalender Kerja</button>
          </div>
        </aside>

        <main>
          <div id="panel-form" class="panel active">
            <div class="card">
          <h2 style="margin:0 0 12px">Input Kalender Kerja</h2>

          @if(session('status'))
            <div class="status">{{ session('status') }}</div>
          @endif
          @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
          @endif

          <form method="POST" action="{{ route('admin.kalender.store') }}" id="kalenderForm">
            @csrf
            <div class="grid">
              <div>
                <label for="minggu">Minggu ke-</label>
                <select id="minggu" name="minggu" required>
                  @for($i=1;$i<=6;$i++)
                    <option value="{{ $i }}">{{ $i }}</option>
                  @endfor
                </select>
              </div>
              <div>
                <label for="wfo_maks">WFO Maksimal (%)</label>
                <input type="number" id="wfo_maks" name="wfo_maks" min="0" max="100" step="0.01" required placeholder="Contoh: 50">
              </div>
              <div>
                <label for="bulan">Bulan</label>
                <select id="bulan" name="bulan" required></select>
              </div>
              <div>
                <label for="tahun">Tahun</label>
                <select id="tahun" name="tahun" required></select>
              </div>
            </div>

            <div class="row2">
              <div>
                <label>Tanggal Awal</label>
                <input type="text" id="tgl_awal_view" placeholder="Klik tanggal di kalender" readonly>
                <input type="hidden" id="tgl_awal" name="tgl_awal" required>
              </div>
              <div>
                <label>Tanggal Akhir</label>
                <input type="text" id="tgl_akhir_view" placeholder="Klik tanggal di kalender" readonly>
                <input type="hidden" id="tgl_akhir" name="tgl_akhir" required>
              </div>
            </div>

            <div class="hint">Pilih Bulan/Tahun terlebih dahulu. Lalu klik tanggal di kalender untuk mengisi Tanggal Awal dan Tanggal Akhir.</div>

            <div style="margin-top:14px">
              <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">
                <div>
                  <strong id="calTitle">Kalender</strong>
                  <div style="font-size:12px;color:var(--muted)" id="calSub"></div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                  <button type="button" class="btn" id="prevMonth" title="Bulan sebelumnya">‹ Bulan</button>
                  <button type="button" class="btn" id="nextMonth" title="Bulan berikutnya">Bulan ›</button>
                  <button type="button" class="btn" id="pickModeStart">Pilih Tanggal Awal</button>
                  <button type="button" class="btn" id="pickModeEnd">Pilih Tanggal Akhir</button>
                </div>
              </div>

              <div class="calendar" id="calendar">
                <!-- header days -->
              </div>
            </div>

            <div class="actions">
              <button type="submit" class="btn primary">Simpan</button>
            </div>
          </form>
            </div>
          </div>

          <div id="panel-data" class="panel">
            <div class="card">
              <h2 style="margin:0 0 12px">Data Kalender Kerja</h2>
              <table>
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Kalender</th>
                    <th>Tanggal Awal</th>
                    <th>Tanggal Akhir</th>
                    <th>WFO Maks (%)</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($rows as $r)
                    <tr>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $r->judul }}</td>
                      <td>{{ $r->tgl_awal ? $r->tgl_awal->format('Y-m-d') : '' }}</td>
                      <td>{{ $r->tgl_akhir ? $r->tgl_akhir->format('Y-m-d') : '' }}</td>
                      <td>{{ $r->persentase_decimal ?? $r->persentase }}</td>
                      <td>
                        <div style="display:flex;gap:8px;flex-wrap:wrap">
                          <a href="{{ route('kalender.edit', ['id' => $r->id]) }}" style="text-decoration:none">
                            <button type="button" class="btn" style="padding:8px 10px;border-radius:8px;border:1px solid #eef0f6;background:#fff">Edit</button>
                          </a>

                          <form method="POST" action="{{ route('kalender.destroy', ['id' => $r->id]) }}" onsubmit="return confirm('Yakin hapus data ini?');" style="margin:0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="padding:8px 10px;border-radius:8px;border:1px solid #fecdd3;background:#fff1f2;color:#991b1b">Hapus</button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" style="color:var(--muted)">Belum ada data.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </main>
      </div>
    </div>

    <script>
      // --- Tabs ---
      const tabs = document.querySelectorAll('.tab');
      const panelForm = document.getElementById('panel-form');
      const panelData = document.getElementById('panel-data');
      tabs.forEach(t => t.addEventListener('click', () => {
        tabs.forEach(x => x.classList.remove('active'));
        t.classList.add('active');
        const key = t.getAttribute('data-tab');
        panelForm.classList.toggle('active', key === 'form');
        panelData.classList.toggle('active', key === 'data');
      }));

      // --- Dropdown bulan & tahun ---
      const bulanEl = document.getElementById('bulan');
      const tahunEl = document.getElementById('tahun');
  const mingguEl = document.getElementById('minggu');

      const bulanNames = [
        'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
      ];

      function initBulan() {
        bulanEl.innerHTML = '';
        bulanNames.forEach((n, i) => {
          const opt = document.createElement('option');
          opt.value = String(i + 1);
          opt.textContent = n;
          bulanEl.appendChild(opt);
        });
      }

      function initTahun() {
        const now = new Date();
        const y = now.getFullYear();
        const years = [y - 1, y, y + 1, y + 2];
        tahunEl.innerHTML = '';
        years.forEach(yy => {
          const opt = document.createElement('option');
          opt.value = String(yy);
          opt.textContent = String(yy);
          tahunEl.appendChild(opt);
        });
        tahunEl.value = String(y);
      }

  // --- Kalender (grid bulanan) ---
      const cal = document.getElementById('calendar');
      const calTitle = document.getElementById('calTitle');
      const calSub = document.getElementById('calSub');
      const tglAwalHidden = document.getElementById('tgl_awal');
      const tglAkhirHidden = document.getElementById('tgl_akhir');
      const tglAwalView = document.getElementById('tgl_awal_view');
      const tglAkhirView = document.getElementById('tgl_akhir_view');
      const pickModeStart = document.getElementById('pickModeStart');
      const pickModeEnd = document.getElementById('pickModeEnd');
  const prevMonthBtn = document.getElementById('prevMonth');
  const nextMonthBtn = document.getElementById('nextMonth');

      let pickMode = 'start';
      function setPickMode(mode) {
        pickMode = mode;
        pickModeStart.classList.toggle('primary', mode === 'start');
        pickModeEnd.classList.toggle('primary', mode === 'end');
      }

      pickModeStart.addEventListener('click', () => setPickMode('start'));
      pickModeEnd.addEventListener('click', () => setPickMode('end'));
      setPickMode('start');

      function pad2(n){ return String(n).padStart(2, '0'); }
      function fmtISO(y,m,d){ return `${y}-${pad2(m)}-${pad2(d)}`; }

      // get hari dari sebuah data (lokal) 
      function ymd(date) {
        return `${date.getFullYear()}-${pad2(date.getMonth()+1)}-${pad2(date.getDate())}`;
      }

      let viewYear = null;
      let viewMonth = null;
  let suppressDropdownChange = false;

      function setViewMonth(year, month) {
        viewYear = year;
        viewMonth = month;
      }

      function syncDropdownToView() {
        suppressDropdownChange = true;
        bulanEl.value = String(viewMonth);
        tahunEl.value = String(viewYear);
        suppressDropdownChange = false;
      }

      function shiftViewMonth(delta) {
        const d = new Date(viewYear, viewMonth - 1, 1);
        d.setMonth(d.getMonth() + delta);
        setViewMonth(d.getFullYear(), d.getMonth() + 1);

        syncDropdownToView();

        buildCalendar();
      }

      prevMonthBtn.addEventListener('click', () => shiftViewMonth(-1));
      nextMonthBtn.addEventListener('click', () => shiftViewMonth(1));

      function buildCalendar() {
        const year = parseInt(tahunEl.value, 10);
        const month = parseInt(bulanEl.value, 10);

        if (viewYear === null || viewMonth === null) {
          setViewMonth(year, month);
        }

        calTitle.textContent = `Kalender ${bulanNames[viewMonth-1]} ${viewYear}`;
        calSub.textContent = `Klik tanggal untuk mengisi Tanggal Awal / Tanggal Akhir`;

        const heads = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
        cal.innerHTML = '';
        heads.forEach(h => {
          const el = document.createElement('div');
          el.className = 'cal-head';
          el.textContent = h;
          cal.appendChild(el);
        });

        const firstOfView = new Date(viewYear, viewMonth - 1, 1);
        const daysInView = new Date(viewYear, viewMonth, 0).getDate();
  const firstIdx = firstOfView.getDay();
  const weeksInView = Math.ceil((firstIdx + daysInView) / 7);

  const gridStart = new Date(viewYear, viewMonth - 1, 1 - firstIdx);
        const totalCells = weeksInView * 7;

        for (let i = 0; i < totalCells; i++) {
          const date = new Date(gridStart);
          date.setDate(gridStart.getDate() + i);

          const el = document.createElement('div');
          el.className = 'date';
          el.textContent = String(date.getDate());

          if (date.getMonth() + 1 !== viewMonth || date.getFullYear() !== viewYear) {
            el.style.opacity = '0.55';
          }

          const allowed = true;

          const iso = ymd(date);
          if (tglAwalHidden.value === iso || tglAkhirHidden.value === iso) {
            el.classList.add('selected');
          }

          el.addEventListener('click', () => {
            if ((date.getMonth() + 1) !== viewMonth || date.getFullYear() !== viewYear) {
              setViewMonth(date.getFullYear(), date.getMonth() + 1);
              syncDropdownToView();
            }

            if (pickMode === 'start') {
              tglAwalHidden.value = iso;
              tglAwalView.value = iso;
              if (tglAkhirHidden.value && tglAkhirHidden.value < tglAwalHidden.value) {
                tglAkhirHidden.value = '';
                tglAkhirView.value = '';
              }
            } else {
              if (tglAwalHidden.value && iso < tglAwalHidden.value) {
                alert('Tanggal akhir harus lebih besar atau sama dengan tanggal awal.');
                return;
              }
              tglAkhirHidden.value = iso;
              tglAkhirView.value = iso;
            }
            buildCalendar();
          });

          cal.appendChild(el);
        }
      }

      function resetDates() {
        tglAwalHidden.value = '';
        tglAkhirHidden.value = '';
        tglAwalView.value = '';
        tglAkhirView.value = '';
      }

      initBulan();
      initTahun();


  const now = new Date();
  bulanEl.value = String(now.getMonth() + 1);
  setViewMonth(parseInt(tahunEl.value, 10), parseInt(bulanEl.value, 10));

      [bulanEl, tahunEl, mingguEl].forEach(el => el.addEventListener('change', () => {
        if (suppressDropdownChange) return;
        resetDates();
        setViewMonth(parseInt(tahunEl.value, 10), parseInt(bulanEl.value, 10));
        buildCalendar();
      }));

      buildCalendar();
    </script>
  </body>
</html>
