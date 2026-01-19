@extends('layouts.app')

@section('title', 'WG Absen — Dashboard')

@section('styles')
<style>
  .dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-top: 24px
  }

  .dashboard-card {
    background: #fff;
    border-radius: 18px;
    border: 1px solid #e7eaf3;
    box-shadow: 0 10px 35px rgba(35, 45, 120, .08);
    padding: 32px;
    text-decoration: none;
    color: var(--text);
    transition: all 0.3s ease;
    cursor: pointer
  }

  .dashboard-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 45px rgba(35, 45, 120, .15);
    border-color: var(--primary)
  }

  .dashboard-card .icon {
    font-size: 48px;
    margin-bottom: 16px;
    display: block
  }

  .dashboard-card h3 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 8px;
    color: var(--text)
  }

  .dashboard-card p {
    font-size: 14px;
    color: var(--text-muted);
    line-height: 1.6;
    margin: 0
  }

  .dashboard-card.primary {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: #fff
  }

  .dashboard-card.primary h3,
  .dashboard-card.primary p {
    color: #fff
  }

  .welcome {
    background: #fff;
    border-radius: 18px;
    border: 1px solid #e7eaf3;
    box-shadow: 0 10px 35px rgba(35, 45, 120, .08);
    padding: 32px;
    margin-bottom: 24px
  }

  .welcome h1 {
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 12px;
    color: var(--text)
  }

  .welcome p {
    font-size: 16px;
    color: var(--text-muted);
    margin: 0
  }
</style>
@endsection

@section('content')
<div class="welcome">
  <h1>👋 Selamat Datang, {{ auth()->user()->name ?? 'Admin' }}</h1>
  <p>Dashboard Admin - Kelola sistem absensi dan pengajuan WFO</p>
</div>

<div class="dashboard-grid">
  <a href="{{ route('pengajuan.index') }}" class="dashboard-card primary">
    <span class="icon">📋</span>
    <h3>Pengajuan WFO</h3>
    <p>Kelola pengajuan work from office dan work from anywhere dari semua biro</p>
  </a>

  <a href="{{ route('report.index') }}" class="dashboard-card">
    <span class="icon">📊</span>
    <h3>Report Absensi</h3>
    <p>Lihat laporan absensi pegawai berdasarkan periode dan unit kerja</p>
  </a>

  <a href="{{ route('admin.kalender') }}" class="dashboard-card">
    <span class="icon">📅</span>
    <h3>Kalender Kerja</h3>
    <p>Atur periode kalender kerja, minggu, dan persentase target WFO</p>
  </a>

  <a href="{{ route('settings.index') }}" class="dashboard-card">
    <span class="icon">⚙️</span>
    <h3>Setting User</h3>
    <p>Kelola data pengguna, biro, jabatan, dan role akses sistem</p>
  </a>

  <div class="dashboard-card" style="background:#f9fafb;cursor:default">
    <span class="icon">📊</span>
    <h3>Statistik</h3>
    <p>Total Biro: <strong>{{ DB::table('biro')->count() }}</strong><br>
      Total User: <strong>{{ DB::table('users')->count() }}</strong></p>
  </div>
</div>
@endsection