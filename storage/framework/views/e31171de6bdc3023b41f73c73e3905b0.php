

<?php $__env->startSection('title', 'WG Absen — Laporan Makan'); ?>

<?php $__env->startSection('content'); ?>
<style>
    .report-container {
        max-width: 100%;
        padding: 24px;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }

    .page-title {
        font-size: 20px;
        font-weight: 700;
        color: #000000;
        margin: 0;
        line-height: 1.4;
    }

    .export-buttons {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 10px 18px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: #fff;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn:hover {
        background: #f9fafb;
    }

    .btn.primary {
        background: #5966f7;
        border-color: #5966f7;
        color: #fff;
    }

    .btn.primary:hover {
        background: #4854d8;
    }

    .btn.success {
        background: #10b981;
        border-color: #10b981;
        color: #fff;
    }

    .btn.success:hover {
        background: #059669;
    }

    .btn.danger {
        background: #ef4444;
        border-color: #ef4444;
        color: #fff;
    }

    .btn.danger:hover {
        background: #dc2626;
    }

    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .filter-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        padding: 20px;
        margin-bottom: 24px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .filter-input,
    .filter-select {
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        font-size: 14px;
        width: 100%;
        transition: border-color 0.2s ease;
    }

    .filter-input:focus,
    .filter-select:focus {
        outline: none;
        border-color: #5966f7;
        box-shadow: 0 0 0 3px rgba(89, 102, 247, 0.1);
    }

    /* Tom Select Styling */
    .ts-wrapper {
        width: 100%;
    }

    .ts-control {
        padding: 8px 12px !important;
        border-radius: 8px !important;
        border: 1px solid #e5e7eb !important;
        font-size: 14px !important;
        min-height: 42px !important;
    }

    .ts-control:focus,
    .ts-wrapper.focus .ts-control {
        border-color: #5966f7 !important;
        box-shadow: 0 0 0 3px rgba(89, 102, 247, 0.1) !important;
    }

    .ts-dropdown {
        border-radius: 8px !important;
        border: 1px solid #e5e7eb !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
    }

    .ts-dropdown .option {
        padding: 10px 14px !important;
        color: #374151 !important;
    }

    .ts-dropdown .option.active,
    .ts-dropdown .option:hover {
        background: #5966f7 !important;
        color: #fff !important;
    }

    .data-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .data-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
        flex-wrap: wrap;
        gap: 12px;
    }

    .data-title {
        font-size: 16px;
        font-weight: 600;
        color: #374151;
    }

    .search-box {
        position: relative;
    }

    .search-box input {
        padding: 8px 14px 8px 36px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        font-size: 14px;
        width: 250px;
    }

    .search-box svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: #9ca3af;
    }

    .table-container {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .data-table th {
        background: #5ba6e7;
        color: #0f172a;
        font-weight: 600;
        padding: 12px 10px;
        text-align: center;
        border: 1px solid #a7f3d0;
        white-space: nowrap;
    }

    .data-table td {
        padding: 10px;
        border: 1px solid #e5e7eb;
        text-align: center;
        vertical-align: middle;
    }

    .data-table tbody tr:hover {
        background: #f9fafb;
    }

    .data-table tbody tr.total-row:hover {
        background: #FFF1DE;
    }

    .data-table .total-row {
        background: #FFF1DE;
        color: #1f2937;
        font-weight: 700;
    }

    .data-table .total-row td {
        padding: 12px 10px;
        color: #1f2937;
        text-align: left;
    }

    .data-table .total-row .total-value {
        color: #1f2937;
        text-align: right;
    }

    .clickable-count {
        color: #5966f7;
        cursor: pointer;
        text-decoration: underline;
        font-weight: 600;
    }

    .clickable-count:hover {
        color: #4854d8;
    }

    .total-value {
        font-weight: 700;
        color: #0f172a;
    }

    .loading {
        text-align: center;
        padding: 40px;
        color: #6b7280;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    /* Modal Styles */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: #fff;
        border-radius: 12px;
        max-width: 900px;
        width: 90%;
        max-height: 85vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-title {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #6b7280;
        padding: 0;
        line-height: 1;
    }

    .modal-close:hover {
        color: #1f2937;
    }

    .modal-body {
        padding: 20px;
        overflow-y: auto;
        flex: 1;
    }

    .modal-table-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .entries-select {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .entries-select select {
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
    }

    .modal-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .modal-table th {
        background: #f3f4f6;
        color: #374151;
        font-weight: 600;
        padding: 10px 12px;
        text-align: left;
        border: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .modal-table td {
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
    }

    .modal-table tbody tr:hover {
        background: #f9fafb;
    }

    .text-red {
        color: #ef4444 !important;
    }

    .text-green {
        color: #10b981;
    }

    .text-blue {
        color: #3b82f6;
    }

    .modal-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-top: 1px solid #e5e7eb;
    }

    .pagination-info {
        font-size: 14px;
        color: #6b7280;
    }

    .pagination-buttons {
        display: flex;
        gap: 8px;
    }

    .page-btn {
        padding: 6px 12px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        background: #fff;
        cursor: pointer;
        font-size: 14px;
    }

    .page-btn:hover {
        background: #f3f4f6;
    }

    .page-btn.active {
        background: #3b82f6;
        color: #fff;
        border-color: #3b82f6;
    }

    @media (max-width: 768px) {
        .report-container {
            padding: 16px;
        }

        .filter-grid {
            grid-template-columns: 1fr;
        }

        .search-box input {
            width: 100%;
        }

        .data-header {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>

<div class="report-container">
    <div class="page-header">
        <h1 class="page-title" id="periodTitle">LAPORAN KEBUTUHAN UANG MAKAN PER PEGAWAI</h1>
        <div class="export-buttons">
            <button type="button" class="btn success" id="btnExportExcel" disabled>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Export Excel
            </button>
            <button type="button" class="btn danger" id="btnExportPdf" disabled>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
                Export PDF
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <div class="filter-grid">
            <div class="filter-group">
                <label class="filter-label">Pilih Pegawai</label>
                <select id="filterPegawai" class="filter-select" placeholder="-- Please select --">
                    <option value="">-- Please select --</option>
                    <?php $__currentLoopData = $pegawaiList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($p->nip); ?>"><?php echo e($p->nama); ?> (<?php echo e($p->nip); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Pilih Biro</label>
                <select id="filterBiro" class="filter-select" placeholder="-- Please select --">
                    <option value="">-- Please select --</option>
                    <?php $__currentLoopData = $biroList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($b->id); ?>"><?php echo e($b->biro_name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Mulai dari</label>
                <input type="date" id="filterFrom" class="filter-input">
            </div>

            <div class="filter-group">
                <label class="filter-label">Sampai tanggal</label>
                <input type="date" id="filterTo" class="filter-input">
            </div>

            <div style="display: flex; align-items: end;">
                <button class="btn primary" id="btnProses">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    Cari
                </button>
            </div>
        </div>
    </div>

    <!-- Data Card -->
    <div class="data-card">
        <div class="data-header">
            <span class="data-title">Hasil Laporan</span>
            <div class="search-box">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" id="tableSearch" placeholder="Cari di tabel...">
            </div>
        </div>

        <div class="table-container">
            <table class="data-table" id="dataTable">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>NIP</th>
                        <th>NAMA<br>PEGAWAI</th>
                        <th>NAMA<br>UNIT KERJA</th>
                        <th>JUMLAH<br>HARI KERJA</th>
                        <th>JUMLAH<br>HARI WFO</th>
                        <th>JUMLAH<br>HARI WFA</th>
                        <th>JUMLAH HARI<br>ABSEN WFO</th>
                        <th>JUMLAH HARI<br>ABSEN WFA</th>
                        <th>JUMLAH<br>ABSEN</th>
                        <th>% HARI<br>ABSEN</th>
                        <th>NILAI UANG<br>MAKAN SIANG<br>PER HARI</th>
                        <th>TOTAL NILAI<br>UANG MAKAN<br>SIANG BULAN INI</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                </tbody>
            </table>
        </div>

        <div id="loadingState" class="loading" style="display: none;">
            <div style="text-align: center; padding: 40px;">
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #e5e7eb; border-top-color: #3ec9a7; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p style="margin-top: 16px; color: #6b7280;">Memuat data...</p>
            </div>
        </div>
        <style>
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
        </style>

        <div id="emptyState" class="empty-state">
            Pilih filter dan klik "Cari" untuk menampilkan data
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal-overlay" id="detailModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">List Absen</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="modal-table-controls">
                <div class="entries-select">
                    Show <select id="modalEntries" onchange="updateModalTable()">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select> entries
                </div>
                <div class="search-box">
                    <input type="text" id="modalSearch" placeholder="Search:" onkeyup="updateModalTable()">
                </div>
            </div>
            <table class="modal-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Masuk</th>
                        <th>Pulang</th>
                        <th>Status</th>
                        <th>Lokasi WFA</th>
                        <th>Alasan Lokasi WFA</th>
                    </tr>
                </thead>
                <tbody id="modalTableBody">
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <div class="pagination-info" id="modalPaginationInfo">Showing 1 to 10 of 0 entries</div>
            <div class="pagination-buttons" id="modalPagination">
            </div>
            <button class="btn" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    let tableData = [];
    let modalData = [];
    let modalCurrentPage = 1;
    let currentFilters = {};

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Tom Select for dropdowns
        new TomSelect('#filterPegawai', {
            allowEmptyOption: true,
            placeholder: '-- Please select --'
        });
        new TomSelect('#filterBiro', {
            allowEmptyOption: true,
            placeholder: '-- Please select --'
        });

        // Tanggal TIDAK di-set otomatis - user harus pilih manual
        // Tampilkan empty state awal, sembunyikan loading & tabel
        document.getElementById('loadingState').style.display = 'none';
        document.getElementById('emptyState').style.display = 'block';
        document.getElementById('dataTable').style.display = 'none';

        // Event listeners
        document.getElementById('btnProses').addEventListener('click', fetchData);
        document.getElementById('tableSearch').addEventListener('keyup', filterTable);
        document.getElementById('btnExportExcel').addEventListener('click', exportExcel);
        document.getElementById('btnExportPdf').addEventListener('click', exportPDF);
    });

    function toggleExportButtons(enabled) {
        document.getElementById('btnExportExcel').disabled = !enabled;
        document.getElementById('btnExportPdf').disabled = !enabled;
    }

    function fetchData() {
        const nip = document.getElementById('filterPegawai').value;
        const biroId = document.getElementById('filterBiro').value;
        const tanggalFrom = document.getElementById('filterFrom').value;
        const tanggalTo = document.getElementById('filterTo').value;

        if (!tanggalFrom || !tanggalTo) {
            alert('Tanggal harus diisi');
            return;
        }

        currentFilters = { nip, biroId, tanggalFrom, tanggalTo };

        document.getElementById('loadingState').style.display = 'block';
        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('dataTable').style.display = 'none';
        document.getElementById('tableBody').innerHTML = '';
        toggleExportButtons(false);

        fetch('<?php echo e(route("makan.getData")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                nip: nip,
                biro_id: biroId,
                tanggal_from: tanggalFrom,
                tanggal_to: tanggalTo
            })
        })
        .then(response => response.json())
        .then(result => {
            document.getElementById('loadingState').style.display = 'none';

            if (result.success) {
                tableData = result.data;
                document.getElementById('periodTitle').innerHTML = result.period_title;
                document.getElementById('dataTable').style.display = 'table';
                renderTable(tableData, result.grand_total);
                toggleExportButtons(tableData.length > 0);
            } else {
                alert(result.message || 'Gagal memuat data');
                document.getElementById('dataTable').style.display = 'none';
                document.getElementById('emptyState').style.display = 'block';
                toggleExportButtons(false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loadingState').style.display = 'none';
            document.getElementById('dataTable').style.display = 'none';
            document.getElementById('emptyState').style.display = 'block';
            toggleExportButtons(false);
            alert('Terjadi kesalahan saat memuat data');
        });
    }

    function renderTable(data, grandTotal) {
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';

        if (data.length === 0) {
            document.getElementById('dataTable').style.display = 'none';
            document.getElementById('emptyState').style.display = 'block';
            return;
        }

        document.getElementById('dataTable').style.display = 'table';
        document.getElementById('emptyState').style.display = 'none';

        // Total row first
        const totalRow = document.createElement('tr');
        totalRow.className = 'total-row';
        totalRow.innerHTML = `
            <td colspan="12"><strong>TOTAL</strong></td>
            <td class="total-value" style="text-align:right">${formatCurrency(grandTotal)}</td>
        `;
        tbody.appendChild(totalRow);

        // Data rows
        data.forEach((row, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${index + 1}</td>
                <td>${row.nip}</td>
                <td>${row.nama}</td>
                <td>${row.biro_name}</td>
                <td>${row.jumlah_hari_kerja}</td>
                <td>${row.jumlah_hari_wfo}</td>
                <td>${row.jumlah_hari_wfa}</td>
                <td>${row.jumlah_absen_wfo}</td>
                <td>${row.jumlah_absen_wfa}</td>
                <td><span class="clickable-count" onclick="showDetail('${row.nip}', '${row.nama}')">${row.jumlah_absen}</span></td>
                <td>${row.persen_absen.toFixed(2)} %</td>
                <td>${formatCurrency(row.uang_makan_per_hari)}</td>
                <td>${formatCurrency(row.total_uang_makan)}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    function filterTable() {
        const searchText = document.getElementById('tableSearch').value.toLowerCase();
        const filtered = tableData.filter(row => 
            row.nama.toLowerCase().includes(searchText) ||
            row.nip.toLowerCase().includes(searchText) ||
            row.biro_name.toLowerCase().includes(searchText)
        );

        const grandTotal = filtered.reduce((sum, row) => sum + row.total_uang_makan, 0);
        renderTable(filtered, grandTotal);
    }

    function showDetail(nip, nama) {
        document.getElementById('modalTitle').textContent = `List Absen ${nama} - ${nip}`;
        document.getElementById('detailModal').classList.add('active');
        document.getElementById('modalTableBody').innerHTML = '<tr><td colspan="6">Memuat...</td></tr>';

        fetch('<?php echo e(route("makan.getDetail")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                nip: nip,
                tanggal_from: currentFilters.tanggalFrom,
                tanggal_to: currentFilters.tanggalTo
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                modalData = result.details;
                modalCurrentPage = 1;
                updateModalTable();
            } else {
                document.getElementById('modalTableBody').innerHTML = '<tr><td colspan="6">Gagal memuat data</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('modalTableBody').innerHTML = '<tr><td colspan="6">Terjadi kesalahan</td></tr>';
        });
    }

    function updateModalTable() {
        const searchText = document.getElementById('modalSearch').value.toLowerCase();
        const perPage = parseInt(document.getElementById('modalEntries').value);

        let filtered = modalData.filter(row =>
            row.tanggal.includes(searchText) ||
            row.status.toLowerCase().includes(searchText) ||
            (row.lokasi_wfa && row.lokasi_wfa.toLowerCase().includes(searchText))
        );

        const totalPages = Math.ceil(filtered.length / perPage);
        if (modalCurrentPage > totalPages) modalCurrentPage = 1;

        const start = (modalCurrentPage - 1) * perPage;
        const pageData = filtered.slice(start, start + perPage);

        const tbody = document.getElementById('modalTableBody');
        tbody.innerHTML = '';

        pageData.forEach(row => {
            const isWfa = row.status === 'wfa';
            const statusDisplay = row.status.toUpperCase(); // Tampilkan uppercase
            const redStyle = isWfa ? 'color: #ef4444;' : ''; // Inline style merah kalau WFA
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td style="${redStyle}">${row.tanggal}</td>
                <td style="${redStyle}">${row.scan_masuk}</td>
                <td style="${redStyle}">${row.scan_pulang}</td>
                <td style="${redStyle}">${statusDisplay}</td>
                <td style="${redStyle}">${row.lokasi_wfa}</td>
                <td style="${redStyle}">${row.alasan_lokasi}</td>
            `;
            tbody.appendChild(tr);
        });

        // Update pagination info
        const endIdx = Math.min(start + perPage, filtered.length);
        document.getElementById('modalPaginationInfo').textContent = 
            `Showing ${start + 1} to ${endIdx} of ${filtered.length} entries`;

        // Update pagination buttons
        const pagination = document.getElementById('modalPagination');
        pagination.innerHTML = '';

        const prevBtn = document.createElement('button');
        prevBtn.className = 'page-btn';
        prevBtn.textContent = 'Previous';
        prevBtn.onclick = () => { if (modalCurrentPage > 1) { modalCurrentPage--; updateModalTable(); } };
        pagination.appendChild(prevBtn);

        for (let i = 1; i <= Math.min(totalPages, 5); i++) {
            const btn = document.createElement('button');
            btn.className = 'page-btn' + (i === modalCurrentPage ? ' active' : '');
            btn.textContent = i;
            btn.onclick = () => { modalCurrentPage = i; updateModalTable(); };
            pagination.appendChild(btn);
        }

        const nextBtn = document.createElement('button');
        nextBtn.className = 'page-btn';
        nextBtn.textContent = 'Next';
        nextBtn.onclick = () => { if (modalCurrentPage < totalPages) { modalCurrentPage++; updateModalTable(); } };
        pagination.appendChild(nextBtn);
    }

    function closeModal() {
        document.getElementById('detailModal').classList.remove('active');
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(value);
    }

    function exportExcel() {
        if (tableData.length === 0) {
            alert('Tidak ada data untuk di-export');
            return;
        }

        // Simple CSV export
        let csv = [];
        const headers = ['NO', 'NIP', 'NAMA PEGAWAI', 'NAMA UNIT KERJA', 'JUMLAH HARI KERJA', 'JUMLAH HARI WFO', 'JUMLAH HARI WFA', 'JUMLAH HARI ABSEN WFO', 'JUMLAH HARI ABSEN WFA', 'JUMLAH ABSEN', '% HARI ABSEN', 'NILAI UANG MAKAN PER HARI', 'TOTAL UANG MAKAN'];
        csv.push(headers.join(','));

        tableData.forEach((row, index) => {
            csv.push([
                index + 1,
                row.nip,
                `"${row.nama}"`,
                `"${row.biro_name}"`,
                row.jumlah_hari_kerja,
                row.jumlah_hari_wfo,
                row.jumlah_hari_wfa,
                row.jumlah_absen_wfo,
                row.jumlah_absen_wfa,
                row.jumlah_absen,
                row.persen_absen.toFixed(2),
                row.uang_makan_per_hari,
                row.total_uang_makan
            ].join(','));
        });

        // Add total row
        const grandTotal = tableData.reduce((sum, row) => sum + row.total_uang_makan, 0);
        csv.push(['', '', '', '', '', '', '', '', '', '', '', 'TOTAL', grandTotal].join(','));

        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'laporan_uang_makan.csv';
        link.click();
    }

    function exportPDF() {
        if (tableData.length === 0) {
            alert('Tidak ada data untuk di-export');
            return;
        }
        alert('Export PDF - fitur dalam pengembangan');
    }

    // Close modal when clicking outside
    document.getElementById('detailModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kevannn\Documents\FILE MAGANG\AbsensiWika\resources\views/laporan-makan/index.blade.php ENDPATH**/ ?>