<?php /** 404 — Page Not Found */ ?>
<div style="min-height:70vh;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:var(--space-12) var(--space-4)">
  <div style="font-size:120px;line-height:1;margin-bottom:var(--space-4);animation:pulse 2s ease-in-out infinite">🏨</div>
  <h1 style="font-size:clamp(60px,10vw,120px);font-weight:800;color:var(--color-gold);line-height:1;margin-bottom:var(--space-2)">404</h1>
  <h2 style="font-size:var(--font-size-2xl);margin-bottom:var(--space-3);color:var(--text-primary)">Halaman Tidak Ditemukan</h2>
  <p style="color:var(--text-muted);max-width:400px;margin-bottom:var(--space-8);line-height:1.6">
    Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan. Silakan kembali ke halaman utama.
  </p>
  <div style="display:flex;gap:var(--space-3);flex-wrap:wrap;justify-content:center">
    <a href="<?= BASE_URL ?>" class="btn btn-primary btn-lg">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      Kembali ke Beranda
    </a>
    <a href="<?= BASE_URL ?>?page=destinasi" class="btn btn-outline btn-lg">Cari Hotel</a>
  </div>
</div>
<style>
@keyframes pulse {0%,100%{transform:scale(1)}50%{transform:scale(1.05)}}
</style>
