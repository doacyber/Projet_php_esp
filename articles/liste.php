<?php
$base_url = '../';
$base_url_path = '../';
$titre_page = "Gestion des articles";

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

$articles = $pdo->query("
    SELECT a.id, a.titre, a.date_publication,
           c.nom AS categorie,
           u.prenom, u.nom AS auteur_nom
    FROM articles a
    LEFT JOIN categories c ON a.categorie_id = c.id
    LEFT JOIN utilisateurs u ON a.auteur_id = u.id
    ORDER BY a.date_publication DESC
")->fetchAll();
?>

<main class="container">
    <div class="page-header">
        <h1>Gestion des articles</h1>
        <a href="ajouter.php" class="btn-ajouter">+ Nouvel article</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <?php if (empty($articles)): ?>
        <p class="msg-vide">Aucun article pour l'instant.</p>
    <?php else: ?>
    <div class="table-responsive">
    <table class="table-admin">
        <thead>
            <tr>
                <th>#</th>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Auteur</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($articles as $a): ?>
            <tr>
                <td><?= $a['id'] ?></td>
                <td><?= htmlspecialchars($a['titre']) ?></td>
                <td><?= htmlspecialchars($a['categorie'] ?? '-') ?></td>
                <td><?= htmlspecialchars($a['prenom'] . ' ' . $a['auteur_nom']) ?></td>
                <td><?= date('d/m/Y', strtotime($a['date_publication'])) ?></td>
                <td class="td-actions">
                    <a href="modifier.php?id=<?= $a['id'] ?>" class="btn-edit">Modifier</a>
                    <a href="supprimer.php?id=<?= $a['id'] ?>" class="btn-del"
                       onclick="return confirm('Supprimer cet article ?')">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</main>

<?php require_once '../pied.php'; ?>
