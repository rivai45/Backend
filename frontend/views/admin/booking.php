<?php /** Admin Booking Management */ ?>
<div class="admin-table-container">
  <div class="admin-table-header">
    <form method="GET" action="<?= BASE_URL ?>" class="admin-table-search">
      <input type="hidden" name="page" value="admin/booking">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" name="search" placeholder="<?= __('admin.search') ?>" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    </form>
    <div style="display:flex;gap:var(--space-3);flex-wrap:wrap">
      <select onchange="location.href='<?= BASE_URL ?>?page=admin/booking&status='+this.value" class="form-select" style="width:auto;padding:8px 32px 8px 12px">
        <option value="">Semua Status</option>
        <?php foreach(['pending','confirmed','checked_in','completed','cancelled','expired'] as $st): ?>
        <option value="<?= $st ?>" <?= ($_GET['status']??'')===$st?'selected':'' ?>><?= __('status.'.$st) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-primary btn-sm" onclick="document.getElementById('addBookingModal').classList.add('active')">+ <?= __('admin.add_new') ?></button>
    </div>
  </div>

  <table class="data-table">
    <thead>
      <tr>
        <th><?= __('admin.booking_id') ?></th>
        <th><?= __('admin.guest_name') ?></th>
        <th><?= __('admin.hotel_name') ?></th>
        <th><?= __('admin.check_in') ?></th>
        <th>Malam</th>
        <th><?= __('admin.amount') ?></th>
        <th>Bukti Bayar</th>
        <th><?= __('admin.status') ?></th>
        <th><?= __('admin.actions') ?></th>
      </tr>
    </thead>
    <tbody>
    <?php
    // Ambil semua payment proof untuk booking yang tampil
    $bookingIds = array_column($bookings ?? [], 'id');
    $paymentProofs = [];
    if (!empty($bookingIds)) {
        $placeholders = implode(',', array_fill(0, count($bookingIds), '?'));
        $proofStmt = db()->prepare("SELECT booking_id, payment_proof, status FROM payments WHERE booking_id IN ($placeholders) ORDER BY created_at DESC");
        $proofStmt->execute($bookingIds);
        foreach ($proofStmt->fetchAll() as $pp) {
            if (!isset($paymentProofs[$pp['booking_id']])) {
                $paymentProofs[$pp['booking_id']] = $pp;
            }
        }
    }
    ?>
    <?php foreach(($bookings ?? []) as $b):
      $sc = match($b['status']){
          'confirmed'  => 'success',
          'pending'    => 'warning',
          'cancelled'  => 'danger',
          'expired'    => 'danger',
          'completed'  => 'info',
          'checked_in' => 'success',
          default      => 'warning'
      };
      $proof = $paymentProofs[$b['id']] ?? null;
    ?>
    <tr>
      <td style="font-weight:600;color:var(--color-gold)"><?= $b['booking_code'] ?></td>
      <td>
        <div><?= htmlspecialchars($b['guest_name']) ?></div>
        <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars($b['guest_email']) ?></div>
      </td>
      <td>
        <div><?= htmlspecialchars($b['hotel_name']) ?></div>
        <div style="font-size:11px;color:var(--text-muted)"><?= htmlspecialchars($b['room_type'] ?? '') ?></div>
      </td>
      <td><?= formatDate($b['check_in']) ?></td>
      <td style="text-align:center"><?= $b['nights'] ?? '-' ?> mlm</td>
      <td style="font-weight:600"><?= formatCurrency($b['total_price']) ?></td>
      <td style="text-align:center">
        <?php if ($proof && $proof['payment_proof']): ?>
          <button class="btn btn-sm btn-outline" style="color:var(--status-success)" title="Lihat Bukti"
            onclick="showProof('<?= BASE_URL . $proof['payment_proof'] ?>', '<?= $b['booking_code'] ?>', '<?= formatCurrency($b['total_price']) ?>', <?= $b['id'] ?>)">
            🖼 Lihat
          </button>
        <?php elseif($b['status'] === 'pending'): ?>
          <span style="font-size:11px;color:var(--text-muted)">Belum upload</span>
        <?php else: ?>
          <span style="font-size:11px;color:var(--text-muted)">—</span>
        <?php endif; ?>
      </td>
      <td><span class="badge badge-<?= $sc ?>"><?= __('status.'.$b['status']) ?></span></td>
      <td class="table-actions">
        <?php if(in_array($b['status'],['pending'])): ?>
        <a href="<?= BASE_URL ?>?page=admin/booking&action=status&id=<?= $b['id'] ?>&status=confirmed"
           class="btn btn-sm btn-outline" title="Konfirmasi" style="color:var(--status-success)"
           onclick="return confirm('Konfirmasi booking <?= $b['booking_code'] ?>?')">✓</a>
        <?php endif; ?>
        <?php if(in_array($b['status'],['confirmed','checked_in'])): ?>
        <a href="<?= BASE_URL ?>?page=admin/booking&action=status&id=<?= $b['id'] ?>&status=completed"
           class="btn btn-sm btn-outline" title="Selesai" style="color:var(--status-info)"
           onclick="return confirm('Tandai booking ini sebagai Selesai?')">✔</a>
        <?php endif; ?>
        <button class="btn btn-sm btn-outline" onclick="editBooking(<?= $b['id'] ?>)" title="Edit">✎</button>
        <a href="<?= BASE_URL ?>?page=admin/booking&action=delete&id=<?= $b['id'] ?>"
           class="btn btn-sm btn-danger"
           onclick="return confirm('<?= __('admin.confirm_delete') ?>')" title="Hapus">✕</a>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if(empty($bookings)): ?>
    <tr><td colspan="8" class="text-center text-muted" style="padding:var(--space-8)"><?= __('admin.no_data') ?></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if(($totalPages??1) > 1): ?>
<div class="pagination">
  <?php for($p=1;$p<=$totalPages;$p++): ?>
  <a href="<?= BASE_URL ?>?page=admin/booking&p=<?= $p ?>&search=<?= urlencode($_GET['search']??'') ?>&status=<?= urlencode($_GET['status']??'') ?>"
     class="<?= $p==($_GET['p']??1)?'active':'' ?>"><?= $p ?></a>
  <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Add Booking Modal -->
<div class="modal-backdrop" id="addBookingModal">
  <div class="modal">
    <div class="modal-header">
      <h3>Tambah Booking</h3>
      <button class="modal-close" onclick="this.closest('.modal-backdrop').classList.remove('active')">✕</button>
    </div>
    <form method="POST" action="<?= BASE_URL ?>?page=admin/booking&action=store">
      <?= csrfField() ?>
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Pilih Pengguna</label>
          <select name="user_id" class="form-select" required>
            <option value="">— Pilih User —</option>
            <?php foreach(($users ?? []) as $u): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['name']) ?> (<?= htmlspecialchars($u['email']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Pilih Hotel & Kamar</label>
          <select name="hotel_id" class="form-select" id="modalHotelSelect" onchange="loadModalRooms(this.value)">
            <option value="">— Pilih Hotel —</option>
            <?php foreach(($hotels ?? []) as $h): ?>
            <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Kamar</label>
          <select name="room_id" class="form-select" id="modalRoomSelect" required>
            <option value="">Pilih hotel dulu</option>
          </select>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Check-In</label><input type="date" name="check_in" class="form-input" required min="<?= date('Y-m-d') ?>" id="modalCheckIn"></div>
          <div class="form-group"><label class="form-label">Check-Out</label><input type="date" name="check_out" class="form-input" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" id="modalCheckOut"></div>
        </div>
        <div class="form-group"><label class="form-label">Jumlah Tamu</label><input type="number" name="guests" class="form-input" value="2" min="1" max="20"></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Nama Tamu</label><input type="text" name="guest_name" class="form-input" required></div>
          <div class="form-group"><label class="form-label">Email Tamu</label><input type="email" name="guest_email" class="form-input" required></div>
        </div>
        <div class="form-group"><label class="form-label">No. Telepon</label><input type="tel" name="guest_phone" class="form-input"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-ghost" onclick="this.closest('.modal-backdrop').classList.remove('active')"><?= __('common.cancel') ?></button>
        <button type="submit" class="btn btn-primary"><?= __('common.save') ?></button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Booking Modal -->
<div class="modal-backdrop" id="editBookingModal">
  <div class="modal">
    <div class="modal-header">
      <h3>Edit Booking</h3>
      <button class="modal-close" onclick="this.closest('.modal-backdrop').classList.remove('active')">✕</button>
    </div>
    <form method="POST" id="editBookingForm" action="">
      <?= csrfField() ?>
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group"><label class="form-label">Nama Tamu</label><input type="text" name="guest_name" id="editGuestName" class="form-input" required></div>
          <div class="form-group"><label class="form-label">Email</label><input type="email" name="guest_email" id="editGuestEmail" class="form-input" required></div>
        </div>
        <div class="form-group"><label class="form-label">No. Telepon</label><input type="tel" name="guest_phone" id="editGuestPhone" class="form-input"></div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Check-In</label><input type="date" name="check_in" id="editCheckIn" class="form-input" required></div>
          <div class="form-group"><label class="form-label">Check-Out</label><input type="date" name="check_out" id="editCheckOut" class="form-input" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label class="form-label">Jumlah Tamu</label><input type="number" name="guests" id="editGuests" class="form-input" min="1" max="20"></div>
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" id="editStatus" class="form-select">
              <?php foreach(['pending','confirmed','checked_in','completed','cancelled'] as $st): ?>
              <option value="<?= $st ?>"><?= __('status.'.$st) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
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
const BASE = '<?= BASE_URL ?>';

function editBooking(id) {
    fetch(BASE + '?page=admin/booking&action=edit&id=' + id)
        .then(r => r.json())
        .then(d => {
            if (!d.success) { alert('Gagal memuat data booking.'); return; }
            const b = d.data;
            document.getElementById('editGuestName').value  = b.guest_name  || '';
            document.getElementById('editGuestEmail').value = b.guest_email || '';
            document.getElementById('editGuestPhone').value = b.guest_phone || '';
            document.getElementById('editCheckIn').value    = b.check_in    || '';
            document.getElementById('editCheckOut').value   = b.check_out   || '';
            document.getElementById('editGuests').value     = b.guests      || 1;
            document.getElementById('editStatus').value     = b.status      || 'pending';
            document.getElementById('editBookingForm').action = BASE + '?page=admin/booking&action=update&id=' + id;
            document.getElementById('editBookingModal').classList.add('active');
        })
        .catch(() => alert('Gagal memuat data.'));
}

function loadModalRooms(hotelId) {
    if (!hotelId) return;
    fetch(BASE + '?page=api/rooms&hotel_id=' + hotelId)
        .then(r => r.json())
        .then(d => {
            const s = document.getElementById('modalRoomSelect');
            s.innerHTML = '';
            (d.data || []).forEach(r => {
                const o = document.createElement('option');
                o.value = r.id;
                o.textContent = r.type_name + ' — Rp ' + Number(r.price_per_night).toLocaleString('id-ID');
                s.appendChild(o);
            });
        });
}

// Update modal check-out min date
document.getElementById('modalCheckIn').addEventListener('change', function() {
    if (this.value) {
        const d = new Date(this.value);
        d.setDate(d.getDate() + 1);
        const coEl = document.getElementById('modalCheckOut');
        coEl.min = d.toISOString().split('T')[0];
        if (coEl.value <= this.value) coEl.value = coEl.min;
    }
});

function showProof(imgUrl, code, amount, bookingId) {
    document.getElementById('proofImg').src = imgUrl;
    document.getElementById('proofImgLink').href = imgUrl;
    document.getElementById('proofCode').textContent = code;
    document.getElementById('proofAmount').textContent = amount;
    document.getElementById('proofConfirmBtn').href = BASE + '?page=admin/booking&action=status&id=' + bookingId + '&status=confirmed';
    document.getElementById('proofModal').classList.add('active');
}
document.getElementById('proofModal')?.addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('active');
});
</script>

<!-- Modal Preview Bukti Bayar -->
<div class="modal-backdrop" id="proofModal">
  <div class="modal" style="max-width:540px">
    <div class="modal-header">
      <h3>🖼 Bukti Transfer</h3>
      <button class="modal-close" onclick="this.closest('.modal-backdrop').classList.remove('active')">✕</button>
    </div>
    <div class="modal-body">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-4);padding:var(--space-3);background:var(--bg-secondary);border-radius:var(--radius-md)">
        <div>
          <div style="font-size:var(--font-size-xs);color:var(--text-muted)">Kode Booking</div>
          <div style="font-weight:700;color:var(--color-gold)" id="proofCode">-</div>
        </div>
        <div style="text-align:right">
          <div style="font-size:var(--font-size-xs);color:var(--text-muted)">Total</div>
          <div style="font-weight:700" id="proofAmount">-</div>
        </div>
      </div>
      <a href="#" id="proofImgLink" target="_blank">
        <img id="proofImg" src="" alt="Bukti Transfer"
             style="width:100%;max-height:380px;object-fit:contain;border-radius:var(--radius-md);border:1px solid var(--border-color);background:var(--bg-secondary);cursor:zoom-in">
      </a>
      <p style="font-size:var(--font-size-xs);color:var(--text-muted);margin-top:var(--space-2);text-align:center">Klik gambar untuk membuka full size</p>
    </div>
    <div class="modal-footer" style="gap:var(--space-3)">
      <button class="btn btn-ghost" onclick="this.closest('.modal-backdrop').classList.remove('active')">Tutup</button>
      <a href="#" id="proofConfirmBtn" class="btn btn-primary"
         onclick="return confirm('Konfirmasi pembayaran dan ubah status ke Terkonfirmasi?')">
        ✅ Konfirmasi Pembayaran
      </a>
    </div>
  </div>
</div>
