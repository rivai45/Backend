<?php /** Booking Detail Page */ ?>
<?php
$statusConfig = [
    'pending'    => ['class' => 'warning',  'label' => 'Menunggu Pembayaran', 'icon' => '⏳'],
    'confirmed'  => ['class' => 'success',  'label' => 'Terkonfirmasi',       'icon' => '✅'],
    'checked_in' => ['class' => 'success',  'label' => 'Sedang Check-In',     'icon' => '🏨'],
    'completed'  => ['class' => 'info',     'label' => 'Selesai',             'icon' => '🎉'],
    'cancelled'  => ['class' => 'danger',   'label' => 'Dibatalkan',          'icon' => '❌'],
    'expired'    => ['class' => 'danger',   'label' => 'Kedaluwarsa',         'icon' => '⌛'],
];
$sc     = $statusConfig[$booking['status']] ?? ['class' => 'warning', 'label' => $booking['status'], 'icon' => '❓'];
$thumbUrl = $booking['hotel_thumbnail'] ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80';
if ($booking['hotel_thumbnail'] && !str_starts_with($thumbUrl, 'http')) {
    $thumbUrl = BASE_URL . $thumbUrl;
}
?>

<style>
.bd-hero { position:relative; height:260px; border-radius: var(--radius-xl); overflow:hidden; margin-bottom:var(--space-6); }
.bd-hero img { width:100%; height:100%; object-fit:cover; }
.bd-hero-overlay { position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.2) 60%); display:flex; align-items:flex-end; padding:var(--space-6); }
.bd-hero-title h1 { font-size:var(--font-size-2xl); margin-bottom:var(--space-1); }
.bd-hero-title p { color:rgba(255,255,255,0.75); font-size:var(--font-size-sm); }
.bd-layout { display:grid; grid-template-columns:1fr 360px; gap:var(--space-6); }
@media(max-width:900px){ .bd-layout { grid-template-columns:1fr; } }
.bd-card { background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--radius-xl); padding:var(--space-6); margin-bottom:var(--space-4); }
.bd-card h3 { font-size:var(--font-size-base); font-weight:600; color:var(--color-gold); margin-bottom:var(--space-4); padding-bottom:var(--space-3); border-bottom:1px solid var(--border-color); }
.bd-row { display:flex; justify-content:space-between; align-items:center; padding:var(--space-2) 0; border-bottom:1px solid rgba(255,255,255,0.04); font-size:var(--font-size-sm); }
.bd-row:last-child { border-bottom:none; }
.bd-row .label { color:var(--text-muted); }
.bd-row .value { font-weight:500; text-align:right; }
.bd-total { display:flex; justify-content:space-between; align-items:center; padding:var(--space-4) 0 0; margin-top:var(--space-3); border-top:2px solid var(--color-gold); }
.bd-total .amount { font-size:var(--font-size-xl); font-weight:700; color:var(--color-gold); }
.status-banner { display:flex; align-items:center; gap:var(--space-3); padding:var(--space-4); border-radius:var(--radius-lg); margin-bottom:var(--space-4); font-weight:600; }
.status-banner.warning { background:rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.3); color:#f59e0b; }
.status-banner.success { background:rgba(16,185,129,0.12); border:1px solid rgba(16,185,129,0.3); color:#10b981; }
.status-banner.danger  { background:rgba(239,68,68,0.12);  border:1px solid rgba(239,68,68,0.3);  color:#ef4444; }
.status-banner.info    { background:rgba(59,130,246,0.12); border:1px solid rgba(59,130,246,0.3); color:#3b82f6; }
.bank-item { background:var(--bg-secondary); border-radius:var(--radius-md); padding:var(--space-3) var(--space-4); margin-bottom:var(--space-2); display:flex; justify-content:space-between; align-items:center; border:1px solid var(--border-color); }
.bank-item strong { color:var(--color-gold); }
.proof-img { width:100%; max-height:220px; object-fit:contain; border-radius:var(--radius-md); border:1px solid var(--border-color); background:var(--bg-secondary); }
</style>

<div class="container" style="padding-top:var(--space-8);padding-bottom:var(--space-12)">

  <!-- Back button -->
  <a href="<?= BASE_URL ?>?page=my-bookings" class="btn btn-outline btn-sm" style="margin-bottom:var(--space-5)">
    ← Kembali ke Riwayat Booking
  </a>

  <!-- Hero -->
  <div class="bd-hero">
    <img src="<?= htmlspecialchars($thumbUrl) ?>" alt="<?= htmlspecialchars($booking['hotel_name']) ?>">
    <div class="bd-hero-overlay">
      <div class="bd-hero-title">
        <h1><?= htmlspecialchars($booking['hotel_name']) ?></h1>
        <p>📍 <?= htmlspecialchars($booking['hotel_city'] ?? '') ?> &nbsp;·&nbsp; 🛏️ <?= htmlspecialchars($booking['room_type']) ?></p>
      </div>
    </div>
  </div>

  <!-- Status Banner -->
  <div class="status-banner <?= $sc['class'] ?>">
    <span style="font-size:1.5rem"><?= $sc['icon'] ?></span>
    <div>
      <div style="font-size:var(--font-size-base)"><?= $sc['label'] ?></div>
      <div style="font-weight:400;font-size:var(--font-size-sm);opacity:.8">Kode Booking: <strong><?= $booking['booking_code'] ?></strong></div>
    </div>
  </div>

  <div class="bd-layout">
    <!-- LEFT -->
    <div>
      <!-- Info Menginap -->
      <div class="bd-card">
        <h3>📅 Informasi Menginap</h3>
        <div class="bd-row"><span class="label">Hotel</span><span class="value"><?= htmlspecialchars($booking['hotel_name']) ?></span></div>
        <div class="bd-row"><span class="label">Tipe Kamar</span><span class="value"><?= htmlspecialchars($booking['room_type']) ?></span></div>
        <div class="bd-row"><span class="label">Check-in</span><span class="value"><?= formatDate($booking['check_in']) ?></span></div>
        <div class="bd-row"><span class="label">Check-out</span><span class="value"><?= formatDate($booking['check_out']) ?></span></div>
        <div class="bd-row"><span class="label">Durasi</span><span class="value"><?= $booking['nights'] ?> malam</span></div>
        <div class="bd-row"><span class="label">Jumlah Tamu</span><span class="value"><?= $booking['guests'] ?> orang</span></div>
        <?php if ($booking['special_requests']): ?>
        <div class="bd-row" style="align-items:flex-start;flex-direction:column;gap:4px">
          <span class="label">Permintaan Khusus</span>
          <span class="value" style="text-align:left;color:var(--text-muted)"><?= nl2br(htmlspecialchars($booking['special_requests'])) ?></span>
        </div>
        <?php endif; ?>
      </div>

      <!-- Info Tamu -->
      <div class="bd-card">
        <h3>👤 Data Tamu</h3>
        <div class="bd-row"><span class="label">Nama</span><span class="value"><?= htmlspecialchars($booking['guest_name']) ?></span></div>
        <div class="bd-row"><span class="label">Email</span><span class="value"><?= htmlspecialchars($booking['guest_email']) ?></span></div>
        <?php if ($booking['guest_phone']): ?>
        <div class="bd-row"><span class="label">Telepon</span><span class="value"><?= htmlspecialchars($booking['guest_phone']) ?></span></div>
        <?php endif; ?>
        <div class="bd-row"><span class="label">Tanggal Pesan</span><span class="value"><?= formatDate($booking['created_at'], 'd M Y, H:i') ?></span></div>
      </div>

      <!-- Tombol Review (hanya untuk completed & belum review) -->
      <?php if ($booking['status'] === 'completed' && !$existingReview): ?>
      <div class="bd-card" style="border-color:var(--color-gold)">
        <h3>⭐ Bagikan Pengalaman Anda</h3>
        <p style="color:var(--text-muted);font-size:var(--font-size-sm);margin-bottom:var(--space-4)">
          Anda sudah menyelesaikan menginap di <?= htmlspecialchars($booking['hotel_name']) ?>. Bagikan pengalaman Anda untuk membantu tamu lain!
        </p>
        <a href="<?= BASE_URL ?>?page=review&booking_id=<?= $booking['id'] ?>" class="btn btn-primary">⭐ Tulis Review</a>
      </div>
      <?php elseif ($booking['status'] === 'completed' && $existingReview): ?>
      <div class="bd-card">
        <h3>⭐ Review</h3>
        <p style="color:var(--color-gold);font-size:var(--font-size-sm)">✅ Anda sudah memberikan review untuk hotel ini. Terima kasih!</p>
      </div>
      <?php endif; ?>
    </div>

    <!-- RIGHT SIDEBAR -->
    <div>
      <!-- Ringkasan Biaya -->
      <div class="bd-card">
        <h3>💰 Ringkasan Biaya</h3>
        <div class="bd-row">
          <span class="label">Harga Kamar/malam</span>
          <span class="value"><?= formatCurrency($booking['room_price']) ?></span>
        </div>
        <div class="bd-row">
          <span class="label">× <?= $booking['nights'] ?> malam</span>
          <span class="value"><?= formatCurrency($booking['room_price'] * $booking['nights']) ?></span>
        </div>
        <div class="bd-row">
          <span class="label">Pajak (10%)</span>
          <span class="value"><?= formatCurrency($booking['tax_amount']) ?></span>
        </div>
        <div class="bd-total">
          <span>Total</span>
          <span class="amount"><?= formatCurrency($booking['total_price']) ?></span>
        </div>
      </div>

      <!-- Info Pembayaran -->
      <div class="bd-card">
        <h3>🏦 Pembayaran</h3>
        <?php if ($payment): ?>
          <div class="bd-row"><span class="label">Metode</span><span class="value"><?= ucfirst(str_replace('_',' ',$payment['payment_method'])) ?></span></div>
          <?php if ($payment['bank_name']): ?>
          <div class="bd-row"><span class="label">Bank</span><span class="value"><?= htmlspecialchars($payment['bank_name']) ?></span></div>
          <?php endif; ?>
          <div class="bd-row">
            <span class="label">Status Bayar</span>
            <span class="value">
              <span class="badge badge-<?= $payment['status']==='paid'?'success':($payment['status']==='pending'?'warning':'danger') ?>">
                <?= ucfirst($payment['status']) ?>
              </span>
            </span>
          </div>
          <?php if ($payment['payment_proof']): ?>
          <div style="margin-top:var(--space-3)">
            <p style="font-size:var(--font-size-xs);color:var(--text-muted);margin-bottom:var(--space-2)">Bukti Transfer:</p>
            <a href="<?= BASE_URL . $payment['payment_proof'] ?>" target="_blank">
              <img src="<?= BASE_URL . $payment['payment_proof'] ?>" class="proof-img" alt="Bukti Transfer">
            </a>
          </div>
          <?php endif; ?>
        <?php else: ?>
          <p style="color:var(--text-muted);font-size:var(--font-size-sm)">Data pembayaran tidak ditemukan.</p>
        <?php endif; ?>

        <!-- Tombol Upload -->
        <?php if ($booking['status'] === 'pending'): ?>
        <div style="margin-top:var(--space-4);display:grid;gap:var(--space-2)">
          <?php if (!$payment || !$payment['payment_proof']): ?>
          <a href="<?= BASE_URL ?>?page=upload-bukti&booking_id=<?= $booking['id'] ?>" class="btn btn-primary">
            📤 Upload Bukti Transfer
          </a>
          <?php else: ?>
          <a href="<?= BASE_URL ?>?page=upload-bukti&booking_id=<?= $booking['id'] ?>" class="btn btn-outline btn-sm">
            🔄 Ganti Bukti Transfer
          </a>
          <?php endif; ?>
          <div style="background:var(--bg-secondary);border-radius:var(--radius-md);padding:var(--space-3)">
            <p style="font-size:var(--font-size-xs);color:var(--text-muted);margin-bottom:var(--space-2);font-weight:600">Transfer ke:</p>
            <div class="bank-item"><span>BCA</span><strong>1234567890</strong></div>
            <div class="bank-item"><span>Mandiri</span><strong>0987654321</strong></div>
            <p style="font-size:var(--font-size-xs);color:var(--text-muted);margin-top:var(--space-2)">a.n. Lynvaii Hotel Indonesia<br>Cantumkan kode booking sebagai berita transfer.</p>
          </div>
          <a href="<?= BASE_URL ?>?page=cancel-booking&id=<?= $booking['id'] ?>"
             class="btn btn-outline btn-sm" style="color:var(--status-danger);border-color:var(--status-danger)"
             onclick="return confirm('Yakin ingin membatalkan booking ini?')">
            ❌ Batalkan Booking
          </a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
