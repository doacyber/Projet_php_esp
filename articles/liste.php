<?php
$chemin_racine = '../';
require_once '../config.php';
session_start();

// ===== Vérification des droits =====
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['editeur', 'administrateur'])) {
    header('Location: ../connexion.php');
    exit;
}

$page_titre = "Backoffice - Articles";
include '../entete.php';
include '../menu.php';

$bdd = matos_connexion();

// ===== Récupération des articles avec infos catégorie et auteur =====
$articles = $bdd->query("
    SELECT a.id, a.titre, a.date_publication, c.nom AS cat_nom, u.prenom 
    FROM articles a
    LEFT JOIN categories c ON a.categorie_id = c.id
    LEFT JOIN utilisateurs u ON a.auteur_id = u.id
    ORDER BY a.date_publication DESC
")->fetchAll();
?>

<main class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2.5rem">
        <h1>Gestion des actualités</h1>
        <a href="ajouter.php" class="btn-ajouter">+ Nouvel Article</a>
    </div>

    <?php if (isset($_GET['ok'])): ?>
        <p style="background:#065f46; color:#fff; padding:1rem; border-radius:4px; margin-bottom:1.5rem">
            Opération réussie.
        </p>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Catégorie</th>
                    <th>Auteur</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($articles as $article): ?>
                    <tr>
                        <td><?= $article['id'] ?></td>
                        <td style="font-weight:600"><?= htmlspecialchars($article['titre']) ?></td>
                        <td>
                            <span class="badge-cat" style="margin:0;">
                                <?= htmlspecialchars($article['cat_nom'] ?: 'Divers') ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($article['prenom'] ?? 'Inconnu') ?></td>
                        <td><?= date('d/m/Y', strtotime($article['date_publication'])) ?></td>
                        <td>
                            <div style="display:flex; gap:0.5rem">
                                <a href="modifier.php?id=<?= $article['id'] ?>" 
                                   style="color:var(--accent); font-size:0.85rem;">Modifier</a>
                                <a href="supprimer.php?id=<?= $article['id'] ?>" 
                                   style="color:#ef4444; font-size:0.85rem;"
                                   onclick="return confirm('Voulez-vous vraiment supprimer cet article ?')">Suppr.</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../pied.php'; ?>