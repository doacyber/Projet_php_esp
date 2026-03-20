<?php
// On lance la session direct
if (!session_id()) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_titre) ? $page_titre : 'JAKARLO ESP'; ?></title>
    
    <!-- Nouveau style premium Noir & Ambre -->
    <link rel="stylesheet" href="<?php echo (isset($chemin_racine) ? $chemin_racine : ''); ?>assets/css/style.css?v=<?php echo time(); ?>">
    
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
</head>
<body>

<header>
    <div class="header-inner">
        <a href="<?php echo (isset($chemin_racine) ? $chemin_racine : ''); ?>accueil.php" class="site-titre">
            JAKARLO <span style="color:var(--accent)">ESP</span>
        </a>
        <div class="header-sub">Portail de l'École Supérieure Polytechnique</div>
    </div>
</header>
