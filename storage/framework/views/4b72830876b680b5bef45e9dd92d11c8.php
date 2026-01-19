

<?php $__env->startSection('content'); ?>
<style>
  .alert {
    padding: 1rem 1.25rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.875rem;
  }

  .alert-success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #86efac;
  }

  .alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
  }

  .alert-info {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #93c5fd;
  }

  .header-section {
    margin-bottom: 2rem;
  }

  .header-section h1 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 1.5rem;
  }

  .back-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: var(--primary);
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    margin-bottom: 1.5rem;
    transition: all 0.2s;
  }

  .back-button:hover {
    background: #4f46e5;
    transform: translateY(-1px);
  }

  .info-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e7eaf3;
    box-shadow: 0 4px 20px rgba(35, 45, 120, .08);
    padding: 24px;
    margin-bottom: 24px;
  }

  .info-grid {
    display: grid;
    gap: 1rem;
  }

  .info-item {
    background: #f1f5f9;
    padding: 1rem;
    border-radius: 8px;
  }

  .info-label {
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 0.25rem;
  }

  .info-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text);
  }

  /* Disabled radio button styling */
  input[type="radio"]:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  input[type="radio"]:disabled+label,
  .radio-cell input[type="radio"]:disabled {
    opacity: 0.6;
  }

  /* Holiday styling */
  .libur-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #fef2f2;
    color: #991b1b;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
    border: 1px solid #fecaca;
    margin-top: 2px;
  }

  .holiday-col {
    background: #fee2e2 !important;
    position: relative;
  }

  .holiday-header {
    background: #fecaca !important;
  }

  .table-section {
    background: white;
    border-radius: 16px;
    border: 1px solid #e7eaf3;
    box-shadow: 0 4px 20px rgba(35, 45, 120, .08);
    padding: 24px;
  }

  .table-section h2 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--text);
  }

  .tableScroll {
    overflow-x: auto;
    margin-bottom: 1rem;
  }

  table {
    width: auto;
    min-width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
    table-layout: fixed;
  }

  /* Column widths via classes */
  .col-nip {
    width: 110px !important;
    min-width: 110px !important;
    max-width: 110px !important;
  }

  .col-nama {
    width: 150px !important;
    min-width: 150px !important;
    max-width: 150px !important;
    white-space: normal;
    word-wrap: break-word;
    line-height: 1.3;
  }

  .col-day {
    width: 70px !important;
    min-width: 70px !important;
    max-width: 70px !important;
  }

  .col-day-header {
    width: 140px !important;
    min-width: 140px !important;
    max-width: 140px !important;
  }

  thead {
    background: #f8fafc;
  }

  th {
    padding: 0.85rem 0.6rem;
    text-align: center;
    font-weight: 600;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-size: 0.875rem;
  }

  th.text-left {
    text-align: left;
  }

  /* Color coding for day headers */
  .senin-col {
    background: #c6f6d5 !important;
  }

  .selasa-col {
    background: #bee3f8 !important;
  }

  .rabu-col {
    background: #fef08a !important;
  }

  .kamis-col {
    background: #fbcfe8 !important;
  }

  .jumat-col {
    background: #bfdbfe !important;
  }

  td {
    padding: 0.85rem 0.6rem;
    border: 1px solid #e2e8f0;
    text-align: center;
    font-size: 0.875rem;
  }

  td.text-left {
    text-align: left;
  }

  td.radio-cell {
    text-align: center;
    padding: 0.85rem 0.6rem;
    /* Consistent padding */
  }

  /* Apply day colors to data cells */
  tbody tr:not(.summary-row) td.senin-col {
    background: #dcfce7;
  }

  tbody tr:not(.summary-row) td.selasa-col {
    background: #dbeafe;
  }

  tbody tr:not(.summary-row) td.rabu-col {
    background: #fef9c3;
  }

  tbody tr:not(.summary-row) td.kamis-col {
    background: #fce7f3;
  }

  tbody tr:not(.summary-row) td.jumat-col {
    background: #e0f2fe;
  }

  tbody tr:hover td {
    opacity: 0.9;
  }

  .summary-row {
    font-weight: 600;
    background: #f8fafc !important;
  }

  .summary-row td {
    background: #f8fafc !important;
    font-weight: 600;
  }

  .summary-row.totals td {
    color: #1e293b;
  }

  .percentage-cell {
    font-weight: 600;
  }

  .percentage-cell.wfo {
    color: #16a34a;
  }

  .percentage-cell.wfa {
    color: #16a34a;
  }

  .percentage-cell.over-target {
    color: #dc2626 !important;
  }

  input[type="radio"] {
    cursor: pointer;
    width: 16px;
    height: 16px;
  }

  .save-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 1rem;
  }

  .save-button:hover {
    background: #4f46e5;
    transform: translateY(-1px);
  }

  .save-button:disabled {
    background: #94a3b8;
    cursor: not-allowed;
    transform: none;
  }
</style>

<?php if(session('success')): ?>
<div class="alert alert-success">
  ✅ <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<?php if(session('error')): ?>
<div class="alert alert-error">
  ❌ <?php echo e(session('error')); ?>

</div>
<?php endif; ?>

<?php if(session('info')): ?>
<div class="alert alert-info">
  ℹ️ <?php echo e(session('info')); ?>

</div>
<?php endif; ?>

<?php if(session('status')): ?>
<div class="alert alert-success">
  ✅ <?php echo e(session('status')); ?>

</div>
<?php endif; ?>

<div class="header-section">
  <a href="<?php echo e(route('pengajuan.index')); ?>" class="back-button">
    ← Kembali ke List Pengajuan WFO
  </a>

  <h1>VIEW PENGAJUAN PEGAWAI WAO MINGGU <?php echo e($pengajuan->minggu ?? '-'); ?> BULAN <?php echo e($pengajuan->bulan ?? '-'); ?> TAHUN <?php echo e($pengajuan->tahun ?? '-'); ?></h1>

  <div class="info-card">
    <div class="info-grid">
      <div class="info-item">
        <div class="info-label">Unit Kerja</div>
        <div class="info-value"><?php echo e($pengajuan->biro_name); ?></div>
      </div>

      <div class="info-item">
        <div class="info-label">Tahun</div>
        <div class="info-value"><?php echo e($pengajuan->tahun ?? '-'); ?></div>
      </div>

      <div class="info-item">
        <div class="info-label">Bulan</div>
        <div class="info-value"><?php echo e($pengajuan->bulan ?? '-'); ?></div>
      </div>

      <div class="info-item">
        <div class="info-label">Minggu</div>
        <div class="info-value"><?php echo e($pengajuan->minggu ?? '-'); ?></div>
      </div>

      <div class="info-item">
        <div class="info-label">Periode</div>
        <div class="info-value">
          <?php if($pengajuan->tgl_awal && $pengajuan->tgl_akhir): ?>
          <?php echo e(\Carbon\Carbon::parse($pengajuan->tgl_awal)->format('d-M-Y')); ?> sd <?php echo e(\Carbon\Carbon::parse($pengajuan->tgl_akhir)->format('d-M-Y')); ?>

          <?php else: ?>
          -
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="table-section">
  <h2>Pengajuan Pegawai</h2>

  <form id="pengajuanForm" method="POST" action="<?php echo e(route('pengajuan.update')); ?>">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="tableScroll">
      <table>
        <colgroup>
          <col style="width: 110px;"> <!-- NIP -->
          <col style="width: 150px;"> <!-- Nama Pegawai -->
          <col style="width: 70px;"> <!-- Senin WFO -->
          <col style="width: 70px;"> <!-- Senin WFA -->
          <col style="width: 70px;"> <!-- Selasa WFO -->
          <col style="width: 70px;"> <!-- Selasa WFA -->
          <col style="width: 70px;"> <!-- Rabu WFO -->
          <col style="width: 70px;"> <!-- Rabu WFA -->
          <col style="width: 70px;"> <!-- Kamis WFO -->
          <col style="width: 70px;"> <!-- Kamis WFA -->
          <col style="width: 70px;"> <!-- Jumat WFO -->
          <col style="width: 70px;"> <!-- Jumat WFA -->
          <col style="width: 70px;"> <!-- Jumlah WFO -->
          <col style="width: 70px;"> <!-- Jumlah WFA -->
          <col style="width: 70px;"> <!-- % WFO -->
          <col style="width: 70px;"> <!-- % WFA -->
        </colgroup>
        <thead>
          <tr>
            <th rowspan="2" class="text-left col-nip">NIP</th>
            <th rowspan="2" class="text-left col-nama">Nama Pegawai</th>
            <th colspan="2" class="senin-col col-day-header <?php echo e(($hariLibur['senin'] ?? false) ? 'holiday-header' : ''); ?>">
              Senin
              <?php if($hariLibur['senin'] ?? false): ?>
              <div class="libur-badge">LIBUR</div>
              <?php endif; ?>
            </th>
            <th colspan="2" class="selasa-col col-day-header <?php echo e(($hariLibur['selasa'] ?? false) ? 'holiday-header' : ''); ?>">
              Selasa
              <?php if($hariLibur['selasa'] ?? false): ?>
              <div class="libur-badge">LIBUR</div>
              <?php endif; ?>
            </th>
            <th colspan="2" class="rabu-col col-day-header <?php echo e(($hariLibur['rabu'] ?? false) ? 'holiday-header' : ''); ?>">
              Rabu
              <?php if($hariLibur['rabu'] ?? false): ?>
              <div class="libur-badge">LIBUR</div>
              <?php endif; ?>
            </th>
            <th colspan="2" class="kamis-col col-day-header <?php echo e(($hariLibur['kamis'] ?? false) ? 'holiday-header' : ''); ?>">
              Kamis
              <?php if($hariLibur['kamis'] ?? false): ?>
              <div class="libur-badge">LIBUR</div>
              <?php endif; ?>
            </th>
            <th colspan="2" class="jumat-col col-day-header <?php echo e(($hariLibur['jumat'] ?? false) ? 'holiday-header' : ''); ?>">
              Jum'at
              <?php if($hariLibur['jumat'] ?? false): ?>
              <div class="libur-badge">LIBUR</div>
              <?php endif; ?>
            </th>
            <th colspan="2" class="col-day-header">Jumlah</th>
            <th colspan="2" class="col-day-header">%</th>
          </tr>
          <tr>
            <th class="senin-col col-day <?php echo e(($hariLibur['senin'] ?? false) ? 'holiday-header' : ''); ?>">WFO</th>
            <th class="senin-col col-day <?php echo e(($hariLibur['senin'] ?? false) ? 'holiday-header' : ''); ?>">WFA</th>
            <th class="selasa-col col-day <?php echo e(($hariLibur['selasa'] ?? false) ? 'holiday-header' : ''); ?>">WFO</th>
            <th class="selasa-col col-day <?php echo e(($hariLibur['selasa'] ?? false) ? 'holiday-header' : ''); ?>">WFA</th>
            <th class="rabu-col col-day <?php echo e(($hariLibur['rabu'] ?? false) ? 'holiday-header' : ''); ?>">WFO</th>
            <th class="rabu-col col-day <?php echo e(($hariLibur['rabu'] ?? false) ? 'holiday-header' : ''); ?>">WFA</th>
            <th class="kamis-col col-day <?php echo e(($hariLibur['kamis'] ?? false) ? 'holiday-header' : ''); ?>">WFO</th>
            <th class="kamis-col col-day <?php echo e(($hariLibur['kamis'] ?? false) ? 'holiday-header' : ''); ?>">WFA</th>
            <th class="jumat-col col-day <?php echo e(($hariLibur['jumat'] ?? false) ? 'holiday-header' : ''); ?>">WFO</th>
            <th class="jumat-col col-day <?php echo e(($hariLibur['jumat'] ?? false) ? 'holiday-header' : ''); ?>">WFA</th>
            <th class="col-day">WFO</th>
            <th class="col-day">WFA</th>
            <th class="col-day">WFO</th>
            <th class="col-day">WFA</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $totalPegawai = $details->count();
          $wfoCount = ['senin' => 0, 'selasa' => 0, 'rabu' => 0, 'kamis' => 0, 'jumat' => 0];
          $wfaCount = ['senin' => 0, 'selasa' => 0, 'rabu' => 0, 'kamis' => 0, 'jumat' => 0];
          ?>

          <!-- Summary Row: Jumlah -->
          <tr class="summary-row totals">
            <td colspan="2" class="text-left col-nama">Jumlah</td>
            <td class="senin-wfo-total senin-col col-day <?php echo e(($hariLibur['senin'] ?? false) ? 'holiday-col' : ''); ?>">0</td>
            <td class="senin-wfa-total senin-col col-day <?php echo e(($hariLibur['senin'] ?? false) ? 'holiday-col' : ''); ?>">0</td>
            <td class="selasa-wfo-total selasa-col col-day <?php echo e(($hariLibur['selasa'] ?? false) ? 'holiday-col' : ''); ?>">0</td>
            <td class="selasa-wfa-total selasa-col col-day <?php echo e(($hariLibur['selasa'] ?? false) ? 'holiday-col' : ''); ?>">0</td>
            <td class="rabu-wfo-total rabu-col col-day <?php echo e(($hariLibur['rabu'] ?? false) ? 'holiday-col' : ''); ?>">0</td>
            <td class="rabu-wfa-total rabu-col col-day <?php echo e(($hariLibur['rabu'] ?? false) ? 'holiday-col' : ''); ?>">0</td>
            <td class="kamis-wfo-total kamis-col col-day <?php echo e(($hariLibur['kamis'] ?? false) ? 'holiday-col' : ''); ?>">0</td>
            <td class="kamis-wfa-total kamis-col col-day <?php echo e(($hariLibur['kamis'] ?? false) ? 'holiday-col' : ''); ?>">0</td>
            <td class="jumat-wfo-total jumat-col col-day <?php echo e(($hariLibur['jumat'] ?? false) ? 'holiday-col' : ''); ?>">0</td>
            <td class="jumat-wfa-total jumat-col col-day <?php echo e(($hariLibur['jumat'] ?? false) ? 'holiday-col' : ''); ?>">0</td>
            <td class="col-day">-</td>
            <td class="col-day">-</td>
            <td class="col-day">-</td>
            <td class="col-day">-</td>
          </tr>

          <!-- Summary Row: Persentase -->
          <tr class="summary-row percentages">
            <td colspan="2" class="text-left col-nama">Persentase</td>
            <td class="percentage-cell wfo senin-wfo-percentage senin-col col-day <?php echo e(($hariLibur['senin'] ?? false) ? 'holiday-col' : ''); ?>">0%</td>
            <td class="percentage-cell wfa senin-wfa-percentage senin-col col-day <?php echo e(($hariLibur['senin'] ?? false) ? 'holiday-col' : ''); ?>">0%</td>
            <td class="percentage-cell wfo selasa-wfo-percentage selasa-col col-day <?php echo e(($hariLibur['selasa'] ?? false) ? 'holiday-col' : ''); ?>">0%</td>
            <td class="percentage-cell wfa selasa-wfa-percentage selasa-col col-day <?php echo e(($hariLibur['selasa'] ?? false) ? 'holiday-col' : ''); ?>">0%</td>
            <td class="percentage-cell wfo rabu-wfo-percentage rabu-col col-day <?php echo e(($hariLibur['rabu'] ?? false) ? 'holiday-col' : ''); ?>">0%</td>
            <td class="percentage-cell wfa rabu-wfa-percentage rabu-col col-day <?php echo e(($hariLibur['rabu'] ?? false) ? 'holiday-col' : ''); ?>">0%</td>
            <td class="percentage-cell wfo kamis-wfo-percentage kamis-col col-day <?php echo e(($hariLibur['kamis'] ?? false) ? 'holiday-col' : ''); ?>">0%</td>
            <td class="percentage-cell wfa kamis-wfa-percentage kamis-col col-day <?php echo e(($hariLibur['kamis'] ?? false) ? 'holiday-col' : ''); ?>">0%</td>
            <td class="percentage-cell wfo jumat-wfo-percentage jumat-col col-day <?php echo e(($hariLibur['jumat'] ?? false) ? 'holiday-col' : ''); ?>">0%</td>
            <td class="percentage-cell wfa jumat-wfa-percentage jumat-col col-day <?php echo e(($hariLibur['jumat'] ?? false) ? 'holiday-col' : ''); ?>">0%</td>
            <td class="col-day">-</td>
            <td class="col-day">-</td>
            <td class="col-day">-</td>
            <td class="col-day">-</td>
          </tr>

          <?php $__empty_1 = true; $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
          // Count for summary - skip hari libur (null values)
          if(!($hariLibur['senin'] ?? false)) {
          if($detail->senin) $wfoCount['senin']++; else $wfaCount['senin']++;
          }
          if(!($hariLibur['selasa'] ?? false)) {
          if($detail->selasa) $wfoCount['selasa']++; else $wfaCount['selasa']++;
          }
          if(!($hariLibur['rabu'] ?? false)) {
          if($detail->rabu) $wfoCount['rabu']++; else $wfaCount['rabu']++;
          }
          if(!($hariLibur['kamis'] ?? false)) {
          if($detail->kamis) $wfoCount['kamis']++; else $wfaCount['kamis']++;
          }
          if(!($hariLibur['jumat'] ?? false)) {
          if($detail->jumat) $wfoCount['jumat']++; else $wfaCount['jumat']++;
          }

          // Calculate individual totals - exclude hari libur
          $hariKerja = 5 - (($hariLibur['senin'] ?? false) ? 1 : 0) - (($hariLibur['selasa'] ?? false) ? 1 : 0) - (($hariLibur['rabu'] ?? false) ? 1 : 0) - (($hariLibur['kamis'] ?? false) ? 1 : 0) - (($hariLibur['jumat'] ?? false) ? 1 : 0);
          $wfoTotal = ((!($hariLibur['senin'] ?? false) && $detail->senin) ? 1 : 0) + ((!($hariLibur['selasa'] ?? false) && $detail->selasa) ? 1 : 0) + ((!($hariLibur['rabu'] ?? false) && $detail->rabu) ? 1 : 0) + ((!($hariLibur['kamis'] ?? false) && $detail->kamis) ? 1 : 0) + ((!($hariLibur['jumat'] ?? false) && $detail->jumat) ? 1 : 0);
          $wfaTotal = $hariKerja - $wfoTotal;
          $wfoPercentage = $hariKerja > 0 ? ($wfoTotal / $hariKerja) * 100 : 0;
          $wfaPercentage = 100 - $wfoPercentage;
          ?>

          <tr class="employee-row" data-nip="<?php echo e($detail->nip); ?>">
            <td class="text-left col-nip"><?php echo e($detail->nip); ?></td>
            <td class="text-left col-nama"><?php echo e(strtoupper($detail->nama)); ?></td>

            <!-- Senin -->
            <td class="radio-cell senin-col col-day <?php echo e(($hariLibur['senin'] ?? false) ? 'holiday-col' : ''); ?>">
              <?php if($hariLibur['senin'] ?? false): ?>
              <span style="color:#991b1b;font-size:11px">-</span>
              <?php else: ?>
              <input type="radio" name="attendance[<?php echo e($detail->nip); ?>][senin]" value="1" <?php echo e($detail->senin ? 'checked' : ''); ?> class="day-radio" data-day="senin" <?php echo e($readOnly ? 'disabled' : ''); ?>>
              <?php endif; ?>
            </td>
            <td class="radio-cell senin-col col-day <?php echo e(($hariLibur['senin'] ?? false) ? 'holiday-col' : ''); ?>">
              <?php if($hariLibur['senin'] ?? false): ?>
              <span style="color:#991b1b;font-size:11px">-</span>
              <?php else: ?>
              <input type="radio" name="attendance[<?php echo e($detail->nip); ?>][senin]" value="0" <?php echo e(!$detail->senin ? 'checked' : ''); ?> class="day-radio" data-day="senin" <?php echo e($readOnly ? 'disabled' : ''); ?>>
              <?php endif; ?>
            </td>

            <!-- Selasa -->
            <td class="radio-cell selasa-col col-day <?php echo e(($hariLibur['selasa'] ?? false) ? 'holiday-col' : ''); ?>">
              <?php if($hariLibur['selasa'] ?? false): ?>
              <span style="color:#991b1b;font-size:11px">-</span>
              <?php else: ?>
              <input type="radio" name="attendance[<?php echo e($detail->nip); ?>][selasa]" value="1" <?php echo e($detail->selasa ? 'checked' : ''); ?> class="day-radio" data-day="selasa" <?php echo e($readOnly ? 'disabled' : ''); ?>>
              <?php endif; ?>
            </td>
            <td class="radio-cell selasa-col col-day <?php echo e(($hariLibur['selasa'] ?? false) ? 'holiday-col' : ''); ?>">
              <?php if($hariLibur['selasa'] ?? false): ?>
              <span style="color:#991b1b;font-size:11px">-</span>
              <?php else: ?>
              <input type="radio" name="attendance[<?php echo e($detail->nip); ?>][selasa]" value="0" <?php echo e(!$detail->selasa ? 'checked' : ''); ?> class="day-radio" data-day="selasa" <?php echo e($readOnly ? 'disabled' : ''); ?>>
              <?php endif; ?>
            </td>

            <!-- Rabu -->
            <td class="radio-cell rabu-col col-day <?php echo e(($hariLibur['rabu'] ?? false) ? 'holiday-col' : ''); ?>">
              <?php if($hariLibur['rabu'] ?? false): ?>
              <span style="color:#991b1b;font-size:11px">-</span>
              <?php else: ?>
              <input type="radio" name="attendance[<?php echo e($detail->nip); ?>][rabu]" value="1" <?php echo e($detail->rabu ? 'checked' : ''); ?> class="day-radio" data-day="rabu" <?php echo e($readOnly ? 'disabled' : ''); ?>>
              <?php endif; ?>
            </td>
            <td class="radio-cell rabu-col col-day <?php echo e(($hariLibur['rabu'] ?? false) ? 'holiday-col' : ''); ?>">
              <?php if($hariLibur['rabu'] ?? false): ?>
              <span style="color:#991b1b;font-size:11px">-</span>
              <?php else: ?>
              <input type="radio" name="attendance[<?php echo e($detail->nip); ?>][rabu]" value="0" <?php echo e(!$detail->rabu ? 'checked' : ''); ?> class="day-radio" data-day="rabu" <?php echo e($readOnly ? 'disabled' : ''); ?>>
              <?php endif; ?>
            </td>

            <!-- Kamis -->
            <td class="radio-cell kamis-col col-day <?php echo e(($hariLibur['kamis'] ?? false) ? 'holiday-col' : ''); ?>">
              <?php if($hariLibur['kamis'] ?? false): ?>
              <span style="color:#991b1b;font-size:11px">-</span>
              <?php else: ?>
              <input type="radio" name="attendance[<?php echo e($detail->nip); ?>][kamis]" value="1" <?php echo e($detail->kamis ? 'checked' : ''); ?> class="day-radio" data-day="kamis" <?php echo e($readOnly ? 'disabled' : ''); ?>>
              <?php endif; ?>
            </td>
            <td class="radio-cell kamis-col col-day <?php echo e(($hariLibur['kamis'] ?? false) ? 'holiday-col' : ''); ?>">
              <?php if($hariLibur['kamis'] ?? false): ?>
              <span style="color:#991b1b;font-size:11px">-</span>
              <?php else: ?>
              <input type="radio" name="attendance[<?php echo e($detail->nip); ?>][kamis]" value="0" <?php echo e(!$detail->kamis ? 'checked' : ''); ?> class="day-radio" data-day="kamis" <?php echo e($readOnly ? 'disabled' : ''); ?>>
              <?php endif; ?>
            </td>

            <!-- Jumat -->
            <td class="radio-cell jumat-col col-day <?php echo e(($hariLibur['jumat'] ?? false) ? 'holiday-col' : ''); ?>">
              <?php if($hariLibur['jumat'] ?? false): ?>
              <span style="color:#991b1b;font-size:11px">-</span>
              <?php else: ?>
              <input type="radio" name="attendance[<?php echo e($detail->nip); ?>][jumat]" value="1" <?php echo e($detail->jumat ? 'checked' : ''); ?> class="day-radio" data-day="jumat" <?php echo e($readOnly ? 'disabled' : ''); ?>>
              <?php endif; ?>
            </td>
            <td class="radio-cell jumat-col col-day <?php echo e(($hariLibur['jumat'] ?? false) ? 'holiday-col' : ''); ?>">
              <?php if($hariLibur['jumat'] ?? false): ?>
              <span style="color:#991b1b;font-size:11px">-</span>
              <?php else: ?>
              <input type="radio" name="attendance[<?php echo e($detail->nip); ?>][jumat]" value="0" <?php echo e(!$detail->jumat ? 'checked' : ''); ?> class="day-radio" data-day="jumat" <?php echo e($readOnly ? 'disabled' : ''); ?>>
              <?php endif; ?>
            </td>

            <!-- Jumlah WFO/WFA -->
            <td class="wfo-count col-day"><?php echo e($wfoTotal); ?></td>
            <td class="wfa-count col-day"><?php echo e($wfaTotal); ?></td>

            <!-- Persentase WFO/WFA -->
            <td class="wfo-percentage col-day"><?php echo e(number_format($wfoPercentage, 0)); ?>%</td>
            <td class="wfa-percentage col-day"><?php echo e(number_format($wfaPercentage, 0)); ?>%</td>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="16" style="text-align: center; color: #94a3b8; padding: 2rem;">
              Tidak ada data pegawai
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if($details->count() > 0 && !$readOnly): ?>
    <div id="overTargetWarning" class="alert alert-error" style="display: none; margin-top: 1rem;">
      ⚠️ <strong>Tidak dapat menyimpan!</strong> Persentase WFO melebihi batas maksimal <?php echo e($pengajuan->persentase_decimal ?? 50); ?>%. Silakan kurangi jumlah WFO pada hari yang berwarna merah.
    </div>
    
    <!-- Auto-save status indicator -->
    <div id="autoSaveStatus" style="margin-top: 1rem; padding: 8px 12px; border-radius: 6px; font-size: 0.8rem; display: none;">
    </div>

    <div style="display: flex; gap: 12px; align-items: center; margin-top: 1rem; flex-wrap: wrap;">
      <!-- Tombol Simpan Draft -->
      <button type="button" id="saveDraftBtn" class="save-button" style="background: #6b7280;">
        💾 Simpan Draft
      </button>
      
      <!-- Tombol Simpan Permanen -->
      <button type="submit" name="save_final" value="1" class="save-button" id="saveFinalBtn">
        ✅ Simpan Permanen
      </button>

      <span style="font-size: 0.8rem; color: #64748b;">
        <strong>Draft:</strong> Progress tersimpan, masih bisa diedit. 
        <strong>Permanen:</strong> Data final, tidak bisa diedit lagi.
      </span>
    </div>
    <?php endif; ?>
  </form>
</div>

<script>
  // Real-time calculation when radio buttons change
  const form = document.getElementById('pengajuanForm');
  const totalPegawai = <?php echo e($totalPegawai); ?>;
  const targetPercentage = <?php echo e($pengajuan->persentase_decimal ?? 50); ?>;
  const saveDraftBtn = document.getElementById('saveDraftBtn');
  const saveFinalBtn = document.getElementById('saveFinalBtn');
  const overTargetWarning = document.getElementById('overTargetWarning');
  const autoSaveStatus = document.getElementById('autoSaveStatus');

  // Hari libur dari server
  <?php
  $hariLiburJs = $hariLibur ?? ['senin' => false, 'selasa' => false, 'rabu' => false, 'kamis' => false, 'jumat' => false];
  ?>
  const hariLibur = <?php echo json_encode($hariLiburJs, 15, 512) ?>;

  // Hitung jumlah hari kerja (exclude libur)
  const hariKerja = 5 - (hariLibur.senin ? 1 : 0) - (hariLibur.selasa ? 1 : 0) - (hariLibur.rabu ? 1 : 0) - (hariLibur.kamis ? 1 : 0) - (hariLibur.jumat ? 1 : 0);

  // Auto-save timer
  let autoSaveTimer = null;
  let hasUnsavedChanges = false;

  // Initialize summary on page load
  document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
  });

  form.addEventListener('change', function(e) {
    if (e.target.classList.contains('day-radio')) {
      updateSummary();
      hasUnsavedChanges = true;
      
      // Auto-save after 3 seconds of no changes
      clearTimeout(autoSaveTimer);
      autoSaveTimer = setTimeout(function() {
        saveDraft(true);
      }, 3000);
    }
  });

  // Save draft function (via AJAX)
  async function saveDraft(isAutoSave = false) {
    if (!hasUnsavedChanges && isAutoSave) return;
    
    const formData = new FormData(form);
    const attendance = {};
    
    // Parse form data to attendance object
    for (let [key, value] of formData.entries()) {
      const match = key.match(/attendance\[([^\]]+)\]\[([^\]]+)\]/);
      if (match) {
        const nip = match[1];
        const day = match[2];
        if (!attendance[nip]) attendance[nip] = {};
        attendance[nip][day] = value;
      }
    }

    // Show saving status
    showAutoSaveStatus('saving', isAutoSave ? 'Menyimpan otomatis...' : 'Menyimpan draft...');

    try {
      const response = await fetch('<?php echo e(route("pengajuan.saveDraft")); ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ attendance })
      });

      const result = await response.json();

      if (result.success) {
        hasUnsavedChanges = false;
        showAutoSaveStatus('success', isAutoSave ? `Auto-saved ${result.saved_at}` : 'Draft tersimpan!');
      } else {
        showAutoSaveStatus('error', result.message || 'Gagal menyimpan');
      }
    } catch (error) {
      console.error('Save draft error:', error);
      showAutoSaveStatus('error', 'Gagal menyimpan: ' + error.message);
    }
  }

  function showAutoSaveStatus(type, message) {
    if (!autoSaveStatus) return;
    
    autoSaveStatus.style.display = 'block';
    autoSaveStatus.textContent = message;
    
    if (type === 'saving') {
      autoSaveStatus.style.background = '#fef3c7';
      autoSaveStatus.style.color = '#92400e';
      autoSaveStatus.style.border = '1px solid #fcd34d';
    } else if (type === 'success') {
      autoSaveStatus.style.background = '#dcfce7';
      autoSaveStatus.style.color = '#166534';
      autoSaveStatus.style.border = '1px solid #86efac';
      
      // Hide after 3 seconds for success
      setTimeout(() => {
        autoSaveStatus.style.display = 'none';
      }, 3000);
    } else if (type === 'error') {
      autoSaveStatus.style.background = '#fee2e2';
      autoSaveStatus.style.color = '#991b1b';
      autoSaveStatus.style.border = '1px solid #fca5a5';
    }
  }

  // Manual save draft button
  if (saveDraftBtn) {
    saveDraftBtn.addEventListener('click', function() {
      saveDraft(false);
    });
  }

  // Confirm before save final
  if (saveFinalBtn) {
    saveFinalBtn.addEventListener('click', function(e) {
      if (!confirm('Apakah Anda yakin ingin menyimpan PERMANEN?\n\nSetelah disimpan permanen, Anda TIDAK BISA mengedit lagi.')) {
        e.preventDefault();
      }
    });
  }

  // Warn before leaving if unsaved changes
  window.addEventListener('beforeunload', function(e) {
    if (hasUnsavedChanges) {
      e.preventDefault();
      e.returnValue = 'Ada perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
    }
  });

  // Clear warning when form is submitting
  form.addEventListener('submit', function() {
    hasUnsavedChanges = false;
  });

  function updateSummary() {
    const days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
    let hasOverTarget = false;

    days.forEach(day => {
      // Skip hari libur
      if (hariLibur[day]) {
        document.querySelector(`.${day}-wfo-total`).textContent = '-';
        document.querySelector(`.${day}-wfa-total`).textContent = '-';
        document.querySelector(`.${day}-wfo-percentage`).textContent = '-';
        document.querySelector(`.${day}-wfa-percentage`).textContent = '-';
        return;
      }

      // Count WFO/WFA for this day
      let wfoCount = 0;
      let wfaCount = 0;

      document.querySelectorAll(`input[name*="[${day}]"]`).forEach(radio => {
        if (radio.checked) {
          if (radio.value === '1') {
            wfoCount++;
          } else {
            wfaCount++;
          }
        }
      });

      // Update totals in summary row
      document.querySelector(`.${day}-wfo-total`).textContent = wfoCount;
      document.querySelector(`.${day}-wfa-total`).textContent = wfaCount;

      // Calculate and update percentages
      const wfoPercentage = totalPegawai > 0 ? (wfoCount / totalPegawai) * 100 : 0;
      const wfaPercentage = 100 - wfoPercentage;

      const wfoCell = document.querySelector(`.${day}-wfo-percentage`);
      const wfaCell = document.querySelector(`.${day}-wfa-percentage`);

      wfoCell.textContent = Math.round(wfoPercentage) + '%';
      wfaCell.textContent = Math.round(wfaPercentage) + '%';

      // Color validation - red if over target
      if (wfoPercentage > targetPercentage) {
        wfoCell.classList.add('over-target');
        hasOverTarget = true;
      } else {
        wfoCell.classList.remove('over-target');
      }
    });

    // Disable/Enable save buttons based on validation
    if (saveDraftBtn) {
      if (hasOverTarget) {
        saveDraftBtn.disabled = true;
        saveDraftBtn.style.opacity = '0.5';
        saveDraftBtn.style.cursor = 'not-allowed';
      } else {
        saveDraftBtn.disabled = false;
        saveDraftBtn.style.opacity = '1';
        saveDraftBtn.style.cursor = 'pointer';
      }
    }

    if (saveFinalBtn) {
      if (hasOverTarget) {
        saveFinalBtn.disabled = true;
        saveFinalBtn.style.opacity = '0.5';
        saveFinalBtn.style.cursor = 'not-allowed';
        saveFinalBtn.title = `Persentase WFO melebihi batas maksimal ${targetPercentage}%`;
        if (overTargetWarning) overTargetWarning.style.display = 'flex';
      } else {
        saveFinalBtn.disabled = false;
        saveFinalBtn.style.opacity = '1';
        saveFinalBtn.style.cursor = 'pointer';
        saveFinalBtn.title = '';
        if (overTargetWarning) overTargetWarning.style.display = 'none';
      }
    }

    // Update individual row totals
    document.querySelectorAll('.employee-row').forEach(row => {
      let wfoTotal = 0;
      row.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
        if (radio.value === '1') wfoTotal++;
      });

      const wfaTotal = hariKerja - wfoTotal;
      const wfoPercentage = hariKerja > 0 ? (wfoTotal / hariKerja) * 100 : 0;
      const wfaPercentage = 100 - wfoPercentage;

      row.querySelector('.wfo-count').textContent = wfoTotal;
      row.querySelector('.wfa-count').textContent = wfaTotal;
      row.querySelector('.wfo-percentage').textContent = Math.round(wfoPercentage) + '%';
      row.querySelector('.wfa-percentage').textContent = Math.round(wfaPercentage) + '%';
    });
  }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kevannn\Documents\FILE MAGANG\AbsensiWika\resources\views/admin/pengajuan/show.blade.php ENDPATH**/ ?>