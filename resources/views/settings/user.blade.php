@extends('layouts.app')

@section('title', 'Setting User')

@section('styles')
  .content-header {
    margin-bottom: 24px
  }

  .content-header h1 {
    font-size: 26px;
    font-weight: 700;
    margin: 0 0 8px;
    color: var(--text)
  }

  .content-header .subtitle {
    font-size: 15px;
    color: var(--text-muted)
  }

  .grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px
  }

  @media(max-width:900px) {
    .grid {
      grid-template-columns: 1fr
    }
  }

  .tile {
    background: #fff;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    padding: 20px;
    transition: all 0.2s ease;
    cursor: pointer;
    text-decoration: none;
    display: block
  }

  .tile:hover {
    border-color: var(--primary);
    box-shadow: 0 8px 20px rgba(89, 102, 247, 0.15);
    transform: translateY(-2px)
  }

  .tile-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--text);
    margin: 0 0 8px
  }

  .tile-desc {
    font-size: 14px;
    color: var(--text-muted);
    margin: 0;
    line-height: 1.5
  }
@endsection

@section('content')
<div class="content-header">
  <h1>Setting User</h1>
  <div class="subtitle">Kelola data user</div>
</div>

<div class="grid">
  <a class="tile" href="{{ route('users.index') }}">
    <div class="tile-title">View / Edit User</div>
    <div class="tile-desc">Lihat daftar user, lalu edit/hapus dari tabel.</div>
  </a>

  <a class="tile" href="{{ route('users.create') }}">
    <div class="tile-title">Add User</div>
    <div class="tile-desc">Tambah user baru.</div>
  </a>
</div>
@endsection