<?php
require_once $base_url_path . 'config.php';

$role = $_SESSION['role'] ?? 'visiteur';
$connecte = isset($_SESSION['user_id']);
?>
<nav class="navbar">
    <button class="hamburger" id="hamburgerBtn" aria-label="Menu" aria-expanded="false">
        <span></span><span></span><span></span>
    </button>

    <ul id="navMenu">
        <li><a href="<?= $base_url ?? '' ?>accueil.php">Accueil</a></li>

        <li class="dropdown">
            <a href="#">Catégories ▾</a>
            <ul class="dropdown-menu">
                <?php
                $pdo = getConnexion();
                $cats = $pdo->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll();
                foreach ($cats as $c) {
                    echo '<li><a href="'. ($base_url ?? '') .'accueil.php?categorie_id='. $c['id'] .'">'. htmlspecialchars($c['nom']) .'</a></li>';
                }
                ?>
                <?php if ($connecte && in_array($role, ['editeur','administrateur'])): ?>
                <li class="dropdown-divider"></li>
                <li><a href="<?= $base_url ?? '' ?>categories/ajouter.php">➕ Ajouter</a></li>
                <li><a href="<?= $base_url ?? '' ?>categories/liste.php">⚙️ Gérer</a></li>
                <?php endif; ?>
            </ul>
        </li>

        <?php if ($connecte && in_array($role, ['editeur','administrateur'])): ?>
        <li class="dropdown">
            <a href="#">Articles ▾</a>
            <ul class="dropdown-menu">
                <li><a href="<?= $base_url ?? '' ?>articles/ajouter.php">➕ Ajouter un article</a></li>
                <li><a href="<?= $base_url ?? '' ?>articles/liste.php">⚙️ Gérer les articles</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <?php if ($connecte && $role === 'administrateur'): ?>
        <li class="dropdown">
            <a href="#">Utilisateurs ▾</a>
            <ul class="dropdown-menu">
                <li><a href="<?= $base_url ?? '' ?>utilisateurs/ajouter.php">➕ Ajouter</a></li>
                <li><a href="<?= $base_url ?? '' ?>utilisateurs/liste.php">⚙️ Gérer</a></li>
            </ul>
        </li>
        <?php endif; ?>
    </ul>

    <div class="nav-auth">
        <?php if ($connecte): ?>
            <span class="user-info">
                👤 <?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?>
                <em>(<?= htmlspecialchars($role) ?>)</em>
            </span>
            <a href="<?= $base_url ?? '' ?>deconnexion.php" class="btn-deco">Déconnexion</a>
        <?php else: ?>
            <?php if (!isset($page_connexion)): ?>
            <a href="<?= $base_url ?? '' ?>connexion.php" class="btn-co">Connexion</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</nav>

<script>
(function() {
    var btn = document.getElementById('hamburgerBtn');
    var menu = document.getElementById('navMenu');
    if (btn && menu) {
        btn.addEventListener('click', function() {
            var expanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !expanded);
            menu.classList.toggle('nav-open');
            this.classList.toggle('active');
        });
    }
})();
</script>
