<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titre_page) ? htmlspecialchars($titre_page) : 'Actualités ESP' ?></title>
    <link rel="stylesheet" href="<?= $base_url ?? '' ?>assets/css/style.css?v=2">
</head>
<body>
<header>
    <div class="header-inner">
        <a href="<?= $base_url ?? '' ?>accueil.php" class="site-titre">📰 Actualités ESP</a>
        <span class="header-sub">École Supérieure Polytechnique</span>
    </div>
</header>
