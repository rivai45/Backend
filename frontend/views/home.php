<?php /** Home Page — Matching Figma Design */ ?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-image">
    <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=1600&q=80" alt="Luxury Hotel">
  </div>
  <div class="hero-overlay"></div>
  <div class="hero-content animate-fadeInUp">
    <span class="hero-subtitle"><?= __('hero.subtitle') ?></span>
    <h1 class="hero-title">
      <?= __('hero.title') ?><br>
      <span class="highlight"><?= __('hero.title_highlight') ?></span>
    </h1>
    <p class="hero-description"><?= __('hero.description') ?></p>
    <a href="<?= BASE_URL ?>?page=destinasi" class="hero-cta"><?= __('hero.cta') ?></a>
  </div>
</section>

<!-- Best Picks Section -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <div>
        <h2 class="section-title">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="var(--color-gold)"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
          <?= __('home.best_picks') ?>
        </h2>
        <p class="section-subtitle"><?= __('home.best_picks_sub') ?></p>
      </div>
    </div>
    <div class="hotel-grid hotel-grid-3">
      <?php
      $images = [
        'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=80',
        'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=600&q=80',
        'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=600&q=80',
      ];
      foreach (($featuredHotels ?? []) as $i => $hotel): ?>
      <div class="hotel-card animate-fadeInUp" style="animation-delay: <?= $i * 0.1 ?>s">
        <div class="hotel-card-image">
          <img src="<?= $images[$i % count($images)] ?>" alt="<?= htmlspecialchars($hotel['name']) ?>">
        </div>
        <div class="hotel-card-body">
          <div class="hotel-card-rating">
            <?php for($s=0;$s<5;$s++): ?><span class="star"><?= $s < round($hotel['rating']) ? '★' : '☆' ?></span><?php endfor; ?>
          </div>
          <h3 class="hotel-card-name"><?= htmlspecialchars($hotel['name']) ?></h3>
          <div class="hotel-card-location">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
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
  </div>
</section>

<!-- Popular Destinations Section -->
<section class="section" style="background:var(--bg-secondary)">
  <div class="container">
    <div class="section-header">
      <div>
        <h2 class="section-title">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="var(--color-gold)"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
          <?= __('home.popular') ?>
        </h2>
        <p class="section-subtitle"><?= __('home.popular_sub') ?></p>
      </div>
      <a href="<?= BASE_URL ?>?page=destinasi" class="section-link">
        <?= __('home.view_all') ?>
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
      </a>
    </div>
    <div class="hotel-grid">
      <?php
      $popImages = [
        'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600&q=80',
        'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=600&q=80',
        'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600&q=80',
        'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?w=600&q=80',
      ];
      foreach (($popularHotels ?? []) as $i => $hotel): ?>
      <div class="hotel-card animate-fadeInUp" style="animation-delay: <?= $i * 0.1 ?>s">
        <div class="hotel-card-image">
          <img src="<?= $popImages[$i % count($popImages)] ?>" alt="<?= htmlspecialchars($hotel['name']) ?>">
        </div>
        <div class="hotel-card-body">
          <div class="hotel-card-rating">
            <?php for($s=0;$s<5;$s++): ?><span class="star"><?= $s < round($hotel['rating']) ? '★' : '☆' ?></span><?php endfor; ?>
          </div>
          <h3 class="hotel-card-name"><?= htmlspecialchars($hotel['name']) ?></h3>
          <div class="hotel-card-location">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
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
  </div>
</section>
