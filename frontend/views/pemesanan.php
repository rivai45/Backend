<?php /** Pemesanan (Booking) Page */ ?>
<div class="pemesanan-page">
  <div class="container">
    <h1 style="margin-bottom:var(--space-2)"><?= __('booking.title') ?></h1>
    <p class="section-subtitle" style="margin-bottom:var(--space-8)"><?= __('booking.subtitle') ?></p>
    <form method="POST" action="<?= BASE_URL ?>?page=pemesanan" id="bookingForm">
      <?= csrfField() ?>
      <div class="pemesanan-layout">
        <div>
          <!-- Detail Pemesanan -->
          <div class="pemesanan-section">
            <h3 class="pemesanan-section-title"><?= __('booking.detail_title') ?></h3>
            <div class="form-group">
              <label class="form-label"><?= __('booking.select_hotel') ?></label>
              <select name="hotel_id" class="form-select" id="hotelSelect" required onchange="loadRooms(this.value)">
                <option value="">— <?= __('booking.select_hotel') ?> —</option>
                <?php foreach(($hotels ?? []) as $h): ?>
                <option value="<?= $h['id'] ?>" <?= ($selectedHotelId ?? '')==$h['id']?'selected':'' ?>>
                  <?= htmlspecialchars($h['name']) ?> — <?= formatCurrency($h['price_start']) ?> <?= __('home.per_night') ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label"><?= __('booking.check_in') ?></label>
                <input type="date" name="check_in" class="form-input" required
                       min="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($_GET['check_in'] ?? '') ?>"
                       id="checkIn">
              </div>
              <div class="form-group">
                <label class="form-label"><?= __('booking.check_out') ?></label>
                <input type="date" name="check_out" class="form-input" required
                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                       value="<?= htmlspecialchars($_GET['check_out'] ?? '') ?>"
                       id="checkOut">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label"><?= __('booking.guest_count') ?></label>
              <div style="display:flex;align-items:center;gap:var(--space-2)">
                <span>👤</span>
                <select name="guests" class="form-select" style="max-width:150px" id="guestCount">
                  <?php for($g=1;$g<=10;$g++): ?>
                  <option value="<?= $g ?>" <?= ($g==($_GET['guests']??2))?'selected':'' ?>><?= $g ?> Tamu</option>
                  <?php endfor; ?>
                </select>
              </div>
            </div>
            <div class="form-group" id="roomSelectGroup">
              <label class="form-label"><?= __('hotel_detail.select_room') ?></label>
              <select name="room_id" class="form-select" id="roomSelect" required>
                <?php if(!empty($rooms)): foreach($rooms as $r): ?>
                <option value="<?= $r['id'] ?>" data-price="<?= $r['price_per_night'] ?>" <?= ($selectedRoomId ?? '')==$r['id']?'selected':'' ?>>
                  <?= htmlspecialchars($r['type_name']) ?> — <?= formatCurrency($r['price_per_night']) ?>
                </option>
                <?php endforeach; else: ?>
                <option value="">Pilih hotel terlebih dahulu</option>
                <?php endif; ?>
              </select>
            </div>
          </div>

          <!-- Informasi Tamu -->
          <div class="pemesanan-section">
            <h3 class="pemesanan-section-title"><?= __('booking.guest_info') ?></h3>
            <div class="form-group">
              <label class="form-label"><?= __('booking.id_number') ?></label>
              <input type="text" name="id_number" class="form-input" placeholder="3201XXXXXXXXXX" maxlength="16">
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label"><?= __('booking.first_name') ?></label>
                <input type="text" name="guest_name" class="form-input" required value="<?= htmlspecialchars(currentUser()['name'] ?? '') ?>">
              </div>
              <div class="form-group">
                <label class="form-label"><?= __('booking.last_name') ?></label>
                <input type="text" name="last_name" class="form-input">
              </div>
            </div>
            <div class="form-group">
              <label class="form-label"><?= __('booking.email') ?></label>
              <input type="email" name="guest_email" class="form-input" required value="<?= htmlspecialchars(currentUser()['email'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label class="form-label"><?= __('booking.phone') ?></label>
              <input type="tel" name="guest_phone" class="form-input" placeholder="08123456789">
            </div>
            <div class="form-group">
              <label class="form-label">Permintaan Khusus (Opsional)</label>
              <textarea name="special_requests" class="form-textarea" rows="3" placeholder="Contoh: kamar non-smoking, lantai tinggi, dll."></textarea>
            </div>
          </div>

          <!-- Detail Pembayaran -->
          <div class="pemesanan-section">
            <h3 class="pemesanan-section-title"><?= __('booking.payment_detail') ?></h3>
            <div class="form-group">
              <label class="form-label">Bank Tujuan Transfer</label>
              <select name="bank_name" class="form-select" id="bankSelect">
                <option value="BCA">BCA — Bank Central Asia</option>
                <option value="Mandiri">Mandiri — Bank Mandiri</option>
                <option value="BNI">BNI — Bank Negara Indonesia</option>
                <option value="BRI">BRI — Bank Rakyat Indonesia</option>
                <option value="CIMB">CIMB Niaga</option>
                <option value="Permata">Bank Permata</option>
              </select>
            </div>
            <input type="hidden" name="payment_method" value="bank_transfer">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label"><?= __('booking.account_number') ?></label>
                <input type="text" name="account_number" class="form-input" placeholder="Nomor rekening pengirim">
              </div>
              <div class="form-group">
                <label class="form-label"><?= __('booking.account_name') ?></label>
                <input type="text" name="account_name" class="form-input" placeholder="Nama pemilik rekening">
              </div>
            </div>
            <!-- Date validation error message -->
            <div id="dateError" style="display:none;color:var(--status-danger);font-size:var(--font-size-sm);margin-bottom:var(--space-3);padding:var(--space-3);background:rgba(239,68,68,0.1);border-radius:var(--radius-md)">
              ⚠️ Tanggal check-out harus setelah check-in.
            </div>
          </div>
        </div>

        <!-- Summary Sidebar -->
        <div>
          <div class="summary-card">
            <h3 class="summary-title"><?= __('booking.summary') ?></h3>
            <div class="summary-row"><span class="label"><?= __('booking.hotel') ?></span><span class="value" id="sumHotel">-</span></div>
            <div class="summary-row"><span class="label"><?= __('booking.room_type') ?></span><span class="value" id="sumRoom">-</span></div>
            <div class="summary-row"><span class="label"><?= __('booking.check_in') ?></span><span class="value" id="sumCheckin">-</span></div>
            <div class="summary-row"><span class="label"><?= __('booking.check_out') ?></span><span class="value" id="sumCheckout">-</span></div>
            <div class="summary-row"><span class="label"><?= __('booking.nights') ?></span><span class="value" id="sumNights">-</span></div>
            <div class="summary-row"><span class="label"><?= __('booking.guests') ?></span><span class="value" id="sumGuests">-</span></div>
            <div class="summary-divider"></div>
            <div class="summary-row"><span class="label"><?= __('booking.room_rate') ?></span><span class="value" id="sumRate">-</span></div>
            <div class="summary-row"><span class="label"><?= __('booking.tax') ?> (10%)</span><span class="value" id="sumTax">-</span></div>
            <div class="summary-divider"></div>
            <div class="summary-total"><span><?= __('booking.total') ?></span><span class="amount" id="sumTotal">-</span></div>
            <button type="submit" class="btn btn-primary btn-block btn-lg" id="submitBtn"><?= __('booking.confirm') ?></button>
            <div class="summary-note"><span>⏰</span> <?= __('booking.payment_deadline') ?></div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
const BASE = '<?= BASE_URL ?>';

function loadRooms(hid) {
    if (!hid) {
        document.getElementById('roomSelect').innerHTML = '<option value="">Pilih hotel terlebih dahulu</option>';
        updateSummary();
        return;
    }
    fetch(BASE + '?page=api/rooms&hotel_id=' + hid)
        .then(r => r.json())
        .then(d => {
            const s = document.getElementById('roomSelect');
            s.innerHTML = '';
            const rooms = d.data || [];
            if (rooms.length === 0) {
                s.innerHTML = '<option value="">Tidak ada kamar tersedia</option>';
            } else {
                rooms.forEach(r => {
                    const o = document.createElement('option');
                    o.value = r.id;
                    o.dataset.price = r.price_per_night;
                    o.textContent = r.type_name + ' — Rp ' + Number(r.price_per_night).toLocaleString('id-ID');
                    s.appendChild(o);
                });
            }
            updateSummary();
        })
        .catch(() => {
            document.getElementById('roomSelect').innerHTML = '<option value="">Gagal memuat kamar</option>';
        });
}

function updateSummary() {
    const h  = document.getElementById('hotelSelect');
    const r  = document.getElementById('roomSelect');
    const ci = document.getElementById('checkIn');
    const co = document.getElementById('checkOut');

    document.getElementById('sumHotel').textContent = h.options[h.selectedIndex]?.text?.split(' — ')[0] || '-';
    const ro = r.options[r.selectedIndex];
    document.getElementById('sumRoom').textContent = ro?.text?.split(' — ')[0] || '-';
    document.getElementById('sumCheckin').textContent = ci.value || '-';
    document.getElementById('sumCheckout').textContent = co.value || '-';
    document.getElementById('sumGuests').textContent = (document.getElementById('guestCount').value || '-') + ' Tamu';

    if (ci.value && co.value) {
        const nights = Math.max(1, Math.ceil((new Date(co.value) - new Date(ci.value)) / 86400000));
        document.getElementById('sumNights').textContent = nights + ' <?= __("booking.nights") ?>';
        const price = parseFloat(ro?.dataset?.price || 0);
        const sub   = price * nights;
        const tax   = sub * 0.1;
        const tot   = sub + tax;
        document.getElementById('sumRate').textContent = 'Rp ' + sub.toLocaleString('id-ID');
        document.getElementById('sumTax').textContent  = 'Rp ' + tax.toLocaleString('id-ID');
        document.getElementById('sumTotal').textContent = 'Rp ' + tot.toLocaleString('id-ID');
    } else {
        ['sumNights','sumRate','sumTax','sumTotal'].forEach(id => document.getElementById(id).textContent = '-');
    }
}

// Fix Bug 10: update min check-out dynamically when check-in changes
document.getElementById('checkIn').addEventListener('change', function() {
    if (this.value) {
        const d = new Date(this.value);
        d.setDate(d.getDate() + 1);
        const minOut = d.toISOString().split('T')[0];
        const coEl   = document.getElementById('checkOut');
        coEl.min = minOut;
        if (coEl.value && coEl.value <= this.value) {
            coEl.value = minOut;
        }
    }
    updateSummary();
});

document.querySelectorAll('#hotelSelect,#roomSelect,#checkOut,#guestCount').forEach(e => e.addEventListener('change', updateSummary));

// Form validation before submit
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    const ci  = document.getElementById('checkIn').value;
    const co  = document.getElementById('checkOut').value;
    const err = document.getElementById('dateError');

    if (ci && co && co <= ci) {
        e.preventDefault();
        err.style.display = 'block';
        document.getElementById('checkOut').focus();
        return;
    }
    err.style.display = 'none';

    const roomId = document.getElementById('roomSelect').value;
    if (!roomId) {
        e.preventDefault();
        alert('Silakan pilih kamar terlebih dahulu.');
        return;
    }

    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').textContent = 'Memproses...';
});

updateSummary();
<?php if ($selectedHotelId): ?>
loadRooms(<?= (int)$selectedHotelId ?>);
<?php endif; ?>
</script>
