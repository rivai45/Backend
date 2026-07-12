<?php
/**
 * Main App Layout — Client-Facing Pages
 * Includes: navbar + page content + footer
 */
$currentPage = $page ?? 'home';
?>
<!DOCTYPE html>
<html lang="<?= getLang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lynvaii — <?= __('footer.about') ?>">
    <meta name="base-url" content="<?= BASE_URL ?>">
    <title><?= $pageTitle ?? APP_NAME ?></title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/variables.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/base.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/components.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/pages.css">
</head>
<body>
    <?php include FRONTEND_PATH . '/views/components/navbar.php'; ?>

    <?php
    // Flash messages
    $flash = getFlash();
    if ($flash): ?>
    <div class="flash-message flash-<?= $flash['type'] ?>" id="flashMessage">
        <span><?= $flash['message'] ?></span>
        <span class="flash-close" onclick="this.parentElement.remove()">✕</span>
    </div>
    <?php endif; ?>

    <main>
    <?php
    $viewFile = $currentView ?? $currentPage;
    $viewPath = FRONTEND_PATH . '/views/' . $viewFile . '.php';
    if (file_exists($viewPath)) {
        include $viewPath;
    } else {
        include FRONTEND_PATH . '/views/home.php';
    }
    ?>
    </main>

    <?php include FRONTEND_PATH . '/views/components/footer.php'; ?>

    <script src="<?= ASSET_URL ?>/js/app.js"></script>
</body>
</html>
