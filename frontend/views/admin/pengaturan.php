<?php /** Admin Settings */ ?>
<div class="settings-tabs">
  <span class="settings-tab active" onclick="showTabSetting('profile', this)"><?= __('settings.profile') ?></span>
  <span class="settings-tab" onclick="showTabSetting('security', this)"><?= __('settings.security') ?></span>
</div>

<div id="tab-profile" class="detail-tab-content">
  <div class="settings-card" style="max-width:600px">
    <h3 class="settings-card-title"><?= __('settings.update_profile') ?></h3>
    <form method="POST" action="<?= BASE_URL ?>?page=admin/pengaturan&action=profile">
      <?= csrfField() ?>
      <div class="form-group">
        <label class="form-label"><?= __('auth.name') ?></label>
        <input type="text" name="name" class="form-input" required value="<?= htmlspecialchars($userData['name']) ?>">
      </div>
      <div class="form-group">
        <label class="form-label"><?= __('auth.email') ?></label>
        <input type="email" name="email" class="form-input" required value="<?= htmlspecialchars($userData['email']) ?>">
      </div>
      <div class="form-group">
        <label class="form-label"><?= __('auth.phone') ?></label>
        <input type="tel" name="phone" class="form-input" value="<?= htmlspecialchars($userData['phone'] ?? '') ?>">
      </div>
      <button type="submit" class="btn btn-primary"><?= __('common.save') ?></button>
    </form>
  </div>
</div>

<div id="tab-security" class="detail-tab-content" style="display:none">
  <div class="settings-card" style="max-width:600px">
    <h3 class="settings-card-title"><?= __('settings.change_password') ?></h3>
    <form method="POST" action="<?= BASE_URL ?>?page=admin/pengaturan&action=password">
      <?= csrfField() ?>
      <div class="form-group">
        <label class="form-label"><?= __('settings.current_password') ?></label>
        <input type="password" name="current_password" class="form-input" required>
      </div>
      <div class="form-group">
        <label class="form-label"><?= __('settings.new_password') ?></label>
        <input type="password" name="new_password" class="form-input" required minlength="8">
      </div>
      <div class="form-group">
        <label class="form-label"><?= __('settings.confirm_new') ?></label>
        <input type="password" name="confirm_password" class="form-input" required>
      </div>
      <button type="submit" class="btn btn-primary"><?= __('settings.change_password') ?></button>
    </form>
  </div>
</div>

<script>
function showTabSetting(t, el){document.querySelectorAll('.detail-tab-content').forEach(e=>e.style.display='none');document.querySelectorAll('.settings-tab').forEach(e=>e.classList.remove('active'));document.getElementById('tab-'+t).style.display='block';if(el)el.classList.add('active');}
</script>
