<?php /** Detail Hotel Page */
$desc = getLang() === 'en' && $hotel['description_en'] ? $hotel['description_en'] : $hotel['description_id'];
$amenities = json_decode($hotel['amenities'] ?? '[]', true) ?: [];
$galleryImages = [
    'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1200&q=80',
    'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=80',
    'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=1200&q=80',
    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=1200&q=80',
];
?>
<div class="detail-page">
  <div class="container">
    <div class="detail-layout">
      <div>
        <!-- Gallery -->
        <div class="detail-gallery">
          <div class="detail-gallery-main">
            <img src="<?= $galleryImages[0] ?>" alt="<?= htmlspecialchars($hotel['name']) ?>" id="mainImage">
          </div>
          <div class="detail-gallery-thumbs">
            <?php foreach ($galleryImages as $i => $img): ?>
            <img src="<?= str_replace('w=1200','w=300',$img) ?>" alt="Foto <?= $i+1 ?>"
                 class="<?= $i===0?'active':'' ?>"
                 onclick="setMainImage('<?= $img ?>',this)">
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Info -->
        <div class="detail-info">
          <h1><?= htmlspecialchars($hotel['name']) ?></h1>
          <div class="detail-meta">
            <div class="hotel-card-rating">
              <?php for($s=0;$s<5;$s++): ?>
              <span class="star"><?= $s < round($hotel['rating']) ? '★' : '☆' ?></span>
              <?php endfor; ?>
            </div>
            <span><?= $hotel['rating'] ?> (<?= number_format($hotel['total_reviews']) ?> ulasan)</span>
            <span>📍 <?= htmlspecialchars($hotel['city']) ?>, Indonesia</span>
          </div>

          <div class="detail-tabs">
            <span class="detail-tab active" onclick="showTab('about',this)"><?= __('hotel_detail.about') ?></span>
            <span class="detail-tab" onclick="showTab('facilities',this)"><?= __('hotel_detail.facilities') ?></span>
            <span class="detail-tab" onclick="showTab('reviews',this)"><?= __('hotel_detail.reviews') ?></span>
          </div>

          <div id="tab-about" class="detail-tab-content">
            <p class="detail-description"><?= nl2br(htmlspecialchars($desc)) ?></p>
            <?php if (!empty($amenities)): ?>
            <div class="detail-amenities">
              <?php foreach($amenities as $a): ?>
              <span class="amenity-tag"><?= htmlspecialchars($a) ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>

          <div id="tab-facilities" class="detail-tab-content" style="display:none">
            <?php if (!empty($amenities)): ?>
            <div class="detail-amenities">
              <?php foreach($amenities as $a): ?>
              <span class="amenity-tag"><?= htmlspecialchars($a) ?></span>
              <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-muted">Informasi fasilitas belum tersedia.</p>
            <?php endif; ?>
          </div>

          <div id="tab-reviews" class="detail-tab-content" style="display:none">
            <?php
            // Hitung distribusi rating
            $ratingDist = [5=>0, 4=>0, 3=>0, 2=>0, 1=>0];
            foreach($reviews as $rv) $ratingDist[$rv['rating']] = ($ratingDist[$rv['rating']] ?? 0) + 1;
            $totalReviews = count($reviews);
            ?>

            <?php if (empty($reviews)): ?>
            <div style="text-align:center;padding:var(--space-10) 0">
              <div style="font-size:3rem;margin-bottom:var(--space-3)">💬</div>
              <h4 style="margin-bottom:var(--space-2)">Belum ada ulasan</h4>
              <p style="color:var(--text-muted);font-size:var(--font-size-sm)">Jadilah yang pertama memberikan ulasan untuk hotel ini.</p>
              <?php if(isLoggedIn()): ?>
              <a href="<?= BASE_URL ?>?page=my-bookings" class="btn btn-outline btn-sm" style="margin-top:var(--space-4)">Lihat Booking Saya</a>
              <?php endif; ?>
            </div>
            <?php else: ?>

            <!-- Rating Summary -->
            <div style="display:grid;grid-template-columns:auto 1fr;gap:var(--space-6);align-items:center;background:var(--bg-secondary);border-radius:var(--radius-lg);padding:var(--space-5);margin-bottom:var(--space-6)">
              <div style="text-align:center;padding:0 var(--space-4)">
                <div style="font-size:3.5rem;font-weight:800;color:var(--color-gold);line-height:1"><?= number_format($hotel['rating'],1) ?></div>
                <div class="hotel-card-rating" style="justify-content:center;margin:var(--space-2) 0">
                  <?php for($s=0;$s<5;$s++): ?>
                  <span class="star"><?= $s < round($hotel['rating']) ? '★' : '☆' ?></span>
                  <?php endfor; ?>
                </div>
                <div style="font-size:var(--font-size-xs);color:var(--text-muted)"><?= $totalReviews ?> ulasan</div>
              </div>
              <div style="display:grid;gap:var(--space-2)">
                <?php foreach([5,4,3,2,1] as $star): ?>
                <?php $pct = $totalReviews > 0 ? round(($ratingDist[$star]/$totalReviews)*100) : 0; ?>
                <div style="display:flex;align-items:center;gap:var(--space-2);font-size:var(--font-size-xs)">
                  <span style="width:20px;text-align:right;color:var(--text-muted)"><?= $star ?>★</span>
                  <div style="flex:1;height:8px;background:var(--border-color);border-radius:4px;overflow:hidden">
                    <div style="width:<?= $pct ?>%;height:100%;background:var(--color-gold);border-radius:4px;transition:width .6s ease"></div>
                  </div>
                  <span style="width:28px;color:var(--text-muted)"><?= $pct ?>%</span>
                </div>
                <?php endforeach; ?>
              </div>
            </div>

            <!-- Review Cards -->
            <?php foreach($reviews as $r):
              $avatarUrl = null;
              if (!empty($r['avatar'])) {
                $avatarUrl = str_starts_with($r['avatar'], 'http') ? $r['avatar'] : BASE_URL . $r['avatar'];
              }
            ?>
            <div style="padding:var(--space-5) 0;border-bottom:1px solid var(--border-color)">
              <div style="display:flex;align-items:flex-start;gap:var(--space-3)">
                <!-- Avatar -->
                <div class="navbar-user-avatar" style="width:38px;height:38px;font-size:14px;flex-shrink:0;overflow:hidden">
                  <?php if($avatarUrl): ?>
                  <img src="<?= htmlspecialchars($avatarUrl) ?>" style="width:100%;height:100%;object-fit:cover" alt="<?= htmlspecialchars($r['user_name']) ?>">
                  <?php else: ?>
                  <?= strtoupper(substr($r['user_name'],0,1)) ?>
                  <?php endif; ?>
                </div>
                <div style="flex:1;min-width:0">
                  <div style="display:flex;align-items:center;flex-wrap:wrap;gap:var(--space-2);margin-bottom:var(--space-1)">
                    <strong style="font-size:var(--font-size-sm)"><?= htmlspecialchars($r['user_name']) ?></strong>
                    <div class="hotel-card-rating">
                      <?php for($s=0;$s<5;$s++): ?>
                      <span class="star" style="font-size:11px"><?= $s < $r['rating'] ? '★' : '☆' ?></span>
                      <?php endfor; ?>
                    </div>
                    <span style="color:var(--color-gold);font-weight:700;font-size:var(--font-size-xs)"><?= $r['rating'] ?>.0</span>
                    <small style="color:var(--text-muted);margin-left:auto"><?= formatDate($r['created_at']) ?></small>
                  </div>
                  <?php if($r['comment']): ?>
                  <p style="font-size:var(--font-size-sm);color:var(--text-secondary);line-height:1.6;margin:0">"<?= nl2br(htmlspecialchars($r['comment'])) ?>"</p>
                  <?php else: ?>
                  <p style="font-size:var(--font-size-sm);color:var(--text-muted);font-style:italic;margin:0">Tidak ada komentar.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
          </div>

          <!-- Rooms List -->
          <?php if (!empty($rooms)): ?>
          <div style="margin-top:var(--space-8)">
            <h3 style="font-size:var(--font-size-xl);margin-bottom:var(--space-4)">Tipe Kamar Tersedia</h3>
            <?php foreach($rooms as $r): ?>
            <div style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:var(--radius-lg);padding:var(--space-5);margin-bottom:var(--space-3);display:flex;justify-content:space-between;align-items:center;gap:var(--space-4);flex-wrap:wrap">
              <div>
                <strong style="font-size:var(--font-size-base)"><?= htmlspecialchars($r['type_name']) ?></strong>
                <div style="color:var(--text-muted);font-size:var(--font-size-sm);margin-top:4px">👥 Maks. <?= $r['capacity'] ?> tamu &nbsp;·&nbsp; 🛏 Stok: <?= $r['stock'] ?> kamar</div>
              </div>
              <div style="text-align:right">
                <div style="color:var(--color-gold);font-weight:700;font-size:var(--font-size-lg)"><?= formatCurrency($r['price_per_night']) ?><span style="font-weight:400;font-size:var(--font-size-sm);color:var(--text-muted)"> / malam</span></div>
                <a href="<?= BASE_URL ?>?page=pemesanan&hotel_id=<?= $hotel['id'] ?>&room_id=<?= $r['id'] ?>" class="btn btn-primary btn-sm" style="margin-top:var(--space-2)"><?= __('hotel_detail.book_now') ?></a>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Booking Sidebar -->
      <div class="booking-sidebar">
        <div class="booking-card">
          <div style="font-size:var(--font-size-sm);color:var(--text-muted);margin-bottom:var(--space-2)"><?= __('hotel_detail.price_start') ?></div>
          <div class="booking-card-price"><?= formatCurrency($hotel['price_start']) ?> <span><?= __('home.per_night') ?></span></div>
          <form method="GET" action="<?= BASE_URL ?>" id="quickBookForm">
            <input type="hidden" name="page" value="pemesanan">
            <input type="hidden" name="hotel_id" value="<?= $hotel['id'] ?>">
            <div class="form-group">
              <label class="form-label"><?= __('hotel_detail.check_in') ?></label>
              <input type="date" name="check_in" class="form-input" required min="<?= date('Y-m-d') ?>" id="sidebarCheckIn">
            </div>
            <div class="form-group">
              <label class="form-label"><?= __('hotel_detail.check_out') ?></label>
              <input type="date" name="check_out" class="form-input" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" id="sidebarCheckOut">
            </div>
            <div class="form-group">
              <label class="form-label"><?= __('hotel_detail.guests') ?></label>
              <select name="guests" class="form-select">
                <?php for($g=1;$g<=6;$g++): ?>
                <option value="<?= $g ?>"><?= $g ?> <?= $g == 1 ? 'Tamu' : 'Tamu' ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <?php if (!empty($rooms)): ?>
            <div class="form-group">
              <label class="form-label"><?= __('hotel_detail.select_room') ?></label>
              <select name="room_id" class="form-select" required>
                <?php foreach($rooms as $r): ?>
                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['type_name']) ?> — <?= formatCurrency($r['price_per_night']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary btn-block btn-lg"><?= __('hotel_detail.book_now') ?></button>
          </form>
          <div class="summary-note"><span>⏰</span> <?= __('hotel_detail.payment_note') ?></div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
function setMainImage(src, el) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.detail-gallery-thumbs img').forEach(i => i.classList.remove('active'));
    el.classList.add('active');
}
function showTab(t, el) {
    document.querySelectorAll('.detail-tab-content').forEach(e => e.style.display='none');
    document.querySelectorAll('.detail-tab').forEach(e => e.classList.remove('active'));
    document.getElementById('tab-'+t).style.display = 'block';
    el.classList.add('active');
}
// Fix: update min check-out when check-in changes
const ci = document.getElementById('sidebarCheckIn');
const co = document.getElementById('sidebarCheckOut');
if (ci && co) {
    ci.addEventListener('change', function() {
        if (this.value) {
            const d = new Date(this.value);
            d.setDate(d.getDate() + 1);
            co.min = d.toISOString().split('T')[0];
            if (co.value && co.value <= this.value) co.value = co.min;
        }
    });
}
</script>
