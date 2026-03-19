<?php
$base_url = '../';
$base_url_path = '../';
$titre_page = "Détail article";

require_once '../config.php';
require_once '../entete.php';
require_once '../menu.php';

$pdo = getConnexion();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ../accueil.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT a.*, c.nom AS categorie, u.prenom, u.nom AS auteur_nom
    FROM articles a
    LEFT JOIN categories c ON a.categorie_id = c.id
    LEFT JOIN utilisateurs u ON a.auteur_id = u.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$art = $stmt->fetch();

if (!$art) {
    echo '<main class="container"><p class="alert alert-danger">Article introuvable.</p></main>';
    require_once '../pied.php';
    exit;
}

$titre_page = htmlspecialchars($art['titre']) . ' — Actualités ESP';
?>

<main class="container">
    <a href="../accueil.php" class="btn-retour">← Retour à l'accueil</a>

    <article class="article-detail">
        <div class="article-meta">
            <span class="badge-cat"><?= htmlspecialchars($art['categorie'] ?? 'Non classé') ?></span>
            <span class="article-date"><?= date('d/m/Y à H:i', strtotime($art['date_publication'])) ?></span>
        </div>

        <h1><?= htmlspecialchars($art['titre']) ?></h1>

        <p class="auteur-detail">Par <strong><?= htmlspecialchars($art['prenom'] . ' ' . $art['auteur_nom']) ?></strong></p>

        <?php if (!empty($art['image']) && file_exists('../uploads/' . $art['image'])): ?>
            <img src="../uploads/<?= htmlspecialchars($art['image']) ?>" alt="illustration" class="img-detail">
        <?php endif; ?>

        <div class="contenu-article">
            <?= nl2br(htmlspecialchars($art['contenu'])) ?>
        </div>

        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['editeur','administrateur'])): ?>
        <div class="actions-article">
            <a href="modifier.php?id=<?= $art['id'] ?>" class="btn-edit">✏️ Modifier</a>
            <a href="supprimer.php?id=<?= $art['id'] ?>" class="btn-del"
               onclick="return confirm('Supprimer cet article ?')">🗑️ Supprimer</a>
        </div>
        <?php endif; ?>
    </article>
</main>

<?php require_once '../pied.php'; ?>
