<?php
/**
 * Main Page - News
 * Version: 3.0
 */

require_once 'config.php';

$db = matos_connexion();

$pageTitle = "News - Home";

// Get filtres
$categoryId = filter_input(INPUT_GET, 'categorie_id', FILTER_VALIDATE_INT) ?? 0;
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;

$page = ($page < 1) ? 1 : $page;
$limit = NB_MAX_ARTICLES;
$offset = ($page - 1) * $limit;

//  total articles
if ($categoryId > 0) {
    $countStmt = $db->prepare("SELECT COUNT(*) FROM articles WHERE categorie_id = :category");
    $countStmt->execute(['category' => $categoryId]);
} else {
    $countStmt = $db->query("SELECT COUNT(*) FROM articles");
}

$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $limit);


$sql = "
    SELECT 
        a.id,
        a.titre,
        a.description_courte,
        a.image,
        a.date_publication,
        c.nom AS category_name,
        u.prenom AS author_firstname,
        u.nom AS author_lastname
    FROM articles a
    LEFT JOIN categories c ON a.categorie_id = c.id
    LEFT JOIN utilisateurs u ON a.auteur_id = u.id
";

$params = [];

if ($categoryId > 0) {
    $sql .= " WHERE a.categorie_id = :category";
    $params['category'] = $categoryId;
}

$sql .= " ORDER BY a.date_publication DESC LIMIT :offset, :limit";

$stmt = $db->prepare($sql);


if ($categoryId > 0) {
    $stmt->bindValue(':category', $categoryId, PDO::PARAM_INT);
}
$stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);


$sectionTitle = "All News";
if ($categoryId > 0 && !empty($articles)) {
    $sectionTitle = $articles[0]['category_name'];
}

include 'entete.php';
include 'menu.php';
?>

<main class="container">
    <div class="page-header">
        <h1><?= htmlspecialchars($sectionTitle) ?></h1>
        <p class="text-muted">Latest updates and announcements.</p>
    </div>

    <?php if (empty($articles)): ?>
        <div class="empty-state">
            <p>No content available.</p>
        </div>

    <?php else: ?>
        <div class="articles-container">
            <?php foreach ($articles as $article): ?>
                <div class="article-card">

                    <div class="article-img-wrapper">
                        <?php if (!empty($article['image'])): ?>
                            <img src="uploads/<?= htmlspecialchars($article['image']) ?>" class="article-img" alt="">
                        <?php else: ?>
                            <div class="placeholder-img"></div>
                        <?php endif; ?>
                    </div>

                    <div class="article-body">
                        <span class="badge-cat">
                            <?= htmlspecialchars($article['category_name']) ?>
                        </span>

                        <h2 class="article-title">
                            <a href="articles/detail.php?id=<?= (int)$article['id'] ?>">
                                <?= htmlspecialchars($article['titre']) ?>
                            </a>
                        </h2>

                        <p class="article-desc">
                            <?= htmlspecialchars(mb_strimwidth($article['description_courte'], 0, 150, '...')) ?>
                        </p>

                        <div class="article-footer">
                            <span>
                                By <strong>
                                    <?= htmlspecialchars($article['author_firstname'] . ' ' . $article['author_lastname']) ?>
                                </strong>
                            </span>
                            <span>
                                <?= date('d M Y', strtotime($article['date_publication'])) ?>
                            </span>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?><?= $categoryId ? '&categorie_id=' . $categoryId : '' ?>"
                       class="<?= ($i === $page) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</main>

<?php include 'pied.php'; ?>