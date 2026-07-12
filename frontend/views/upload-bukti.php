<?php /** Upload Bukti Pembayaran Page */ ?>

<style>
.upload-container { max-width:620px; margin:0 auto; padding:var(--space-8) var(--space-4) var(--space-12); }
.upload-card { background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--radius-xl); padding:var(--space-8); }
.upload-card-title { font-size:var(--font-size-xl); font-weight:700; margin-bottom:var(--space-2); }
.upload-card-subtitle { color:var(--text-muted); font-size:var(--font-size-sm); margin-bottom:var(--space-6); }
.booking-info-row { display:flex; justify-content:space-between; padding:var(--space-2) 0; border-bottom:1px solid rgba(255,255,255,0.05); font-size:var(--font-size-sm); }
.booking-info-row .label { color:var(--text-muted); }
.booking-info-row .value { font-weight:600; }
.upload-dropzone {
  border: 2px dashed var(--border-color);
  border-radius: var(--radius-lg);
  padding: var(--space-10) var(--space-4);
  text-align: center;
  cursor: pointer;
  transition: all 0.2s;
  background: var(--bg-secondary);
  position: relative;
}
.upload-dropzone:hover, .upload-dropzone.dragover {
  border-color: var(--color-gold);
  background: rgba(212,163,115,0.05);
}
.upload-dropzone input[type=file] { position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%; }
.upload-icon { font-size:3rem; margin-bottom:var(--space-3); display:block; }
.upload-preview { width:100%; max-height:240px; object-fit:contain; border-radius:var(--radius-md); border:1px solid var(--border-color); display:none; margin-top:var(--space-4); }
.bank-info { background:rgba(212,163,115,0.08); border:1px solid rgba(212,163,115,0.25); border-radius:var(--radius-lg); padding:var(--space-5); margin-bottom:var(--space-6); }
.bank-info h4 { color:var(--color-gold); font-size:var(--font-size-sm); margin-bottom:var(--space-3); }
.bank-row { display:flex; justify-content:space-between; align-items:center; padding:var(--space-2) 0; font-size:var(--font-size-sm); }
.bank-row strong { color:var(--color-gold); font-size:var(--font-size-base); }
</style>

<div class="upload-container">
  <a href="<?= BASE_URL ?>?page=booking-detail&id=<?= $booking['id'] ?>" class="btn btn-outline btn-sm" style="margin-bottom:var(--space-5)">
    ← Kembali ke Detail Booking
  </a>

  <div class="upload-card">
    <div class="upload-card-title">📤 Upload Bukti Transfer</div>
    <div class="upload-card-subtitle">Kode Booking: <strong style="color:var(--color-gold)"><?= $booking['booking_code'] ?></strong></div>

    <!-- Info Booking -->
    <div style="background:var(--bg-secondary);border-radius:var(--radius-lg);padding:var(--space-4);margin-bottom:var(--space-6)">
      <div class="booking-info-row"><span class="label">Hotel</span><span class="value"><?= htmlspecialchars($booking['hotel_name']) ?></span></div>
      <div class="booking-info-row"><span class="label">Kamar</span><span class="value"><?= htmlspecialchars($booking['room_type']) ?></span></div>
      <div class="booking-info-row"><span class="label">Check-in</span><span class="value"><?= formatDate($booking['check_in']) ?></span></div>
      <div class="booking-info-row"><span class="label">Check-out</span><span class="value"><?= formatDate($booking['check_out']) ?></span></div>
      <div class="booking-info-row" style="border-bottom:none">
        <span class="label">Total Tagihan</span>
        <span class="value" style="color:var(--color-gold);font-size:var(--font-size-lg)"><?= formatCurrency($booking['total_price']) ?></span>
      </div>
    </div>

    <!-- Info Rekening -->
    <div class="bank-info">
      <h4>🏦 Rekening Tujuan Transfer</h4>
      <div class="bank-row"><span>BCA — Bank Central Asia</span><strong>1234567890</strong></div>
      <div class="bank-row"><span>Mandiri — Bank Mandiri</span><strong>0987654321</strong></div>
      <div class="bank-row"><span>BNI — Bank Negara Indonesia</span><strong>1122334455</strong></div>
      <div style="font-size:var(--font-size-xs);color:var(--text-muted);margin-top:var(--space-2)">
        a.n. <strong>Lynvaii Hotel Indonesia</strong> &nbsp;·&nbsp; Sertakan kode booking sebagai berita transfer.
      </div>
    </div>

    <!-- Form Upload -->
    <?php if ($payment && $payment['payment_proof']): ?>
    <div style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.3);border-radius:var(--radius-lg);padding:var(--space-4);margin-bottom:var(--space-5)">
      <p style="color:#10b981;font-size:var(--font-size-sm);margin-bottom:var(--space-3)">✅ Bukti transfer sudah diupload sebelumnya. Anda dapat menggantinya di bawah ini.</p>
      <img src="<?= BASE_URL . $payment['payment_proof'] ?>" style="width:100%;max-height:180px;object-fit:contain;border-radius:var(--radius-md);border:1px solid var(--border-color)" alt="Bukti lama">
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>?page=upload-bukti&booking_id=<?= $booking['id'] ?>&action=upload" enctype="multipart/form-data" id="uploadForm">
      <?= csrfField() ?>

      <div class="form-group">
        <label class="form-label">Foto / Screenshot Bukti Transfer</label>
        <div class="upload-dropzone" id="dropzone">
          <input type="file" name="payment_proof" accept="image/jpeg,image/png,image/webp" required id="fileInput">
          <span class="upload-icon">📁</span>
          <p style="font-weight:600;margin-bottom:var(--space-1)" id="dropText">Klik atau seret file ke sini</p>
          <p style="font-size:var(--font-size-xs);color:var(--text-muted)">JPG, PNG, WebP — Maks. 5MB</p>
          <img id="preview" class="upload-preview" alt="Preview">
        </div>
      </div>

      <div style="display:grid;gap:var(--space-3);margin-top:var(--space-6)">
        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
          📤 Upload & Kirim ke Admin
        </button>
        <a href="<?= BASE_URL ?>?page=booking-detail&id=<?= $booking['id'] ?>" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
</div>

<script>
const fileInput = document.getElementById('fileInput');
const preview   = document.getElementById('preview');
const dropText  = document.getElementById('dropText');
const dropzone  = document.getElementById('dropzone');

fileInput.addEventListener('change', function() {
  if (this.files && this.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.style.display = 'block';
      dropText.textContent = this.files[0].name;
    };
    reader.readAsDataURL(this.files[0]);
  }
});

['dragover','dragenter'].forEach(ev => dropzone.addEventListener(ev, e => { e.preventDefault(); dropzone.classList.add('dragover'); }));
['dragleave','drop'].forEach(ev => dropzone.addEventListener(ev, e => { e.preventDefault(); dropzone.classList.remove('dragover'); }));
dropzone.addEventListener('drop', e => {
  if (e.dataTransfer.files[0]) {
    const dt = new DataTransfer();
    dt.items.add(e.dataTransfer.files[0]);
    fileInput.files = dt.files;
    fileInput.dispatchEvent(new Event('change'));
  }
});

document.getElementById('uploadForm').addEventListener('submit', function() {
  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.textContent = 'Mengupload...';
});
</script>
