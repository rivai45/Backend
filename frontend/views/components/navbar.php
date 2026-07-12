<?php /** Navbar Component */ $currentPage = $page ?? 'home'; ?>
<nav class="navbar" id="navbar">
  <div class="navbar-inner">
    <a href="<?= BASE_URL ?>" class="navbar-brand">
      <svg viewBox="0 0 24 24" fill="currentColor"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm14 3c0 .6-.4 1-1 1H6c-.6 0-1-.4-1-1v-1h14v1z"/></svg>
      <?= APP_NAME ?>
    </a>
    <div class="navbar-menu" id="navMenu">
      <a href="<?= BASE_URL ?>" class="<?= $currentPage==='home'?'active':'' ?>"><?= __('nav.home') ?></a>
      <a href="<?= BASE_URL ?>?page=destinasi" class="<?= $currentPage==='destinasi'?'active':'' ?>"><?= __('nav.destinations') ?></a>
      <a href="<?= BASE_URL ?>?page=<?= isLoggedIn()?'my-bookings':'pemesanan' ?>" class="<?= in_array($currentPage,['pemesanan','my-bookings'])?'active':'' ?>"><?= __('nav.bookings') ?></a>
    </div>
    <div class="navbar-actions">
      <div class="navbar-search">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" placeholder="<?= __('nav.search_placeholder') ?>" id="navSearch">
      </div>
      <div class="lang-switcher">
        <a href="?page=<?= $currentPage ?>&lang=id" class="<?= getLang()==='id'?'active':'' ?>">ID</a>
        <a href="?page=<?= $currentPage ?>&lang=en" class="<?= getLang()==='en'?'active':'' ?>">EN</a>
      </div>
      <?php if (isLoggedIn()): ?>
      <div class="navbar-user" onclick="this.querySelector('.navbar-dropdown').classList.toggle('show')">
        <div class="navbar-user-avatar"><?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?></div>
        <div class="navbar-dropdown">
          <a href="<?= BASE_URL ?>?page=profil"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Profil Saya</a>
          <a href="<?= BASE_URL ?>?page=my-bookings"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> <?= __('nav.my_bookings') ?></a>
          <a href="<?= BASE_URL ?>?page=logout"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> <?= __('nav.logout') ?></a>
        </div>
      </div>
      <?php else: ?>
      <a href="<?= BASE_URL ?>?page=login" class="btn-login">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <?= __('nav.login') ?>
      </a>
      <?php endif; ?>
      <div class="navbar-toggle" id="navToggle" onclick="document.getElementById('navMenu').classList.toggle('show')">
        <span></span><span></span><span></span>
      </div>
    </div>
  </div>
</nav>
