<?php /** Destinasi Page */ ?>
<div class="dest-header">
  <div class="container">
    <h1><?= __('destinations.title') ?></h1>
    <p class="section-subtitle"><?= __('destinations.subtitle') ?></p>
    <form class="dest-search" method="GET" action="<?= BASE_URL ?>">
      <input type="hidden" name="page" value="destinasi">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input type="text" name="search" placeholder="<?= __('destinations.search') ?>" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
    </form>
    <div class="dest-filters">
      <a href="<?= BASE_URL ?>?page=destinasi" class="dest-filter-btn <?= empty($_GET['city'])?'active':'' ?>"><?= __('destinations.filter_all') ?></a>
      <?php foreach (($cities ?? []) as $city): ?>
      <a href="<?= BASE_URL ?>?page=destinasi&city=<?= urlencode($city) ?>" class="dest-filter-btn <?= ($_GET['city']??'')===$city?'active':'' ?>"><?= htmlspecialchars($city) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<section class="section" style="padding-top:0">
  <div class="container">
    <?php if (empty($hotels)): ?>
    <div class="empty-state"><p><?= __('destinations.no_results') ?></p></div>
    <?php else: ?>
    <div class="hotel-grid">
      <?php
      // FIX #10: Fallback images jika hotel belum punya thumbnail di DB
      $fallbackImgs = [
        'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=80',
        'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=600&q=80',
        'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=600&q=80',
        'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600&q=80',
        'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=600&q=80',
        'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600&q=80',
        'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=600&q=80',
        'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=600&q=80',
      ];
      foreach ($hotels as $i => $hotel):
        // Prioritas: thumbnail dari DB → fallback Unsplash
        if (!empty($hotel['thumbnail'])) {
          $imgSrc = str_starts_with($hotel['thumbnail'], 'http')
            ? $hotel['thumbnail']
            : BASE_URL . ltrim($hotel['thumbnail'], '/');
        } else {
          $imgSrc = $fallbackImgs[$i % count($fallbackImgs)];
        }
      ?>
      <div class="hotel-card animate-fadeInUp" style="animation-delay:<?= $i*0.05 ?>s">
        <div class="hotel-card-image">
          <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>">
        </div>
        <div class="hotel-card-body">
          <div class="hotel-card-rating"><?php for($s=0;$s<5;$s++): ?><span class="star"><?= $s<round($hotel['rating'])?'★':'☆' ?></span><?php endfor; ?></div>
          <h3 class="hotel-card-name"><?= htmlspecialchars($hotel['name']) ?></h3>
          <div class="hotel-card-location">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <?= htmlspecialchars($hotel['city']) ?>, Indonesia
          </div>
          <div class="hotel-card-footer">
            <span class="hotel-card-price"><?= formatCurrency($hotel['price_start']) ?> <span><?= __('home.per_night') ?></span></span>
            <a href="<?= BASE_URL ?>?page=hotel&id=<?= $hotel['id'] ?>" class="btn-detail"><?= __('home.view_detail') ?></a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php if (($totalPages ?? 1) > 1): ?>
    <div class="pagination">
      <?php for($p=1; $p<=$totalPages; $p++): ?>
      <a href="<?= BASE_URL ?>?page=destinasi&p=<?= $p ?>&city=<?= urlencode($_GET['city']??'') ?>&search=<?= urlencode($_GET['search']??'') ?>" class="<?= $p==($currentPage??1)?'active':'' ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
