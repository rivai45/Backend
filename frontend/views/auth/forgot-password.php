<?php /** Forgot Password Page */ ?>
<style>
.auth-page { min-height:80vh; display:flex; align-items:center; justify-content:center; padding:var(--space-8) var(--space-4); }
.auth-card { background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--radius-xl); padding:var(--space-10); width:100%; max-width:440px; }
.auth-logo { text-align:center; margin-bottom:var(--space-8); }
.auth-logo svg { width:48px; height:48px; color:var(--color-gold); }
.auth-logo h2 { font-size:var(--font-size-2xl); font-weight:800; margin-top:var(--space-3); }
.auth-logo p { color:var(--text-muted); font-size:var(--font-size-sm); margin-top:var(--space-1); }
</style>

<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
      <h2>Lupa Password</h2>
      <p>Masukkan email Anda untuk mendapatkan link reset password.</p>
    </div>

    <form method="POST" action="<?= BASE_URL ?>?page=forgot-password">
      <?= csrfField() ?>
      <div class="form-group">
        <label class="form-label">Alamat Email</label>
        <input type="email" name="email" class="form-input" required autofocus
               placeholder="email@contoh.com"
               value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
      </div>
      <button type="submit" class="btn btn-primary btn-block btn-lg" style="width:100%;margin-top:var(--space-2)">
        🔑 Kirim Link Reset
      </button>
    </form>

    <div style="text-align:center;margin-top:var(--space-6);font-size:var(--font-size-sm);color:var(--text-muted)">
      Ingat password? <a href="<?= BASE_URL ?>?page=login" style="color:var(--color-gold)">Login sekarang</a>
    </div>
  </div>
</div>
