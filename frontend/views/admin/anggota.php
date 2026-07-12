<?php /** Admin Members Management */ ?>
<div class="admin-table-container">
  <div class="admin-table-header">
    <form method="GET" action="<?= BASE_URL ?>" class="admin-table-search">
      <input type="hidden" name="page" value="admin/anggota">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" name="search" placeholder="<?= __('admin.search') ?>" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    </form>
    <div style="display:flex;gap:var(--space-3);flex-wrap:wrap">
      <select onchange="location.href='<?= BASE_URL ?>?page=admin/anggota&role='+this.value" class="form-select" style="width:auto;padding:8px 32px 8px 12px">
        <option value="">Semua Role</option>
        <option value="user"        <?= ($_GET['role']??'')==='user'        ?'selected':'' ?>>User</option>
        <option value="admin"       <?= ($_GET['role']??'')==='admin'       ?'selected':'' ?>>Admin</option>
        <option value="super_admin" <?= ($_GET['role']??'')==='super_admin' ?'selected':'' ?>>Super Admin</option>
      </select>
      <?php if (isSuperAdmin()): ?>
      <button class="btn btn-primary btn-sm" onclick="document.getElementById('addMemberModal').classList.add('active')">+ <?= __('admin.add_new') ?></button>
      <?php endif; ?>
    </div>
  </div>

  <table class="data-table">
    <thead>
      <tr>
        <th>Nama Lengkap</th>
        <th>Email</th>
        <th>Telepon</th>
        <th>Role</th>
        <th>Status</th>
        <th>Bergabung</th>
        <th><?= isSuperAdmin() ? __('admin.actions') : '' ?></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach(($users ?? []) as $u):
      $rc = match($u['role']){'super_admin'=>'danger','admin'=>'warning',default=>'info'};
    ?>
    <tr>
      <td style="font-weight:600"><?= htmlspecialchars($u['name']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td><?= htmlspecialchars($u['phone'] ?? '-') ?></td>
      <td><span class="badge badge-<?= $rc ?>"><?= strtoupper(str_replace('_',' ',$u['role'])) ?></span></td>
      <td><span class="badge badge-<?= $u['is_active']?'success':'danger' ?>"><?= $u['is_active']?'Aktif':'Nonaktif' ?></span></td>
      <td><?= formatDate($u['created_at']) ?></td>
      <td class="table-actions">
        <?php if (isSuperAdmin()): ?>
        <button class="btn btn-sm btn-outline" onclick="editMember(<?= $u['id'] ?>, <?= htmlspecialchars(json_encode($u), ENT_QUOTES) ?>)" title="Edit">✎</button>
        <?php if($u['id'] != ($_SESSION['user_id']??0)): ?>
        <a href="<?= BASE_URL ?>?page=admin/anggota&action=delete&id=<?= $u['id'] ?>"
           class="btn btn-sm btn-danger"
           onclick="return confirm('<?= __('admin.confirm_delete') ?>')" title="Hapus">✕</a>
        <?php endif; ?>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if(empty($users)): ?>
    <tr><td colspan="7" class="text-center text-muted" style="padding:var(--space-8)"><?= __('admin.no_data') ?></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Add Member Modal -->
<div class="modal-backdrop" id="addMemberModal">
  <div class="modal">
    <div class="modal-header">
      <h3>Tambah Anggota</h3>
      <button class="modal-close" onclick="this.closest('.modal-backdrop').classList.remove('active')">✕</button>
    </div>
    <form method="POST" action="<?= BASE_URL ?>?page=admin/anggota&action=store">
      <?= csrfField() ?>
      <div class="modal-body">
        <div class="form-group"><label class="form-label">Nama Lengkap</label><input type="text" name="name" class="form-input" required></div>
        <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" class="form-input" required></div>
        <div class="form-group"><label class="form-label">Telepon</label><input type="tel" name="phone" class="form-input"></div>
        <div class="form-group">
          <label class="form-label">Password (min. 8 karakter)</label>
          <input type="text" name="password" class="form-input" required minlength="8" placeholder="Minimal 8 karakter">
        </div>
        <?php if(isSuperAdmin()): ?>
        <div class="form-group"><label class="form-label">Role Akses</label>
          <select name="role" class="form-select">
            <option value="user">User Biasa</option>
            <option value="admin">Admin</option>
            <option value="super_admin">Super Admin</option>
          </select>
        </div>
        <?php else: ?>
        <input type="hidden" name="role" value="user">
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="this.closest('.modal-backdrop').classList.remove('active')"><?= __('common.cancel') ?></button>
        <button type="submit" class="btn btn-primary"><?= __('common.save') ?></button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Member Modal -->
<div class="modal-backdrop" id="editMemberModal">
  <div class="modal">
    <div class="modal-header">
      <h3>Edit Pengguna</h3>
      <button class="modal-close" onclick="this.closest('.modal-backdrop').classList.remove('active')">✕</button>
    </div>
    <form method="POST" id="editMemberForm" action="">
      <?= csrfField() ?>
      <div class="modal-body">
        <div class="form-group"><label class="form-label">Nama Lengkap</label><input type="text" name="name" id="editMemberName" class="form-input" required></div>
        <div class="form-group"><label class="form-label">Email</label><input type="email" name="email" id="editMemberEmail" class="form-input" required></div>
        <div class="form-group"><label class="form-label">Telepon</label><input type="tel" name="phone" id="editMemberPhone" class="form-input"></div>
        <div class="form-group">
          <label class="form-label">Password Baru (kosongkan jika tidak ingin mengubah)</label>
          <input type="text" name="password" class="form-input" minlength="8" placeholder="Kosongkan jika tidak diubah">
        </div>
        <?php if(isSuperAdmin()): ?>
        <div class="form-group">
          <label class="form-label">Role Akses</label>
          <select name="role" id="editMemberRole" class="form-select">
            <option value="user">User Biasa</option>
            <option value="admin">Admin</option>
            <option value="super_admin">Super Admin</option>
          </select>
        </div>
        <?php endif; ?>
        <div class="form-group" style="display:flex;gap:10px;align-items:center">
          <input type="checkbox" name="is_active" id="editMemberActive" value="1">
          <label for="editMemberActive">Akun Aktif</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="this.closest('.modal-backdrop').classList.remove('active')"><?= __('common.cancel') ?></button>
        <button type="submit" class="btn btn-primary"><?= __('common.save') ?></button>
      </div>
    </form>
  </div>
</div>

<script>
const BASE_URL_ADMIN = '<?= BASE_URL ?>';
function editMember(id, data) {
    document.getElementById('editMemberName').value  = data.name  || '';
    document.getElementById('editMemberEmail').value = data.email || '';
    document.getElementById('editMemberPhone').value = data.phone || '';
    document.getElementById('editMemberActive').checked = data.is_active == 1;
    const roleEl = document.getElementById('editMemberRole');
    if (roleEl) roleEl.value = data.role || 'user';
    document.getElementById('editMemberForm').action = BASE_URL_ADMIN + '?page=admin/anggota&action=update&id=' + id;
    document.getElementById('editMemberModal').classList.add('active');
}
</script>
