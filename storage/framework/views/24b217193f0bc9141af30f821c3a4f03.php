

<?php $__env->startSection('title', 'WG Absen — Dashboard'); ?>

<?php $__env->startSection('styles'); ?>
<style>
  .content-header {
    margin-bottom: 2rem;
    margin-top: 1rem;
  }

  .content-header h1 {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 0.5rem;
  }

  .content-header p {
    color: var(--text-muted);
    font-size: 1rem;
  }

  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
  }

  .tile {
    background: #fff;
    border-radius: 16px;
    padding: 1.5rem;
    border: 1px solid #e7eaf3;
    box-shadow: 0 4px 12px rgba(35, 45, 120, 0.06);
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    display: block;
  }

  .tile:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(35, 45, 120, 0.12);
    border-color: var(--primary);
  }

  .tile .t {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  .tile .d {
    font-size: 0.9rem;
    color: var(--text-muted);
    line-height: 1.5;
  }

  .statusMsg {
    background: #d1fae5;
    color: #065f46;
    padding: 1rem 1.25rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    font-weight: 500;
  }

  @media (max-width: 600px) {
    .grid {
      grid-template-columns: 1fr;
    }
  }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('status')): ?>
<div class="statusMsg"><?php echo e(session('status')); ?></div>
<?php endif; ?>

<div class="content-header">
  <h1>Dashboard</h1>
  <p>Selamat datang di sistem absensi Wika</p>
</div>

<div class="grid">
  
  <?php if($canAccessKalender ?? false): ?>
  <a class="tile" href="<?php echo e(route('admin.kalender')); ?>">
    <div class="t">📅 Kalender Kerja</div>
    <div class="d">Input periode (minggu Senin–Minggu) dan lihat data kalender kerja.</div>
  </a>
  <?php endif; ?>

  
  <a class="tile" href="<?php echo e(route('pengajuan.index')); ?>">
    <div class="t">📋 Pengajuan WFO</div>
    <div class="d">
      <?php if($canAccessAllBiro ?? false): ?>
      Kelola pengajuan work from office dan work from anywhere dari semua biro.
      <?php else: ?>
      Lihat jadwal pengajuan WFO/WFA untuk <?php echo e($biroName ?? 'biro Anda'); ?>.
      <?php endif; ?>
    </div>
  </a>

  
  <?php if($canAccessReport ?? false): ?>
  <a class="tile" href="<?php echo e(route('report.index')); ?>">
    <div class="t">📊 Report Absensi</div>
    <div class="d">Lihat laporan absensi pegawai berdasarkan periode dan unit kerja.</div>
  </a>
  <?php endif; ?>

  
  <?php if($canAccessLaporanMakan ?? false): ?>
  <a class="tile" href="<?php echo e(route('makan.index')); ?>">
    <div class="t">🍽️ Laporan Uang Makan</div>
    <div class="d">Lihat laporan uang makan pegawai berdasarkan kehadiran WFO.</div>
  </a>
  <?php endif; ?>

  
  <?php if($canAccessSettings ?? false): ?>
  <a class="tile" href="<?php echo e(route('settings.index')); ?>">
    <div class="t">⚙️ Setting User</div>
    <div class="d">Kelola user, biro, jabatan, dan role dalam satu tempat.</div>
  </a>
  <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Kevannn\Documents\FILE MAGANG\AbsensiWika\resources\views/dashboard.blade.php ENDPATH**/ ?>