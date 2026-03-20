<?php
/**
 * Menu principal avec gestion des droits utilisateurs
 */

require_once (isset($chemin_racine) ? $chemin_racine : '') . 'config.php';

// Infos session
$roleUtilisateur = $_SESSION['role'] ?? 'invite';
$estConnecte = !empty($_SESSION['user_id']);
$prenomUtilisateur = $_SESSION['prenom'] ?? '';

// Connexion DB pour les catégories
$db = matos_connexion();
$categories = $db->query("SELECT id, nom FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

?>

<nav class="navbar">
    <button class="hamburger" id="toggleMobileMenu">
        <span></span><span></span><span></span>
    </button>

    <ul id="mainNav">
        <li>
            <a href="<?= $chemin_racine ?? '' ?>accueil.php">Accueil</a>
        </li>

        <!-- Catégories -->
        <li class="dropdown">
            <a href="#">Catégories ▾</a>
            <ul class="dropdown-menu">

                <?php foreach ($categories as $cat): ?>
                    <li>
                        <a href="<?= ($chemin_racine ?? '') . 'accueil.php?categorie_id=' . (int)$cat['id'] ?>">
                            <?= htmlspecialchars($cat['nom']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>

                <?php if ($estConnecte && in_array($roleUtilisateur, ['administrateur', 'editeur'])): ?>
                    <li class="dropdown-divider"></li>
                    <li><a href="<?= $chemin_racine ?? '' ?>categories/ajouter.php">Ajouter</a></li>
                    <li><a href="<?= $chemin_racine ?? '' ?>categories/liste.php">Gérer</a></li>
                <?php endif; ?>

            </ul>
        </li>

        <!-- Rédaction -->
        <?php if ($estConnecte && in_array($roleUtilisateur, ['administrateur', 'editeur'])): ?>
            <li class="dropdown">
                <a href="#">Rédaction ▾</a>
                <ul class="dropdown-menu">
                    <li><a href="<?= $chemin_racine ?? '' ?>articles/ajouter.php">Nouvel article</a></li>
                    <li><a href="<?= $chemin_racine ?? '' ?>articles/liste.php">Tous les articles</a></li>
                </ul>
            </li>
        <?php endif; ?>

        <!-- Admin -->
        <?php if ($estConnecte && $roleUtilisateur === 'administrateur'): ?>
            <li class="dropdown">
                <a href="#">Administration ▾</a>
                <ul class="dropdown-menu">
                    <li><a href="<?= $chemin_racine ?? '' ?>utilisateurs/ajouter.php">Créer un compte</a></li>
                    <li><a href="<?= $chemin_racine ?? '' ?>utilisateurs/liste.php">Utilisateurs</a></li>
                </ul>
            </li>
        <?php endif; ?>
    </ul>

    <!-- Zone utilisateur -->
    <div class="nav-auth">
        <?php if ($estConnecte): ?>
            <span class="user-info">
                Bonjour, <strong><?= htmlspecialchars($prenomUtilisateur) ?></strong>
            </span>
            <a href="<?= $chemin_racine ?? '' ?>deconnexion.php" class="btn-deco">Déconnexion</a>
        <?php else: ?>
            <?php if (!isset($hide_connexion_btn)): ?>
                <a href="<?= $chemin_racine ?? '' ?>connexion.php" class="btn-co">Connexion</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</nav>

<script>
// Gestion simple du menu mobile
document.addEventListener('DOMContentLoaded', function () {
    const btnMenu = document.getElementById('toggleMobileMenu');
    const nav = document.getElementById('mainNav');

    if (btnMenu && nav) {
        btnMenu.addEventListener('click', function () {
            const isVisible = nav.style.display === 'flex';
            nav.style.display = isVisible ? 'none' : 'flex';
            btnMenu.classList.toggle('active');
        });
    }
});
</script>