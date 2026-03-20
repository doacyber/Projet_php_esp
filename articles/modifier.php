<?php
/**
 * Page de modification d'un article existant
 * Seuls les utilisateurs avec rôle 'editeur' ou 'administrateur' peuvent accéder
 */

$chemin_racine = '../';
require_once '../config.php';
session_start();

// ===== Vérification des droits =====
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['editeur', 'administrateur'])) {
    die("Accès interdit. Connecte-toi avec un compte autorisé.");
}

$bdd = matos_connexion();
$messageErreur = "";

// ===== Récupération de l'ID de l'article =====
$article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($article_id <= 0) {
    header('Location: liste.php');
    exit;
}

// ===== Récupération des informations actuelles de l'article =====
$stmt = $bdd->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$article_id]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: liste.php');
    exit;
}

// ===== Traitement du formulaire si POST =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $resume = trim($_POST['resume'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $categorie_id = (int)($_POST['categorie_id'] ?? 0);
    $image_finale = $article['image'];

    if ($titre && $contenu && $categorie_id > 0) {

        // ===== Gestion de l'image si uploadée =====
        if (!empty($_FILES['image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) {
                $image_finale = 'art_' . time() . '_' . rand(100,999) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image_finale);
            }
        }

        // ===== Mise à jour de l'article =====
        $sql_update = "UPDATE articles 
                       SET titre=?, description_courte=?, contenu=?, categorie_id=?, image=? 
                       WHERE id=?";
        $bdd->prepare($sql_update)->execute([$titre, $resume, $contenu, $categorie_id, $image_finale, $article_id]);

        header('Location: liste.php?ok=1');
        exit;
    } else {
        $messageErreur = "Tous les champs obligatoires (*) doivent être remplis.";
    }
}

// ===== Liste des catégories pour le select =====
$liste_categories = $bdd->query("SELECT * FROM categories ORDER BY nom")->fetchAll();

$page_titre = "Édition de l'article";
include '../entete.php';
include '../menu.php';
?>

<main class="container">
    <div style="max-width:850px; margin:0 auto; background:var(--card-bg); padding:3rem; border:1px solid var(--border); border-radius:12px;">
        <h1 style="margin-bottom:2rem">Modifier l'article</h1>

        <?php if($messageErreur): ?>
            <p style="color:#ef4444; margin-bottom:1.5rem"><?= htmlspecialchars($messageErreur) ?></p>
        <?php endif; ?>

        <form action="modifier.php?id=<?= $article_id ?>" method="POST" enctype="multipart/form-data">

            <!-- Titre -->
            <div style="margin-bottom:1.5rem">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim)">Titre *</label>
                <input type="text" name="titre" value="<?= htmlspecialchars($article['titre']) ?>" required
                       style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
            </div>

            <!-- Résumé -->
            <div style="margin-bottom:1.5rem">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim)">Résumé</label>
                <textarea name="resume" rows="2"
                          style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;"><?= htmlspecialchars($article['description_courte']) ?></textarea>
            </div>

            <!-- Catégorie -->
            <div style="margin-bottom:1.5rem">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim)">Catégorie *</label>
                <select name="categorie_id" required
                        style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
                    <?php foreach($liste_categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($article['categorie_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Contenu -->
            <div style="margin-bottom:1.5rem">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim)">Corps de l'article *</label>
                <textarea name="contenu" rows="12" required
                          style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;"><?= htmlspecialchars($article['contenu']) ?></textarea>
            </div>

            <!-- Image -->
            <div style="margin-bottom:2.5rem; padding:1.5rem; border:1px dashed var(--border); border-radius:4px;">
                <label style="display:block; margin-bottom:1rem; font-weight:600">Image d'illustration :</label>
                <?php if($article['image']): ?>
                    <div style="margin-bottom:1rem">
                        <img src="../uploads/<?= htmlspecialchars($article['image']) ?>" style="height:80px; border-radius:4px;">
                        <p style="font-size:0.7rem; color:var(--text-dim)">Laisser vide pour conserver l'image actuelle.</p>
                    </div>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*">
            </div>

            <button type="submit" class="btn-submit" style="width:100%">Mettre à jour l'article</button>
        </form>
    </div>
</main>

<?php include '../pied.php'; ?>