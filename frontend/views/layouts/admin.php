<?php
/**
 * Admin Layout — Dashboard Pages
 */
$adminPage = $adminPage ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="<?= getLang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin — ' . APP_NAME ?></title>
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/variables.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/base.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/components.css">
    <link rel="stylesheet" href="<?= ASSET_URL ?>/css/admin.css">
    <?php if ($adminPage === 'pendapatan'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>
</head>
<body>
    <div class="admin-layout">
        <?php include FRONTEND_PATH . '/views/components/admin-sidebar.php'; ?>

        <div class="admin-main">
            <header class="admin-header">
                <div>
                    <h1><?= $pageTitle ?? __('admin.dashboard') ?></h1>
                </div>
                <div class="admin-header-actions">
                    <div class="lang-switcher">
                        <a href="?page=admin/<?= $adminPage ?>&lang=id" class="<?= getLang()==='id'?'active':'' ?>">ID</a>
                        <a href="?page=admin/<?= $adminPage ?>&lang=en" class="<?= getLang()==='en'?'active':'' ?>">EN</a>
                    </div>
                    <div class="navbar-user" onclick="this.querySelector('.navbar-dropdown').classList.toggle('show')">
                        <div class="navbar-user-avatar"><?= strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)) ?></div>
                        <span style="font-size:var(--font-size-sm)"><?= $_SESSION['user_name'] ?? 'Admin' ?></span>
                        <div class="navbar-dropdown">
                            <a href="?page=admin/pengaturan"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg> <?= __('admin.settings') ?></a>
                            <a href="?page=logout" style="color:var(--status-danger)"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> <?= __('admin.logout') ?></a>
                        </div>
                    </div>
                </div>
            </header>

            <?php
            $flash = getFlash();
            if ($flash): ?>
            <div class="flash-message flash-<?= $flash['type'] ?>" id="flashMessage" style="position:relative;top:auto;right:auto;margin:var(--space-4) var(--space-8) 0;">
                <span><?= $flash['message'] ?></span>
                <span class="flash-close" onclick="this.parentElement.remove()">✕</span>
            </div>
            <?php endif; ?>

            <div class="admin-content">
                <?php
                $viewPath = FRONTEND_PATH . '/views/admin/' . $adminPage . '.php';
                if (file_exists($viewPath)) {
                    include $viewPath;
                }
                ?>
            </div>
        </div>
    </div>

    <script src="<?= ASSET_URL ?>/js/app.js"></script>
    <script src="<?= ASSET_URL ?>/js/admin.js"></script>
    <script>
        setTimeout(() => {
            const f = document.getElementById('flashMessage');
            if (f) f.style.display = 'none';
        }, 5000);
    </script>
</body>
</html>
