<?php /** Admin Login Page */ ?>
<div class="auth-page">
  <div class="auth-container">
    <div class="auth-card" style="border: 1px solid var(--color-gold); box-shadow: 0 0 20px rgba(212,163,115,0.1);">
      <div style="width:80px;height:80px;border-radius:50%;background:var(--color-gold-light);display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-6);border:2px solid var(--color-gold)">
          <svg viewBox="0 0 24 24" fill="currentColor" style="width:40px;height:40px;color:var(--color-gold)"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
      </div>
      <h2 class="auth-title" style="color:var(--color-gold); text-align:center;"><?= APP_NAME ?></h2>
      <p class="auth-subtitle" style="text-align:center; margin-bottom: var(--space-6);"><?= __('auth.admin_subtitle') ?></p>
      
      <?php $flash=getFlash();if($flash): ?><div class="flash-message flash-<?= $flash['type'] ?>" style="position:relative;top:auto;right:auto;margin-bottom:var(--space-4)"><?= $flash['message'] ?></div><?php endif; ?>
      
      <form method="POST" action="<?= BASE_URL ?>/?page=admin/login">
        <?= csrfField() ?>
        <div class="form-group">
            <label class="form-label"><?= __('auth.email') ?></label>
            <input type="email" name="email" class="form-input" required placeholder="admin@lynvaii.com">
        </div>
        <div class="form-group">
            <label class="form-label"><?= __('auth.password') ?></label>
            <input type="password" name="password" class="form-input" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:var(--space-6)"><?= __('auth.login_btn') ?></button>
      </form>
    </div>
  </div>
</div>
