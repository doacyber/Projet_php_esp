<?php
$base_url = '../';
$base_url_path = '../';
$titre_page = "Modifier un article";

require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['editeur','administrateur'])) {
    header('Location: ../connexion.php');
    exit;
}

$pdo = getConnexion();
$erreurs = [];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: liste.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$art = $stmt->fetch();
if (!$art) { header('Location: liste.php'); exit; }

$cats = $pdo->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre   = trim($_POST['titre'] ?? '');
    $desc    = trim($_POST['description_courte'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $cat_id  = (int)($_POST['categorie_id'] ?? 0);

    if (strlen($titre) < 3)    $erreurs[] = "Titre trop court.";
    if (strlen($contenu) < 10) $erreurs[] = "Contenu trop court.";
    if ($cat_id <= 0)          $erreurs[] = "Choisissez une catégorie.";

    $nom_image = $art['image'];
    if (!empty($_FILES['image']['name'])) {
        $ext_ok = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $ext_ok)) {
            $erreurs[] = "Format d'image non supporté.";
        } else {
            $nom_image = uniqid('img_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $nom_image);
        }
    }

    if (empty($erreurs)) {
        $upd = $pdo->prepare("UPDATE articles SET titre=?, description_courte=?, contenu=?, categorie_id=?, image=? WHERE id=?");
        $upd->execute([$titre, $desc, $contenu, $cat_id, $nom_image, $id]);
        $_SESSION['msg'] = "Article modifié.";
        header('Location: liste.php');
        exit;
    }

    $art['titre'] = $titre;
    $art['description_courte'] = $desc;
    $art['contenu'] = $contenu;
    $art['categorie_id'] = $cat_id;
}

require_once '../entete.php';
require_once '../menu.php';
?>

<main class="container">
    <div class="page-header">
        <h1>Modifier l'article</h1>
        <a href="liste.php" class="btn-retour">← Retour</a>
    </div>

    <?php if (!empty($erreurs)): ?>
    <div class="alert alert-danger">
        <ul><?php foreach ($erreurs as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul>
    </div>
    <?php endif; ?>

    <form id="formArticle" method="POST" action="modifier.php?id=<?= $id ?>" enctype="multipart/form-data" novalidate>
        <div class="form-group">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($art['titre']) ?>">
            <span class="err-msg" id="err-titre"></span>
        </div>
        <div class="form-group">
            <label for="description_courte">Description courte</label>
            <input type="text" id="description_courte" name="description_courte"
                   value="<?= htmlspecialchars($art['description_courte'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="categorie_id">Catégorie *</label>
            <select id="categorie_id" name="categorie_id">
                <option value="">-- Choisir --</option>
                <?php foreach ($cats as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($art['categorie_id'] == $c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="err-msg" id="err-cat"></span>
        </div>
        <div class="form-group">
            <label for="contenu">Contenu *</label>
            <textarea id="contenu" name="contenu" rows="10"><?= htmlspecialchars($art['contenu']) ?></textarea>
            <span class="err-msg" id="err-contenu"></span>
        </div>
        <div class="form-group">
            <label>Changer l'image (optionnel)</label>
            <?php if (!empty($art['image'])): ?>
                <p class="img-actuelle">Image actuelle : <?= htmlspecialchars($art['image']) ?></p>
            <?php endif; ?>
            <input type="file" name="image" accept="image/*">
        </div>
        <button type="submit" class="btn-submit">Enregistrer</button>
    </form>
</main>

<script src="../assets/js/validation.js"></script>
<?php require_once '../pied.php'; ?>
