<?php
/**
 * Liste des catégories
 */

$chemin_racine = '../';
require_once '../config.php';

session_start();

// Vérification utilisateur connecté
if (empty($_SESSION['user_id'])) {
    header('Location: ../connexion.php');
    exit;
}

$db = matos_connexion();
$messageOk = $_GET['ok'] ?? '';

// Récupération des catégories avec nombre d'articles
$categories = $db->query("
    SELECT 
        c.id,
        c.nom,
        c.description,
        COUNT(a.id) AS total_articles
    FROM categories c
    LEFT JOIN articles a ON a.categorie_id = c.id
    GROUP BY c.id
    ORDER BY c.nom ASC
")->fetchAll(PDO::FETCH_ASSOC);

$page_titre = "Gestion des catégories";

include '../entete.php';
include '../menu.php';
?>

<main class="container">

    <div class="page-header">
        <h1>Catégories</h1>
        <a href="ajouter.php" class="btn-ajouter">Nouvelle catégorie</a>
    </div>

    <?php if (!empty($messageOk)): ?>
        <div class="alert-success">
            Opération effectuée avec succès.
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Articles</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td class="fw-bold">
                            <?= htmlspecialchars($cat['nom']) ?>
                        </td>

                        <td class="text-muted">
                            <?= htmlspecialchars($cat['description']) ?>
                        </td>

                        <td>
                            <?= (int)$cat['total_articles'] ?> article(s)
                        </td>

                        <td>
                            <div class="actions">
                                <a href="modifier.php?id=<?= (int)$cat['id'] ?>" class="link-edit">
                                    Modifier
                                </a>

                                <a href="supprimer.php?id=<?= (int)$cat['id'] ?>"
                                   class="link-delete"
                                   onclick="return confirm('Confirmer la suppression de cette catégorie ?')">
                                    Supprimer
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

</main>

<?php include '../pied.php'; ?>