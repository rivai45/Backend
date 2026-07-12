<?php
/** Profil Saya Page */
$avatarUrl = null;
if (!empty($user['avatar'])) {
    $avatarUrl = str_starts_with($user['avatar'], 'http') ? $user['avatar'] : BASE_URL . $user['avatar'];
}
$initials = strtoupper(substr($user['name'] ?? 'U', 0, 1));
?>

<style>
.profil-layout { display:grid; grid-template-columns:300px 1fr; gap:var(--space-6); max-width:1000px; margin:0 auto; padding:var(--space-8) var(--space-4) var(--space-12); }
@media(max-width:768px){ .profil-layout { grid-template-columns:1fr; } }
.profil-card { background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--radius-xl); padding:var(--space-6); margin-bottom:var(--space-4); }
.profil-card h3 { font-size:var(--font-size-base); font-weight:600; color:var(--color-gold); margin-bottom:var(--space-4); padding-bottom:var(--space-3); border-bottom:1px solid var(--border-color); }
.avatar-wrap { text-align:center; margin-bottom:var(--space-5); }
.avatar-circle { width:100px; height:100px; border-radius:50%; background:linear-gradient(135deg, var(--color-gold), #8b6914); display:flex; align-items:center; justify-content:center; font-size:2.5rem; font-weight:700; color:#1a1200; margin:0 auto var(--space-3); border:3px solid var(--color-gold); overflow:hidden; }
.avatar-circle img { width:100%; height:100%; object-fit:cover; }
.avatar-name { font-size:var(--font-size-lg); font-weight:700; }
.avatar-role { font-size:var(--font-size-xs); color:var(--text-muted); margin-top:4px; }
.tab-buttons { display:flex; gap:var(--space-2); margin-bottom:var(--space-6); border-bottom:1px solid var(--border-color); }
.tab-btn { padding:var(--space-3) var(--space-4); font-size:var(--font-size-sm); font-weight:600; background:none; border:none; color:var(--text-muted); cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1px; transition:all .2s; }
.tab-btn.active { color:var(--color-gold); border-bottom-color:var(--color-gold); }
.tab-pane { display:none; }
.tab-pane.active { display:block; }
.stat-mini { text-align:center; padding:var(--space-4); background:var(--bg-secondary); border-radius:var(--radius-lg); }
.stat-mini .num { font-size:var(--font-size-2xl); font-weight:700; color:var(--color-gold); }
.stat-mini .lbl { font-size:var(--font-size-xs); color:var(--text-muted); margin-top:4px; }
</style>

<div class="profil-layout">
  <!-- LEFT: Avatar + Stats -->
  <div>
    <div class="profil-card" style="text-align:center">
      <div class="avatar-wrap">
        <div class="avatar-circle" id="avatarPreviewWrap">
          <?php if ($avatarUrl): ?>
          <img src="<?= htmlspecialchars($avatarUrl) ?>" id="avatarImg" alt="Avatar">
          <?php else: ?>
          <span id="avatarInitial"><?= $initials ?></span>
          <?php endif; ?>
        </div>
        <div class="avatar-name"><?= htmlspecialchars($user['name']) ?></div>
        <div class="avatar-role">
          <?php
          $roleLabel = match($user['role']) {
              'super_admin' => '⚡ Super Admin', 'admin' => '🛡️ Admin', default => '👤 Member'
          };
          echo $roleLabel;
          ?>
        </div>
        <div style="font-size:var(--font-size-xs);color:var(--text-muted);margin-top:4px">
          Bergabung sejak <?= formatDate($user['created_at']) ?>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <?php
    $db = db();
    $stmtCount = $db->prepare("SELECT COUNT(*) as total, SUM(total_price) as total_spent FROM bookings WHERE user_id = ? AND status IN ('confirmed','completed','checked_in')");
    $stmtCount->execute([$user['id']]);
    $stats = $stmtCount->fetch();
    $stmtCompleted = $db->prepare("SELECT COUNT(*) as total FROM bookings WHERE user_id = ? AND status = 'completed'");
    $stmtCompleted->execute([$user['id']]);
    $completed = $stmtCompleted->fetch()['total'];
    ?>
    <div class="profil-card">
      <h3>📊 Statistik</h3>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3)">
        <div class="stat-mini"><div class="num"><?= $stats['total'] ?? 0 ?></div><div class="lbl">Booking</div></div>
        <div class="stat-mini"><div class="num"><?= $completed ?></div><div class="lbl">Selesai</div></div>
      </div>
      <div style="margin-top:var(--space-3)">
        <div class="stat-mini" style="background:rgba(212,163,115,0.08);border:1px solid rgba(212,163,115,0.2)">
          <div class="num" style="font-size:var(--font-size-lg)"><?= formatCurrency($stats['total_spent'] ?? 0) ?></div>
          <div class="lbl">Total Pengeluaran</div>
        </div>
      </div>
    </div>

    <a href="<?= BASE_URL ?>?page=my-bookings" class="btn btn-outline" style="width:100%">📋 Lihat Semua Booking</a>
  </div>

  <!-- RIGHT: Form -->
  <div>
    <div class="profil-card">
      <!-- Tabs -->
      <div class="tab-buttons">
        <button class="tab-btn active" onclick="switchTab('profil', this)">👤 Edit Profil</button>
        <button class="tab-btn" onclick="switchTab('password', this)">🔒 Ganti Password</button>
      </div>

      <!-- Tab: Edit Profil -->
      <div id="tab-profil" class="tab-pane active">
        <form method="POST" action="<?= BASE_URL ?>?page=profil&action=profile" enctype="multipart/form-data">
          <?= csrfField() ?>
          <div class="form-group">
            <label class="form-label">Foto Profil (Avatar)</label>
            <div style="display:flex;align-items:center;gap:var(--space-4)">
              <div class="avatar-circle" style="width:64px;height:64px;font-size:1.5rem;flex-shrink:0">
                <?php if ($avatarUrl): ?>
                <img src="<?= htmlspecialchars($avatarUrl) ?>" id="smallAvatarImg" alt="Avatar">
                <?php else: ?>
                <span id="smallAvatarInitial"><?= $initials ?></span>
                <?php endif; ?>
              </div>
              <div>
                <input type="file" name="avatar" id="avatarFile" accept="image/jpeg,image/png,image/webp" style="display:none">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('avatarFile').click()">📷 Pilih Foto</button>
                <p style="font-size:var(--font-size-xs);color:var(--text-muted);margin-top:6px">JPG, PNG, WebP — Maks. 5MB</p>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Nama Lengkap <span style="color:var(--status-danger)">*</span></label>
            <input type="text" name="name" class="form-input" required value="<?= htmlspecialchars($user['name']) ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" class="form-input" value="<?= htmlspecialchars($user['email']) ?>" disabled style="opacity:.6;cursor:not-allowed">
            <small style="color:var(--text-muted)">Email tidak dapat diubah.</small>
          </div>
          <div class="form-group">
            <label class="form-label">Nomor Telepon</label>
            <input type="tel" name="phone" class="form-input" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="08123456789">
          </div>
          <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
        </form>
      </div>

      <!-- Tab: Ganti Password -->
      <div id="tab-password" class="tab-pane">
        <form method="POST" action="<?= BASE_URL ?>?page=profil&action=password">
          <?= csrfField() ?>
          <div class="form-group">
            <label class="form-label">Password Saat Ini <span style="color:var(--status-danger)">*</span></label>
            <input type="password" name="current_password" class="form-input" required placeholder="Masukkan password saat ini">
          </div>
          <div class="form-group">
            <label class="form-label">Password Baru <span style="color:var(--status-danger)">*</span></label>
            <input type="password" name="new_password" class="form-input" required placeholder="Minimal 8 karakter" minlength="8">
          </div>
          <div class="form-group">
            <label class="form-label">Konfirmasi Password Baru <span style="color:var(--status-danger)">*</span></label>
            <input type="password" name="confirm_password" class="form-input" required placeholder="Ulangi password baru" id="confirmPwd">
          </div>
          <div id="pwdMatch" style="font-size:var(--font-size-xs);margin-bottom:var(--space-4);display:none"></div>
          <button type="submit" class="btn btn-primary">🔒 Ubah Password</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
function switchTab(tab, btn) {
  document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + tab).classList.add('active');
  btn.classList.add('active');
}

// Avatar preview
document.getElementById('avatarFile')?.addEventListener('change', function() {
  if (this.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      ['avatarImg','smallAvatarImg'].forEach(id => {
        let el = document.getElementById(id);
        if (!el) {
          el = document.createElement('img');
          el.id = id;
          const wrap = id === 'avatarImg' ? document.getElementById('avatarPreviewWrap') : document.querySelector('#tab-profil .avatar-circle');
          wrap.innerHTML = '';
          wrap.appendChild(el);
        }
        el.src = e.target.result;
        el.style.cssText = 'width:100%;height:100%;object-fit:cover';
      });
    };
    reader.readAsDataURL(this.files[0]);
  }
});

// Password match indicator
document.getElementById('confirmPwd')?.addEventListener('input', function() {
  const newPwd = document.querySelector('[name=new_password]').value;
  const div = document.getElementById('pwdMatch');
  div.style.display = 'block';
  if (this.value === newPwd) {
    div.textContent = '✅ Password cocok';
    div.style.color = '#10b981';
  } else {
    div.textContent = '❌ Password tidak cocok';
    div.style.color = '#ef4444';
  }
});
</script>
