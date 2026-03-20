<?php
$chemin_racine = '../';
require_once '../config.php';
session_start(); // On démarre la session pour gérer les rôles et droits

// ===== Vérification de l'ID de l'article =====
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($article_id <= 0) {
    // Retour à l'accueil si ID invalide
    header('Location: ../accueil.php');
    exit;
}

$bdd = matos_connexion();

// ===== Requête pour récupérer l'article avec catégorie et auteur =====
$sql_article = "
    SELECT a.*, c.nom AS nom_categorie, u.prenom, u.nom AS nom_auteur
    FROM articles a
    LEFT JOIN categories c ON a.categorie_id = c.id
    LEFT JOIN utilisateurs u ON a.auteur_id = u.id
    WHERE a.id = :id
";
$req_article = $bdd->prepare($sql_article);
$req_article->execute(['id' => $article_id]);
$article = $req_article->fetch();

if (!$article) {
    die("Désolé, cet article n'existe plus ou a été supprimé.");
}

$page_titre = htmlspecialchars($article['titre']) . " - ESP Actu";

include '../entete.php';
include '../menu.php';
?>

<main class="container">

    <!-- Lien retour aux actus -->
    <div style="margin-bottom:2rem;">
        <a href="../accueil.php" class="btn-retour" style="color:var(--accent); font-weight:600">
            ← Revenir aux actualités
        </a>
    </div>

    <!-- Article complet -->
    <article class="news-full-view" style="background:var(--card-bg); border:1px solid var(--border); padding:3rem; border-radius:12px;">

        <!-- Catégorie + date -->
        <div style="margin-bottom:1.5rem">
            <span class="badge-cat"><?= htmlspecialchars($article['nom_categorie']) ?></span>
            <span style="color:var(--text-dim); margin-left:1rem; font-size:0.85rem">
                Publié le <?= date('d/m/Y', strtotime($article['date_publication'])) ?>
            </span>
        </div>

        <!-- Titre de l'article -->
        <h1 style="font-size:2.8rem; font-weight:900; line-height:1.2; margin-bottom:1.5rem;">
            <?= htmlspecialchars($article['titre']) ?>
        </h1>

        <!-- Auteur -->
        <p style="color:var(--accent); margin-bottom:2rem; font-size:0.9rem; text-transform:uppercase; letter-spacing:1px;">
            Par <strong><?= htmlspecialchars($article['prenom'] . ' ' . $article['nom_auteur']) ?></strong>
        </p>

        <!-- Image si présente -->
        <?php if ($article['image']): ?>
            <div style="margin-bottom:2.5rem">
                <img src="../uploads/<?= htmlspecialchars($article['image']) ?>" 
                     style="width:100%; max-height:500px; object-fit:cover; border-radius:8px;" 
                     alt="<?= htmlspecialchars($article['titre']) ?>">
            </div>
        <?php endif; ?>

        <!-- Contenu de l'article -->
        <div class="article-content" style="line-height:1.8; font-size:1.1rem; color:#e2e8f0; white-space:pre-wrap;">
            <?= htmlspecialchars($article['contenu']) ?>
        </div>

        <!-- Actions éditables si rôle autorisé -->
        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['administrateur','editeur'])): ?>
            <div style="margin-top:4rem; padding-top:2rem; border-top:1px solid var(--border); display:flex; gap:1rem;">
                <a href="modifier.php?id=<?= $article['id'] ?>" class="btn-edit" 
                   style="background:#4b5563; color:#fff !important; padding:0.8rem 1.5rem; border-radius:4px;">
                    Éditer
                </a>
                <a href="supprimer.php?id=<?= $article['id'] ?>" class="btn-del"
                   onclick="return confirm('Sûr de vouloir supprimer cet article ?');"
                   style="background:#b91c1c; color:#fff !important; padding:0.8rem 1.5rem; border-radius:4px;">
                    Supprimer
                </a>
            </div>
        <?php endif; ?>

    </article>
</main>

<?php include '../pied.php'; ?>