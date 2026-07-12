<?php /** Login Page */ ?>
<div class="auth-page">
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-logo">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
        <?= APP_NAME ?>
      </div>
      <h2 class="auth-title"><?= __('auth.login_title') ?></h2>
      <p class="auth-subtitle"><?= __('auth.login_subtitle') ?></p>
      <form method="POST" action="<?= BASE_URL ?>?page=login">
        <?= csrfField() ?>
        <div class="form-group">
          <label class="form-label"><?= __('auth.email') ?></label>
          <input type="email" name="email" class="form-input" required placeholder="email@example.com" autocomplete="email">
        </div>
        <div class="form-group">
          <label class="form-label"><?= __('auth.password') ?></label>
          <div style="position:relative">
            <input type="password" name="password" class="form-input" required placeholder="••••••••" id="loginPassword" autocomplete="current-password">
            <span onclick="togglePwd('loginPassword')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--text-muted);font-size:14px" title="Tampilkan password">👁</span>
          </div>
        </div>
        <div style="text-align:right;margin-bottom:var(--space-2)">
          <a href="<?= BASE_URL ?>?page=forgot-password" style="font-size:var(--font-size-sm);color:var(--color-gold)">Lupa Password?</a>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:var(--space-2)"><?= __('auth.login_btn') ?></button>
      </form>
      <p class="auth-footer"><?= __('auth.no_account') ?> <a href="<?= BASE_URL ?>?page=register"><?= __('auth.register_btn') ?></a></p>
    </div>
  </div>
</div>
<script>
function togglePwd(id){const el=document.getElementById(id);el.type=el.type==='password'?'text':'password';}
</script>
