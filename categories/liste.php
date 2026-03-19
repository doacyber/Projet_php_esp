<?php
$base_url = '../';
$base_url_path = '../';
$titre_page = "Gestion des catégories";

require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['editeur','administrateur'])) {
    header('Location: ../connexion.php');
    exit;
}

require_once '../entete.php';
require_once '../menu.php';

$pdo = getConnexion();
$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

$cats = $pdo->query("
    SELECT c.id, c.nom, c.description, COUNT(a.id) AS nb_articles
    FROM categories c
    LEFT JOIN articles a ON a.categorie_id = c.id
    GROUP BY c.id
    ORDER BY c.nom
")->fetchAll();
?>

<main class="container">
    <div class="page-header">
        <h1>Catégories</h1>
        <a href="ajouter.php" class="btn-ajouter">+ Nouvelle catégorie</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (empty($cats)): ?>
        <p>Aucune catégorie pour l'instant.</p>
    <?php else: ?>
    <div class="table-responsive">
    <table class="table-admin">
        <thead>
            <tr><th>#</th><th>Nom</th><th>Description</th><th>Nb articles</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($cats as $c): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['nom']) ?></td>
                <td><?= htmlspecialchars($c['description'] ?? '') ?></td>
                <td><?= $c['nb_articles'] ?></td>
                <td class="td-actions">
                    <a href="modifier.php?id=<?= $c['id'] ?>" class="btn-edit">Modifier</a>
                    <a href="supprimer.php?id=<?= $c['id'] ?>" class="btn-del"
                       onclick="return confirm('Supprimer cette catégorie ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</main>

<?php require_once '../pied.php'; ?>
