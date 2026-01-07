@php
  /** @var string $fallback */
  $fallback = $fallback ?? route('dashboard');
  $href = \App\Support\BackLink::url($fallback);
@endphp
<a href="{{ $href }}" style="text-decoration:none">
  <button class="btn" type="button">Kembali</button>
</a>
