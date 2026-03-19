<?php
$base_url = '';
$base_url_path = '';
$titre_page = "Accueil — Actualités ESP";

require_once 'config.php';
require_once 'entete.php';
require_once 'menu.php';

$pdo = getConnexion();

$cat_id = isset($_GET['categorie_id']) ? (int)$_GET['categorie_id'] : 0;

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * ARTICLES_PAR_PAGE;

if ($cat_id > 0) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE categorie_id = ?");
    $stmt->execute([$cat_id]);
} else {
    $stmt = $pdo->query("SELECT COUNT(*) FROM articles");
}
$total = $stmt->fetchColumn();
$nb_pages = (int)ceil($total / ARTICLES_PAR_PAGE);

if ($cat_id > 0) {
    $req = $pdo->prepare("
        SELECT a.id, a.titre, a.description_courte, a.date_publication, a.image,
               c.nom AS categorie, u.prenom, u.nom AS auteur_nom
        FROM articles a
        LEFT JOIN categories c ON a.categorie_id = c.id
        LEFT JOIN utilisateurs u ON a.auteur_id = u.id
        WHERE a.categorie_id = ?
        ORDER BY a.date_publication DESC
        LIMIT " . ARTICLES_PAR_PAGE . " OFFSET " . $offset
    );
    $req->execute([$cat_id]);
} else {
    $req = $pdo->query("
        SELECT a.id, a.titre, a.description_courte, a.date_publication, a.image,
               c.nom AS categorie, u.prenom, u.nom AS auteur_nom
        FROM articles a
        LEFT JOIN categories c ON a.categorie_id = c.id
        LEFT JOIN utilisateurs u ON a.auteur_id = u.id
        ORDER BY a.date_publication DESC
        LIMIT " . ARTICLES_PAR_PAGE . " OFFSET " . $offset
    );
}
$articles = $req->fetchAll();

$nom_cat = '';
if ($cat_id > 0) {
    $sc = $pdo->prepare("SELECT nom FROM categories WHERE id = ?");
    $sc->execute([$cat_id]);
    $row = $sc->fetch();
    $nom_cat = $row ? $row['nom'] : '';
}
?>

<main class="container">
    <div class="page-header">
        <?php if ($nom_cat): ?>
            <h1>Catégorie : <?= htmlspecialchars($nom_cat) ?></h1>
            <a href="accueil.php" class="btn-retour">← Tous les articles</a>
        <?php else: ?>
            <h1>Dernières actualités</h1>
        <?php endif; ?>
    </div>

    <?php if (empty($articles)): ?>
        <p class="msg-vide">Aucun article pour le moment.</p>
    <?php else: ?>

    <div class="articles-liste">
        <?php foreach ($articles as $art): ?>
        <div class="article-card">
            <?php if (!empty($art['image']) && file_exists('uploads/' . $art['image'])): ?>
                <img src="uploads/<?= htmlspecialchars($art['image']) ?>" alt="" class="article-img">
            <?php endif; ?>
            <div class="article-body">
                <div class="article-meta">
                    <span class="badge-cat"><?= htmlspecialchars($art['categorie'] ?? 'Non classé') ?></span>
                    <span class="article-date"><?= date('d/m/Y', strtotime($art['date_publication'])) ?></span>
                </div>
                <h2 class="article-titre">
                    <a href="articles/detail.php?id=<?= $art['id'] ?>">
                        <?= htmlspecialchars($art['titre']) ?>
                    </a>
                </h2>
                <p class="article-desc"><?= htmlspecialchars($art['description_courte']) ?></p>
                <div class="article-footer">
                    <span class="auteur">Par <?= htmlspecialchars($art['prenom'] . ' ' . $art['auteur_nom']) ?></span>
                    <a href="articles/detail.php?id=<?= $art['id'] ?>" class="lire-plus">Lire la suite →</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($nb_pages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page-1 ?><?= $cat_id ? '&categorie_id='.$cat_id : '' ?>" class="btn-page">← Précédent</a>
        <?php endif; ?>
        <span class="page-info">Page <?= $page ?> / <?= $nb_pages ?></span>
        <?php if ($page < $nb_pages): ?>
            <a href="?page=<?= $page+1 ?><?= $cat_id ? '&categorie_id='.$cat_id : '' ?>" class="btn-page">Suivant →</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</main>

<?php require_once 'pied.php'; ?>
