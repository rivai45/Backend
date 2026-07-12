<?php /** Register Page */ ?>
<div class="auth-page">
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-logo">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
        <?= APP_NAME ?>
      </div>
      <h2 class="auth-title"><?= __('auth.register_title') ?></h2>
      <p class="auth-subtitle"><?= __('auth.register_subtitle') ?></p>
      <form method="POST" action="<?= BASE_URL ?>?page=register" id="registerForm">
        <?= csrfField() ?>
        <div class="form-group">
          <label class="form-label"><?= __('auth.name') ?></label>
          <input type="text" name="name" class="form-input" required placeholder="Nama Lengkap" autocomplete="name">
        </div>
        <div class="form-group">
          <label class="form-label"><?= __('auth.email') ?></label>
          <input type="email" name="email" class="form-input" required placeholder="email@example.com" autocomplete="email">
        </div>
        <div class="form-group">
          <label class="form-label"><?= __('auth.phone') ?></label>
          <input type="tel" name="phone" class="form-input" placeholder="08xxxxxxxxxx" autocomplete="tel">
        </div>
        <div class="form-group">
          <label class="form-label"><?= __('auth.password') ?></label>
          <div style="position:relative">
            <input type="password" name="password" class="form-input" required minlength="8" id="regPassword" placeholder="Minimal 8 karakter" autocomplete="new-password">
            <span onclick="togglePwd('regPassword')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--text-muted);font-size:14px" title="Tampilkan password">👁</span>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label"><?= __('auth.confirm_password') ?></label>
          <div style="position:relative">
            <input type="password" name="confirm_password" class="form-input" required id="regConfirm" placeholder="Ulangi password" autocomplete="new-password">
            <span onclick="togglePwd('regConfirm')" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--text-muted);font-size:14px" title="Tampilkan password">👁</span>
          </div>
          <small id="pwdMatch" style="color:var(--status-danger);display:none;margin-top:4px">Password tidak cocok</small>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:var(--space-4)"><?= __('auth.register_btn') ?></button>
      </form>
      <p class="auth-footer"><?= __('auth.has_account') ?> <a href="<?= BASE_URL ?>?page=login"><?= __('auth.login_btn') ?></a></p>
    </div>
  </div>
</div>
<script>
function togglePwd(id){const el=document.getElementById(id);el.type=el.type==='password'?'text':'password';}
const p=document.getElementById('regPassword');
const c=document.getElementById('regConfirm');
const m=document.getElementById('pwdMatch');
c.addEventListener('input',()=>{m.style.display=p.value!==c.value?'block':'none';});
document.getElementById('registerForm').addEventListener('submit',function(e){
  if(p.value!==c.value){e.preventDefault();m.style.display='block';c.focus();}
});
</script>
