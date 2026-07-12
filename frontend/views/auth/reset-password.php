<?php /** Reset Password Page */ ?>
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
      <h2>Reset Password</h2>
      <p>Masukkan password baru Anda di bawah ini.</p>
    </div>

    <?php if (!$isValidToken): ?>
    <div style="text-align:center;padding:var(--space-6);background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:var(--radius-lg)">
      <div style="font-size:2rem;margin-bottom:var(--space-3)">⌛</div>
      <p style="color:#ef4444;font-weight:600">Link reset password tidak valid atau sudah kedaluwarsa.</p>
      <a href="<?= BASE_URL ?>?page=forgot-password" class="btn btn-primary" style="margin-top:var(--space-4)">Minta Link Baru</a>
    </div>
    <?php else: ?>
    <form method="POST" action="<?= BASE_URL ?>?page=reset-password">
      <?= csrfField() ?>
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

      <div class="form-group">
        <label class="form-label">Password Baru <span style="color:var(--status-danger)">*</span></label>
        <input type="password" name="new_password" class="form-input" required
               placeholder="Minimal 8 karakter" minlength="8" autofocus>
      </div>
      <div class="form-group">
        <label class="form-label">Konfirmasi Password <span style="color:var(--status-danger)">*</span></label>
        <input type="password" name="confirm_password" class="form-input" required
               placeholder="Ulangi password baru" id="confirmPwd">
      </div>
      <div id="pwdMatch" style="font-size:var(--font-size-xs);margin-bottom:var(--space-4);display:none"></div>
      <button type="submit" class="btn btn-primary btn-block btn-lg" style="width:100%">
        ✅ Simpan Password Baru
      </button>
    </form>
    <?php endif; ?>
  </div>
</div>
<script>
document.getElementById('confirmPwd')?.addEventListener('input', function() {
  const newPwd = document.querySelector('[name=new_password]').value;
  const div = document.getElementById('pwdMatch');
  div.style.display = 'block';
  if (this.value === newPwd) { div.textContent = '✅ Password cocok'; div.style.color='#10b981'; }
  else { div.textContent = '❌ Password tidak cocok'; div.style.color='#ef4444'; }
});
</script>
