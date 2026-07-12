<?php /** Review Page */ ?>

<style>
.review-container { max-width:640px; margin:0 auto; padding:var(--space-8) var(--space-4) var(--space-12); }
.review-card { background:var(--bg-card); border:1px solid var(--border-color); border-radius:var(--radius-xl); padding:var(--space-8); }
.hotel-info-mini { display:flex; gap:var(--space-4); align-items:center; background:var(--bg-secondary); border-radius:var(--radius-lg); padding:var(--space-4); margin-bottom:var(--space-6); }
.hotel-thumb { width:80px; height:60px; border-radius:var(--radius-md); object-fit:cover; flex-shrink:0; }
.star-rating { display:flex; flex-direction:row-reverse; gap:var(--space-2); justify-content:flex-end; }
.star-rating input { display:none; }
.star-rating label { font-size:2.2rem; cursor:pointer; color:var(--border-color); transition:color 0.15s; }
.star-rating input:checked ~ label,
.star-rating label:hover,
.star-rating label:hover ~ label { color:#f59e0b; }
.rating-desc { font-size:var(--font-size-sm); color:var(--text-muted); margin-top:var(--space-2); min-height:20px; transition:all .2s; }
</style>

<?php
$thumbUrl = $booking['hotel_thumbnail'] ?: 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=300&q=80';
if ($booking['hotel_thumbnail'] && !str_starts_with($thumbUrl, 'http')) {
    $thumbUrl = BASE_URL . $thumbUrl;
}
$ratingDescs = ['', 'Sangat Buruk 😞', 'Kurang Memuaskan 😐', 'Cukup Baik 🙂', 'Bagus 😊', 'Luar Biasa! 🤩'];
?>

<div class="review-container">
  <a href="<?= BASE_URL ?>?page=booking-detail&id=<?= $booking['id'] ?>" class="btn btn-outline btn-sm" style="margin-bottom:var(--space-5)">
    ← Kembali ke Detail Booking
  </a>

  <div class="review-card">
    <h1 style="font-size:var(--font-size-xl);margin-bottom:var(--space-2)">⭐ Tulis Ulasan</h1>
    <p style="color:var(--text-muted);font-size:var(--font-size-sm);margin-bottom:var(--space-6)">Bagikan pengalaman menginap Anda untuk membantu tamu lain memilih hotel terbaik.</p>

    <!-- Info Hotel -->
    <div class="hotel-info-mini">
      <img src="<?= htmlspecialchars($thumbUrl) ?>" class="hotel-thumb" alt="<?= htmlspecialchars($booking['hotel_name']) ?>">
      <div>
        <div style="font-weight:700"><?= htmlspecialchars($booking['hotel_name']) ?></div>
        <div style="font-size:var(--font-size-sm);color:var(--text-muted)"><?= htmlspecialchars($booking['room_type']) ?> · <?= formatDate($booking['check_in']) ?> – <?= formatDate($booking['check_out']) ?></div>
        <div style="font-size:var(--font-size-xs);color:var(--text-muted)"><?= $booking['nights'] ?> malam · <?= $booking['guests'] ?> tamu</div>
      </div>
    </div>

    <form method="POST" action="<?= BASE_URL ?>?page=review&booking_id=<?= $booking['id'] ?>&action=store">
      <?= csrfField() ?>

      <!-- Star Rating -->
      <div class="form-group">
        <label class="form-label">Rating Keseluruhan <span style="color:var(--status-danger)">*</span></label>
        <div class="star-rating" id="starRating">
          <?php for ($i = 5; $i >= 1; $i--): ?>
          <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>">
          <label for="star<?= $i ?>" title="<?= $ratingDescs[$i] ?>">★</label>
          <?php endfor; ?>
        </div>
        <div class="rating-desc" id="ratingDesc">Klik bintang untuk memberi nilai</div>
      </div>

      <!-- Komentar -->
      <div class="form-group">
        <label class="form-label">Komentar <span style="color:var(--text-muted);font-weight:400">(opsional)</span></label>
        <textarea name="comment" class="form-textarea" rows="5"
          placeholder="Ceritakan pengalaman menginap Anda: kebersihan kamar, pelayanan staff, fasilitas, lokasi, dll..."
          style="resize:vertical" maxlength="1000"></textarea>
        <div style="font-size:var(--font-size-xs);color:var(--text-muted);text-align:right;margin-top:4px">
          <span id="charCount">0</span>/1000 karakter
        </div>
      </div>

      <div style="display:grid;gap:var(--space-3)">
        <button type="submit" class="btn btn-primary btn-lg" id="submitReview">⭐ Kirim Review</button>
        <a href="<?= BASE_URL ?>?page=booking-detail&id=<?= $booking['id'] ?>" class="btn btn-outline">Batal</a>
      </div>
    </form>
  </div>
</div>

<script>
const ratingDescs = <?= json_encode($ratingDescs) ?>;

document.querySelectorAll('.star-rating input').forEach(input => {
  input.addEventListener('change', function() {
    document.getElementById('ratingDesc').textContent = ratingDescs[this.value] || '';
  });
});

document.querySelector('[name=comment]')?.addEventListener('input', function() {
  document.getElementById('charCount').textContent = this.value.length;
});

document.getElementById('submitReview').addEventListener('click', function(e) {
  const checked = document.querySelector('.star-rating input:checked');
  if (!checked) {
    e.preventDefault();
    document.getElementById('ratingDesc').textContent = '⚠️ Silakan pilih rating terlebih dahulu!';
    document.getElementById('ratingDesc').style.color = '#ef4444';
    return;
  }
  this.disabled = true;
  this.textContent = 'Mengirim...';
});
</script>
