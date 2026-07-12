<?php /** My Bookings Page */
$filterStatus = $_GET['status'] ?? '';
$filterSearch = strtolower($_GET['search'] ?? '');
// Filter di sisi PHP (bookings sudah diambil semua dari DB)
$filteredBookings = array_filter($bookings ?? [], function($b) use ($filterStatus, $filterSearch) {
    if ($filterStatus && $b['status'] !== $filterStatus) return false;
    if ($filterSearch && strpos(strtolower($b['hotel_name'] . $b['booking_code'] . $b['room_type']), $filterSearch) === false) return false;
    return true;
});
$statusCounts = array_count_values(array_column($bookings ?? [], 'status'));
?>
<div class="my-bookings-page">
  <div class="container">
    <h1 style="margin-bottom:var(--space-2)"><?= __('nav.my_bookings') ?></h1>
    <p class="section-subtitle" style="margin-bottom:var(--space-5)">Kelola semua reservasi hotel Anda di satu tempat.</p>

    <!-- Filter Bar -->
    <div style="display:flex;gap:var(--space-3);flex-wrap:wrap;align-items:center;margin-bottom:var(--space-6);padding:var(--space-4);background:var(--bg-card);border:1px solid var(--border-color);border-radius:var(--radius-xl)">
      <!-- Search -->
      <div style="flex:1;min-width:200px;position:relative">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" id="mbSearch" placeholder="Cari hotel, kode booking..." value="<?= htmlspecialchars($filterSearch) ?>"
               style="width:100%;padding:8px 12px 8px 34px;background:var(--bg-secondary);border:1px solid var(--border-color);border-radius:var(--radius-md);color:var(--text-primary);font-size:var(--font-size-sm)"
               oninput="filterBookings()">
      </div>
      <!-- Status Filter Tabs -->
      <div style="display:flex;gap:var(--space-2);flex-wrap:wrap">
        <?php
        $statusLabels = ['' => 'Semua', 'pending' => 'Pending', 'confirmed' => 'Terkonfirmasi', 'checked_in' => 'Check-In', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
        $statusColors = ['' => '', 'pending' => 'warning', 'confirmed' => 'success', 'checked_in' => 'success', 'completed' => 'info', 'cancelled' => 'danger'];
        foreach($statusLabels as $sVal => $sLabel):
          $cnt = $sVal === '' ? count($bookings ?? []) : ($statusCounts[$sVal] ?? 0);
          $isActive = $filterStatus === $sVal;
        ?>
        <button onclick="filterByStatus('<?= $sVal ?>')"
          style="padding:6px 12px;border-radius:var(--radius-md);font-size:var(--font-size-xs);font-weight:600;cursor:pointer;transition:all .15s;
          background:<?= $isActive ? 'var(--color-gold)' : 'var(--bg-secondary)' ?>;
          color:<?= $isActive ? '#1a1200' : 'var(--text-muted)' ?>;
          border:1px solid <?= $isActive ? 'var(--color-gold)' : 'var(--border-color)' ?>"
          class="mb-filter-btn" data-status="<?= $sVal ?>">
          <?= $sLabel ?><?= $cnt > 0 ? " ($cnt)" : '' ?>
        </button>
        <?php endforeach; ?>
      </div>
      <!-- Total info -->
      <div style="font-size:var(--font-size-xs);color:var(--text-muted);white-space:nowrap">
        Menampilkan <strong id="mbCount"><?= count($filteredBookings) ?></strong> dari <?= count($bookings ?? []) ?> booking
      </div>
    </div>

    <?php if(empty($bookings)): ?>
    <div class="empty-state">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
      <h3>Belum ada pemesanan</h3>
      <p style="margin-bottom:var(--space-4)">Anda belum melakukan reservasi apapun.</p>
      <a href="<?= BASE_URL ?>?page=destinasi" class="btn btn-primary">Cari Hotel Sekarang</a>
    </div>
    <?php else: ?>
      <!-- Empty filtered state -->
      <div id="mbEmptyFilter" style="display:none;text-align:center;padding:var(--space-10) 0">
        <div style="font-size:2.5rem;margin-bottom:var(--space-3)">🔍</div>
        <h4>Tidak ada hasil</h4>
        <p style="color:var(--text-muted);font-size:var(--font-size-sm)">Coba ubah filter atau kata kunci pencarian.</p>
        <button onclick="resetFilter()" class="btn btn-outline btn-sm" style="margin-top:var(--space-4)">Reset Filter</button>
      </div>
      <?php foreach($bookings as $b):
        $statusClass = match($b['status']){
            'confirmed'  => 'success',
            'pending'    => 'warning',
            'cancelled'  => 'danger',
            'expired'    => 'danger',
            'completed'  => 'info',
            'checked_in' => 'success',
            default      => 'warning'
        };
      ?>
      <div class="booking-list-item" data-status="<?= $b['status'] ?>">
        <div class="booking-list-thumb">
          <?php 
          $thumbUrl = $b['hotel_thumbnail'] ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=300&q=80';
          if ($b['hotel_thumbnail'] && !str_starts_with($thumbUrl, 'http')) {
              $thumbUrl = BASE_URL . $thumbUrl;
          }
          ?>
          <img src="<?= htmlspecialchars($thumbUrl) ?>" alt="<?= htmlspecialchars($b['hotel_name']) ?>">
        </div>
        <div style="flex:1;min-width:0">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;flex-wrap:wrap">
            <h3 style="font-size:var(--font-size-lg)"><?= htmlspecialchars($b['hotel_name']) ?></h3>
            <span class="badge badge-<?= $statusClass ?>"><?= __('status.'.$b['status']) ?></span>
          </div>
          <div style="font-size:var(--font-size-sm);color:var(--text-muted);display:flex;gap:12px;flex-wrap:wrap;margin-bottom:4px">
            <span>🛏️ <?= htmlspecialchars($b['room_type']) ?></span>
            <span>📅 <?= formatDate($b['check_in']) ?> → <?= formatDate($b['check_out']) ?> (<?= $b['nights'] ?> <?= __('booking.nights') ?>)</span>
            <span>👥 <?= $b['guests'] ?> tamu</span>
          </div>
          <div style="font-size:var(--font-size-sm);color:var(--text-muted);display:flex;gap:12px;flex-wrap:wrap">
            <span>💳 <strong style="color:var(--color-gold)"><?= formatCurrency($b['total_price']) ?></strong></span>
            <span>🆔 <code style="font-size:11px;background:var(--bg-secondary);padding:2px 6px;border-radius:4px"><?= $b['booking_code'] ?></code></span>
          </div>
        </div>
        <div style="display:flex;flex-direction:column;justify-content:center;align-items:flex-end;gap:var(--space-2);flex-shrink:0">
          <a href="<?= BASE_URL ?>?page=booking-detail&id=<?= $b['id'] ?>" class="btn btn-outline btn-sm">🔍 Lihat Detail</a>
          <?php if($b['status'] === 'pending'): ?>
            <div style="font-size:var(--font-size-xs);color:var(--status-warning);text-align:right">⏰ Menunggu Pembayaran</div>
            <a href="<?= BASE_URL ?>?page=upload-bukti&booking_id=<?= $b['id'] ?>" class="btn btn-primary btn-sm">
              📤 Upload Bukti
            </a>
            <a href="<?= BASE_URL ?>?page=cancel-booking&id=<?= $b['id'] ?>"
               class="btn btn-outline btn-sm"
               style="color:var(--status-danger);border-color:var(--status-danger)"
               onclick="return confirm('Apakah Anda yakin ingin membatalkan booking ini?')">
              Batalkan
            </a>
          <?php elseif($b['status'] === 'completed'): ?>
            <a href="<?= BASE_URL ?>?page=review&booking_id=<?= $b['id'] ?>" class="btn btn-primary btn-sm">⭐ Beri Review</a>
          <?php elseif($b['status'] === 'confirmed' || $b['status'] === 'checked_in'): ?>
            <a href="<?= BASE_URL ?>?page=hotel&id=<?= $b['hotel_id'] ?>" class="btn btn-outline btn-sm">Lihat Hotel</a>
          <?php else: ?>
            <a href="<?= BASE_URL ?>?page=hotel&id=<?= $b['hotel_id'] ?>" class="btn btn-outline btn-sm">Pesan Lagi</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- Payment Info Modal -->
<div class="modal-backdrop" id="paymentModal">
  <div class="modal" style="max-width:480px">
    <div class="modal-header">
      <h3>Informasi Pembayaran</h3>
      <button class="modal-close" onclick="this.closest('.modal-backdrop').classList.remove('active')">✕</button>
    </div>
    <div class="modal-body">
      <div style="text-align:center;margin-bottom:var(--space-5)">
        <div style="font-size:var(--font-size-sm);color:var(--text-muted)">Kode Booking</div>
        <div style="font-size:var(--font-size-xl);font-weight:700;color:var(--color-gold)" id="modalBookingCode">-</div>
      </div>
      <div style="background:var(--bg-secondary);border-radius:var(--radius-md);padding:var(--space-5);margin-bottom:var(--space-4)">
        <div style="display:flex;justify-content:space-between;margin-bottom:var(--space-3)">
          <span style="color:var(--text-muted)">Total Pembayaran</span>
          <strong style="color:var(--color-gold);font-size:var(--font-size-lg)" id="modalAmount">-</strong>
        </div>
        <div style="border-top:1px solid var(--border-color);padding-top:var(--space-3)">
          <p style="font-size:var(--font-size-sm);color:var(--text-muted);margin-bottom:var(--space-2)">Transfer ke salah satu rekening berikut:</p>
          <div style="display:grid;gap:var(--space-2)">
            <div style="background:var(--bg-card);padding:var(--space-3);border-radius:var(--radius-sm);border:1px solid var(--border-color)">
              <strong>BCA</strong> — <span style="color:var(--color-gold);font-weight:700">1234567890</span><br>
              <small style="color:var(--text-muted)">a.n. Lynvaii Hotel Indonesia</small>
            </div>
            <div style="background:var(--bg-card);padding:var(--space-3);border-radius:var(--radius-sm);border:1px solid var(--border-color)">
              <strong>Mandiri</strong> — <span style="color:var(--color-gold);font-weight:700">0987654321</span><br>
              <small style="color:var(--text-muted)">a.n. Lynvaii Hotel Indonesia</small>
            </div>
          </div>
        </div>
      </div>
      <div style="background:rgba(212,163,115,0.1);border:1px solid var(--color-gold);border-radius:var(--radius-md);padding:var(--space-4);font-size:var(--font-size-sm)">
        ⚠️ <strong>Penting:</strong> Sertakan kode booking sebagai berita transfer. Booking akan otomatis dibatalkan jika pembayaran tidak dilakukan dalam <strong>24 jam</strong>.
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-primary" onclick="this.closest('.modal-backdrop').classList.remove('active')">Mengerti</button>
    </div>
  </div>
</div>

<script>
function showPaymentInfo(code, amount) {
    document.getElementById('modalBookingCode').textContent = code;
    document.getElementById('modalAmount').textContent = amount;
    document.getElementById('paymentModal').classList.add('active');
}
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('active');
});

// ─── Filter Logic ────────────────────────────────────────────────────────────
let currentStatus = '<?= $filterStatus ?>';
const allCards = document.querySelectorAll('.booking-list-item');

function filterBookings() {
    const search = document.getElementById('mbSearch').value.toLowerCase();
    let visible = 0;
    allCards.forEach(card => {
        const text = card.textContent.toLowerCase();
        const status = card.dataset.status || '';
        const matchSearch = !search || text.includes(search);
        const matchStatus = !currentStatus || status === currentStatus;
        const show = matchSearch && matchStatus;
        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('mbCount').textContent = visible;
    document.getElementById('mbEmptyFilter').style.display = visible === 0 ? 'block' : 'none';
}

function filterByStatus(status) {
    currentStatus = status;
    // Update button styles
    document.querySelectorAll('.mb-filter-btn').forEach(btn => {
        const active = btn.dataset.status === status;
        btn.style.background = active ? 'var(--color-gold)' : 'var(--bg-secondary)';
        btn.style.color = active ? '#1a1200' : 'var(--text-muted)';
        btn.style.borderColor = active ? 'var(--color-gold)' : 'var(--border-color)';
    });
    filterBookings();
}

function resetFilter() {
    currentStatus = '';
    document.getElementById('mbSearch').value = '';
    filterByStatus('');
}
</script>
