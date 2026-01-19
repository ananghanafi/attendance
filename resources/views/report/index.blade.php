@extends('layouts.app')

@section('title', 'WG Absen — Laporan Absen')

@section('content')
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
        font-size: 24px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
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
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
        align-items: end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .filter-label {
        font-size: 14px;
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

    .table-wrapper {
        overflow-x: auto;
        max-height: calc(100vh - 400px);
        overflow-y: auto;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .report-table th {
        background: #f9fafb;
        padding: 12px 10px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
        border-bottom: 2px solid #e5e7eb;
    }

    .report-table td {
        padding: 10px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: top;
    }

    .report-table tr:hover td {
        background: #f9fafb;
    }

    .report-table .date-col {
        min-width: 180px;
        max-width: 250px;
    }

    .report-table .date-header {
        min-width: 180px;
        text-align: center;
    }

    .report-table .stat-col {
        text-align: center;
        min-width: 80px;
    }
        font-size: 12px;
        line-height: 1.5;
        padding: 6px 8px;
        border-radius: 6px;
        background: #f3f4f6;
    }

    .cell-content.success {
        background: #ecfdf5;
        color: #065f46;
    }

    .cell-content.warning {
        background: #fffbeb;
        color: #92400e;
    }

    .cell-content.danger {
        background: #fef2f2;
        color: #991b1b;
    }

    .cell-content.info {
        background: #eff6ff;
        color: #1e40af;
    }

    .stat-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .stat-badge.green {
        background: #ecfdf5;
        color: #065f46;
    }

    .stat-badge.red {
        background: #fef2f2;
        color: #991b1b;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-top: 1px solid #e5e7eb;
        flex-wrap: wrap;
        gap: 12px;
    }

    .pagination-info {
        font-size: 14px;
        color: #6b7280;
    }

    .pagination-buttons {
        display: flex;
        gap: 8px;
    }

    .pagination-btn {
        padding: 8px 14px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        background: #fff;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .pagination-btn:hover:not(:disabled) {
        background: #f9fafb;
        border-color: #d1d5db;
    }

    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .pagination-btn.active {
        background: #5966f7;
        border-color: #5966f7;
        color: #fff;
    }

    .loading-overlay {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        z-index: 20;
        align-items: center;
        justify-content: center;
    }

    .loading-overlay.show {
        display: flex;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #e5e7eb;
        border-top-color: #5966f7;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        margin-bottom: 16px;
        color: #d1d5db;
    }

    .empty-state h3 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #374151;
    }

    .wa-btn {
        padding: 6px 10px;
        border-radius: 6px;
        background: #25d366;
        color: #fff;
        border: none;
        cursor: pointer;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .wa-btn:hover {
        background: #1da851;
    }

    .wa-btn.teguran {
        background: #f97316;
    }

    .wa-btn.teguran:hover {
        background: #ea580c;
    }

    .wa-btn.peringatan {
        background: #ef4444;
    }

    .wa-btn.peringatan:hover {
        background: #dc2626;
    }

    .action-buttons {
        display: flex;
        gap: 4px;
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
        <h1 class="page-title">Report Absensi</h1>
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
        <form id="filterForm">
            @csrf
            <div class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label">Pegawai</label>
                    <select id="filterPegawai" class="filter-select">
                        <option value="all">-- Semua Pegawai --</option>
                        @foreach($pegawaiList as $pegawai)
                            <option value="{{ $pegawai->nip }}">{{ $pegawai->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Unit Kerja</label>
                    <select id="filterBiro" class="filter-select">
                        <option value="all">-- Semua Unit Kerja --</option>
                        @foreach($biroList as $biro)
                            <option value="{{ $biro->id }}">{{ $biro->biro_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Tanggal Dari</label>
                    <input type="date" id="filterDateFrom" class="filter-input">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Tanggal Sampai</label>
                    <input type="date" id="filterDateTo" class="filter-input">
                </div>
                <div style="display: flex; align-items: end;">
                    <button type="submit" class="btn primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Card -->
    <div class="data-card" style="position: relative;">
        <div class="loading-overlay" id="loadingOverlay">
            <div class="spinner"></div>
        </div>

        <div class="data-header">
            <span class="data-title">Hasil Report</span>
            <div class="search-box">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" id="tableSearch" placeholder="Cari di tabel...">
            </div>
        </div>

        <div class="table-wrapper" id="tableWrapper">
            <div class="empty-state" id="emptyState">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <line x1="10" y1="9" x2="8" y2="9"/>
                </svg>
                <h3>Belum Ada Data</h3>
                <p>Klik "Cari" untuk menampilkan data</p>
            </div>
            <table class="report-table" id="reportTable" style="display: none;">
                <thead id="tableHead"></thead>
                <tbody id="tableBody"></tbody>
            </table>
        </div>

        <div class="pagination-wrapper" id="paginationWrapper" style="display: none;">
            <span class="pagination-info" id="paginationInfo"></span>
            <div class="pagination-buttons" id="paginationButtons"></div>
        </div>
    </div>
</div>

<!-- Tom Select CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Tom Select for dropdowns
    const pegawaiSelect = new TomSelect('#filterPegawai', {
        create: false,
        sortField: [
            { field: '$order' }, // Keep original order (Semua Pegawai first)
            { field: 'text', direction: 'asc' }
        ],
        allowEmptyOption: true,
        placeholder: 'Pilih atau cari pegawai...'
    });

    const biroSelect = new TomSelect('#filterBiro', {
        create: false,
        sortField: [
            { field: '$order' }, // Keep original order (Semua Unit Kerja first)
            { field: 'text', direction: 'asc' }
        ],
        allowEmptyOption: true,
        placeholder: 'Pilih atau cari unit kerja...'
    });

    // State
    let currentPage = 1;
    let currentData = [];
    let currentDates = [];
    let searchQuery = '';

    // Form submit
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        fetchData();
    });

    // Table search
    let searchTimeout;
    document.getElementById('tableSearch').addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            searchQuery = e.target.value;
            currentPage = 1;
            fetchData();
        }, 300);
    });

    // Export Excel
    document.getElementById('btnExportExcel').addEventListener('click', function() {
        exportData('excel');
    });

    // Export PDF
    document.getElementById('btnExportPdf').addEventListener('click', function() {
        exportData('pdf');
    });

    function fetchData() {
        const nip = pegawaiSelect.getValue();
        const biroId = biroSelect.getValue();
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        showLoading(true);

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('nip', nip === 'all' ? '' : nip);
        formData.append('biro_id', biroId === 'all' ? '' : biroId);
        formData.append('fetch_all', (nip === 'all' && biroId === 'all') ? '1' : '0');
        formData.append('tanggal_from', dateFrom);
        formData.append('tanggal_to', dateTo);
        formData.append('search', searchQuery);
        formData.append('page', currentPage);

        fetch('{{ route("report.getData") }}', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            showLoading(false);

            if (data.message) {
                showEmptyState(data.message);
                return;
            }

            if (!data.data || data.data.length === 0) {
                showEmptyState('Tidak ada data yang ditemukan');
                return;
            }

            currentData = data.data;
            currentDates = data.dates;
            renderTable(data.data, data.dates);
            renderPagination(data.pagination);

            // Enable export buttons
            document.getElementById('btnExportExcel').disabled = false;
            document.getElementById('btnExportPdf').disabled = false;
        })
        .catch(err => {
            showLoading(false);
            console.error(err);
            showEmptyState('Terjadi kesalahan saat memuat data');
        });
    }

    function showLoading(show) {
        document.getElementById('loadingOverlay').classList.toggle('show', show);
    }

    function showEmptyState(message) {
        document.getElementById('emptyState').querySelector('p').textContent = message;
        document.getElementById('emptyState').style.display = 'block';
        document.getElementById('reportTable').style.display = 'none';
        document.getElementById('paginationWrapper').style.display = 'none';
        document.getElementById('btnExportExcel').disabled = true;
        document.getElementById('btnExportPdf').disabled = true;
    }

    function renderTable(data, dates) {
        document.getElementById('emptyState').style.display = 'none';
        document.getElementById('reportTable').style.display = 'table';

        // Build header
        let headerHtml = '<tr>';
        headerHtml += '<th>NIP</th>';
        headerHtml += '<th>Nama</th>';
        headerHtml += '<th>Atasan</th>';
        headerHtml += '<th>Unit Kerja</th>';

        dates.forEach(date => {
            const formattedDate = formatDate(date);
            headerHtml += `<th class="date-header">${formattedDate}</th>`;
        });

        headerHtml += '<th class="stat-col">ABSEN S.D 08:00</th>';
        headerHtml += '<th class="stat-col">ABSEN >08:00 / TIDAK ABSEN</th>';
        headerHtml += '<th class="stat-col">RATA² JAM KERJA</th>';
        headerHtml += '<th>Action</th>';
        headerHtml += '</tr>';

        document.getElementById('tableHead').innerHTML = headerHtml;

        // Build body
        let bodyHtml = '';
        data.forEach(row => {
            bodyHtml += '<tr>';
            bodyHtml += `<td>${row.nip}</td>`;
            bodyHtml += `<td>${row.nama}</td>`;
            bodyHtml += `<td>${row.nama_atasan}</td>`;
            bodyHtml += `<td>${row.biro_name}</td>`;

            dates.forEach(date => {
                const cellData = row.absen_data[date];
                const cellClass = getCellClass(cellData);
                bodyHtml += `<td class="date-col"><div class="cell-content ${cellClass}">${cellData.display}</div></td>`;
            });

            bodyHtml += `<td class="stat-col"><span class="stat-badge green">${row.absen_on_time}</span></td>`;
            bodyHtml += `<td class="stat-col"><span class="stat-badge red">${row.absen_late_or_absent}</span></td>`;
            bodyHtml += `<td class="stat-col">${row.avg_work_time}</td>`;
            
            // Action buttons based on violation count
            const violationCount = row.violation_count || 0;
            let actionHtml = '<td><div class="action-buttons">';
            
            if (violationCount >= 1) {
                // Show Teguran button (1+ violations)
                actionHtml += `<button class="wa-btn teguran" onclick="sendTeguran('${row.nip}')" title="Kirim Teguran">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </button>`;
            }
            
            if (violationCount >= 5) {
                // Show Peringatan button (5+ violations)
                actionHtml += `<button class="wa-btn peringatan" onclick="sendPeringatan('${row.nip}')" title="Kirim Peringatan">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </button>`;
            }
            
            if (violationCount === 0) {
                actionHtml += '<span class="text-muted" style="font-size: 11px; color: #9ca3af;">-</span>';
            }
            
            actionHtml += '</div></td>';
            bodyHtml += actionHtml;
            bodyHtml += '</tr>';
        });

        document.getElementById('tableBody').innerHTML = bodyHtml;
    }

    function getCellClass(cellData) {
        if (!cellData) return '';
        
        switch (cellData.status) {
            case 'hadir':
            case 'wfa':
            case 'dinas':
            case 'izin':
                return 'info';
            case 'rejected':
            case 'absent':
                return 'danger';
            default:
                return '';
        }
    }

    function formatDate(dateStr) {
        return dateStr; // Already in yyyy-mm-dd format
    }

    function renderPagination(pagination) {
        if (!pagination || pagination.last_page <= 1) {
            document.getElementById('paginationWrapper').style.display = 'none';
            return;
        }

        document.getElementById('paginationWrapper').style.display = 'flex';

        const from = ((pagination.current_page - 1) * pagination.per_page) + 1;
        const to = Math.min(pagination.current_page * pagination.per_page, pagination.total);
        document.getElementById('paginationInfo').textContent = 
            `Menampilkan ${from}-${to} dari ${pagination.total} data`;

        let buttonsHtml = '';
        
        // Previous button
        buttonsHtml += `<button class="pagination-btn" ${pagination.current_page === 1 ? 'disabled' : ''} onclick="goToPage(${pagination.current_page - 1})">← Prev</button>`;

        // Page numbers
        const maxPages = 5;
        let startPage = Math.max(1, pagination.current_page - Math.floor(maxPages / 2));
        let endPage = Math.min(pagination.last_page, startPage + maxPages - 1);

        if (endPage - startPage + 1 < maxPages) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            buttonsHtml += `<button class="pagination-btn ${i === pagination.current_page ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
        }

        // Next button
        buttonsHtml += `<button class="pagination-btn" ${pagination.current_page === pagination.last_page ? 'disabled' : ''} onclick="goToPage(${pagination.current_page + 1})">Next →</button>`;

        document.getElementById('paginationButtons').innerHTML = buttonsHtml;
    }

    window.goToPage = function(page) {
        currentPage = page;
        fetchData();
    };

    window.sendTeguran = function(nip) {
        if (!confirm('Apakah Anda yakin ingin mengirim Teguran ke pegawai ini?')) {
            return;
        }

        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        if (!dateFrom || !dateTo) {
            alert('Silakan pilih periode tanggal terlebih dahulu');
            return;
        }

        fetch('{{ route("report.sendTeguran") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                nip: nip,
                tanggal_from: dateFrom,
                tanggal_to: dateTo
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Teguran berhasil dikirim!');
            } else {
                alert('❌ Gagal: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Terjadi kesalahan saat mengirim teguran');
        });
    };

    window.sendPeringatan = function(nip) {
        if (!confirm('Apakah Anda yakin ingin mengirim Peringatan ke atasan pegawai ini?')) {
            return;
        }

        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        if (!dateFrom || !dateTo) {
            alert('Silakan pilih periode tanggal terlebih dahulu');
            return;
        }

        fetch('{{ route("report.sendPeringatan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                nip: nip,
                tanggal_from: dateFrom,
                tanggal_to: dateTo
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✅ Peringatan berhasil dikirim ke atasan!');
            } else {
                alert('❌ Gagal: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Terjadi kesalahan saat mengirim peringatan');
        });
    };

    function exportData(type) {
        const nip = pegawaiSelect.getValue();
        const biroId = biroSelect.getValue();
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = type === 'excel' ? '{{ route("report.exportExcel") }}' : '{{ route("report.exportPdf") }}';
        form.target = type === 'pdf' ? '_blank' : '_self';

        const fields = {
            '_token': '{{ csrf_token() }}',
            'nip': nip,
            'biro_id': biroId,
            'tanggal_from': dateFrom,
            'tanggal_to': dateTo
        };

        for (const key in fields) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = fields[key];
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
});
</script>
@endsection
