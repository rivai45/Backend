<?php /** Admin Properties Management (Hotels & Rooms) */ ?>
<div class="settings-tabs">
  <span class="settings-tab active" onclick="showTabProp('hotels', this)">Daftar Hotel</span>
  <span class="settings-tab" onclick="showTabProp('rooms', this)">Daftar Kamar</span>
</div>

<!-- Tab Hotels -->
<div id="tab-hotels" class="detail-tab-content">
  <div class="admin-table-container">
    <div class="admin-table-header">
      <form method="GET" action="<?= BASE_URL ?>" class="admin-table-search">
        <input type="hidden" name="page" value="admin/properti">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" name="search" placeholder="<?= __('admin.search') ?>" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
      </form>
      <?php if (isSuperAdmin()): ?>
      <button class="btn btn-primary btn-sm" onclick="document.getElementById('addHotelModal').classList.add('active')">+ Tambah Hotel</button>
      <?php endif; ?>
    </div>
    <table class="data-table">
      <thead><tr><th>ID</th><th>Nama Hotel</th><th>Kota</th><th>Rating</th><th>Status</th><th><?= isSuperAdmin() ? __('admin.actions') : '' ?></th></tr></thead>
      <tbody>
      <?php foreach(($hotels ?? []) as $h): ?>
      <tr>
        <td><?= $h['id'] ?></td>
        <td style="font-weight:600"><?= htmlspecialchars($h['name']) ?></td>
        <td><?= htmlspecialchars($h['city']) ?></td>
        <td><?= $h['rating'] ?> ★</td>
        <td><span class="badge badge-<?= $h['is_active']?'success':'danger' ?>"><?= $h['is_active']?'Aktif':'Nonaktif' ?></span></td>
        <td class="table-actions">
          <?php if (isSuperAdmin()): ?>
          <a href="<?= BASE_URL ?>?page=admin/properti&action=delete&type=hotel&id=<?= $h['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?= __('admin.confirm_delete') ?>')">✕</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($hotels)): ?><tr><td colspan="6" class="text-center text-muted" style="padding:var(--space-8)"><?= __('admin.no_data') ?></td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Tab Rooms -->
<div id="tab-rooms" class="detail-tab-content" style="display:none">
  <div class="admin-table-container">
    <div class="admin-table-header">
      <h3>Manajemen Kamar</h3>
      <?php if (isSuperAdmin()): ?>
      <button class="btn btn-primary btn-sm" onclick="document.getElementById('addRoomModal').classList.add('active')">+ Tambah Kamar</button>
      <?php endif; ?>
    </div>
    <table class="data-table">
      <thead><tr><th>ID</th><th>Hotel</th><th>Tipe Kamar</th><th>Harga/Malam</th><th>Kapasitas</th><th>Stock</th><th><?= isSuperAdmin() ? __('admin.actions') : '' ?></th></tr></thead>
      <tbody>
      <?php foreach(($rooms ?? []) as $r): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['hotel_name']) ?></td>
        <td style="font-weight:600"><?= htmlspecialchars($r['type_name']) ?></td>
        <td style="color:var(--color-gold)"><?= formatCurrency($r['price_per_night']) ?></td>
        <td><?= $r['capacity'] ?> org</td>
        <td><?= $r['stock'] ?></td>
        <td class="table-actions">
          <?php if (isSuperAdmin()): ?>
          <a href="<?= BASE_URL ?>?page=admin/properti&action=delete&type=room&id=<?= $r['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('<?= __('admin.confirm_delete') ?>')">✕</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if(empty($rooms)): ?><tr><td colspan="7" class="text-center text-muted" style="padding:var(--space-8)"><?= __('admin.no_data') ?></td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modals -->
<div class="modal-backdrop" id="addHotelModal">
  <div class="modal">
    <div class="modal-header"><h3>Tambah Hotel</h3><button class="modal-close" onclick="this.closest('.modal-backdrop').classList.remove('active')">✕</button></div>
    <form method="POST" action="<?= BASE_URL ?>?page=admin/properti&action=store">
      <?= csrfField() ?>
      <input type="hidden" name="type" value="hotel">
      <div class="modal-body">
        <div class="form-group"><label class="form-label">Nama Hotel</label><input type="text" name="name" class="form-input" required></div>
        <div class="form-row"><div class="form-group"><label class="form-label">Kota</label><input type="text" name="city" class="form-input" required></div><div class="form-group"><label class="form-label">Harga Mulai Dari</label><input type="number" name="price_start" class="form-input" required></div></div>
        <div class="form-group"><label class="form-label">Alamat Lengkap</label><textarea name="address" class="form-textarea" required></textarea></div>
        <div class="form-group"><label class="form-label">Deskripsi (ID)</label><textarea name="description_id" class="form-textarea" required></textarea></div>
        <div class="form-group" style="display:flex;gap:10px;align-items:center"><input type="checkbox" name="is_featured" id="is_feat"><label for="is_feat">Jadikan Hotel Unggulan (Featured)</label></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-ghost" onclick="this.closest('.modal-backdrop').classList.remove('active')"><?= __('common.cancel') ?></button><button type="submit" class="btn btn-primary"><?= __('common.save') ?></button></div>
    </form>
  </div>
</div>

<div class="modal-backdrop" id="addRoomModal">
  <div class="modal">
    <div class="modal-header"><h3>Tambah Kamar</h3><button class="modal-close" onclick="this.closest('.modal-backdrop').classList.remove('active')">✕</button></div>
    <form method="POST" action="<?= BASE_URL ?>?page=admin/properti&action=store">
      <?= csrfField() ?>
      <input type="hidden" name="type" value="room">
      <div class="modal-body">
        <div class="form-group"><label class="form-label">Pilih Hotel</label><select name="hotel_id" class="form-select" required><?php foreach(($hotels??[]) as $h): ?><option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['name']) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label class="form-label">Tipe Kamar</label><input type="text" name="type_name" class="form-input" required placeholder="e.g. Deluxe Room"></div>
        <div class="form-group"><label class="form-label">Harga per Malam</label><input type="number" name="price_per_night" class="form-input" required></div>
        <div class="form-row"><div class="form-group"><label class="form-label">Kapasitas (Orang)</label><input type="number" name="capacity" class="form-input" value="2" required></div><div class="form-group"><label class="form-label">Jumlah Stok Kamar</label><input type="number" name="stock" class="form-input" value="1" required></div></div>
      </div>
      <div class="modal-footer"><button type="button" class="btn btn-ghost" onclick="this.closest('.modal-backdrop').classList.remove('active')"><?= __('common.cancel') ?></button><button type="submit" class="btn btn-primary"><?= __('common.save') ?></button></div>
    </form>
  </div>
</div>

<script>
function showTabProp(t, el){document.querySelectorAll('.detail-tab-content').forEach(e=>e.style.display='none');document.querySelectorAll('.settings-tab').forEach(e=>e.classList.remove('active'));document.getElementById('tab-'+t).style.display='block';if(el)el.classList.add('active');}
</script>
