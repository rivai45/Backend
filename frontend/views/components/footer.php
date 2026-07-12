<?php /** Footer Component */ ?>
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="footer-brand">
          <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
          <?= APP_NAME ?>
        </div>
        <p class="footer-about"><?= __('footer.about') ?></p>
      </div>
      <div>
        <h4 class="footer-title"><?= __('footer.destinations') ?></h4>
        <div class="footer-links">
          <a href="<?= BASE_URL ?>?page=destinasi&city=Jakarta">Jakarta</a>
          <a href="<?= BASE_URL ?>?page=destinasi&city=Bali">Bali</a>
          <a href="<?= BASE_URL ?>?page=destinasi&city=Bandung">Bandung</a>
          <a href="<?= BASE_URL ?>?page=destinasi&city=Tasikmalaya">Tasikmalaya</a>
        </div>
      </div>
      <div>
        <h4 class="footer-title"><?= __('footer.support') ?></h4>
        <div class="footer-links">
          <a href="#"><?= __('footer.support_247') ?></a>
          <a href="#"><?= __('footer.faq') ?></a>
          <a href="#"><?= __('footer.security') ?></a>
          <a href="#"><?= __('footer.privacy') ?></a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p><?= __('footer.copyright', ['year' => date('Y')]) ?></p>
    </div>
  </div>
</footer>
