@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
<style>
.content-header {
  margin-bottom: 2rem;
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
@endsection

@section('content')
@if(session('status'))
  <div class="statusMsg">{{ session('status') }}</div>
@endif

<div class="content-header">
  <h1>Dashboard</h1>
  <p>Selamat datang di sistem absensi Wika</p>
</div>

<div class="grid">
  @if($isAdmin ?? false)
    <a class="tile" href="{{ route('pengajuan.index') }}">
      <div class="t">📋 Pengajuan WFO</div>
      <div class="d">Kelola pengajuan work from office dan work from anywhere dari semua biro.</div>
    </a>

    <a class="tile" href="{{ route('admin.kalender') }}">
      <div class="t">📅 Kalender Kerja</div>
      <div class="d">Input periode (minggu Senin–Minggu) dan lihat data kalender kerja.</div>
    </a>

    <a class="tile" href="{{ route('settings.index') }}">
      <div class="t">⚙️ Setting User</div>
      <div class="d">Kelola user, biro, jabatan, dan role dalam satu tempat.</div>
    </a>
  @else
    {{-- User biasa: hanya tampilkan menu pengajuan WFO biro sendiri --}}
    <a class="tile" href="{{ route('pengajuan.index') }}">
      <div class="t">📋 Pengajuan WFO</div>
      <div class="d">Lihat dan kelola pengajuan work from office / work from anywhere untuk {{ $biroName ?? 'biro Anda' }}.</div>
    </a>
  @endif
</div>
@endsection
