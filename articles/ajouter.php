<?php
$base_url = '../';
$base_url_path = '../';
$titre_page = "Ajouter un article";

require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['editeur','administrateur'])) {
    header('Location: ../connexion.php');
    exit;
}

$pdo = getConnexion();
$erreurs = [];
$vals = ['titre'=>'','description_courte'=>'','contenu'=>'','categorie_id'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre  = trim($_POST['titre'] ?? '');
    $desc   = trim($_POST['description_courte'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $cat_id = (int)($_POST['categorie_id'] ?? 0);

    $vals = compact('titre','desc','contenu','cat_id');

    if (strlen($titre) < 3)   $erreurs[] = "Le titre doit faire au moins 3 caractères.";
    if (strlen($contenu) < 10) $erreurs[] = "Le contenu est trop court.";
    if ($cat_id <= 0)          $erreurs[] = "Veuillez choisir une catégorie.";
    if (strlen($desc) > 300)   $erreurs[] = "La description courte ne doit pas dépasser 300 caractères.";

    $nom_image = null;
    if (!empty($_FILES['image']['name'])) {
        $ext_ok = ['jpg','jpeg','png','gif','webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $ext_ok)) {
            $erreurs[] = "Format d'image non supporté (jpg, png, gif, webp uniquement).";
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $erreurs[] = "L'image ne doit pas dépasser 2 Mo.";
        } else {
            $nom_image = uniqid('img_') . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $nom_image);
        }
    }

    if (empty($erreurs)) {
        $ins = $pdo->prepare("INSERT INTO articles (titre, description_courte, contenu, categorie_id, auteur_id, image)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $ins->execute([$titre, $desc, $contenu, $cat_id, $_SESSION['user_id'], $nom_image]);
        $_SESSION['msg'] = "Article ajouté avec succès.";
        header('Location: liste.php');
        exit;
    }
}

$cats = $pdo->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll();

require_once '../entete.php';
require_once '../menu.php';
?>

<main class="container">
    <div class="page-header">
        <h1>Ajouter un article</h1>
        <a href="liste.php" class="btn-retour">← Retour</a>
    </div>

    <?php if (!empty($erreurs)): ?>
    <div class="alert alert-danger">
        <ul><?php foreach ($erreurs as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul>
    </div>
    <?php endif; ?>

    <form id="formArticle" method="POST" action="ajouter.php" enctype="multipart/form-data" novalidate>
        <div class="form-group">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre"
                   value="<?= htmlspecialchars($vals['titre']) ?>" placeholder="Titre de l'article">
            <span class="err-msg" id="err-titre"></span>
        </div>

        <div class="form-group">
            <label for="description_courte">Description courte</label>
            <input type="text" id="description_courte" name="description_courte"
                   value="<?= htmlspecialchars($vals['description_courte'] ?? '') ?>"
                   placeholder="Résumé affiché sur la page d'accueil (max 300 car.)">
            <span class="err-msg" id="err-desc"></span>
        </div>

        <div class="form-group">
            <label for="categorie_id">Catégorie *</label>
            <select id="categorie_id" name="categorie_id">
                <option value="">-- Choisir --</option>
                <?php foreach ($cats as $c): ?>
                    <option value="<?= $c['id'] ?>"
                        <?= ($vals['categorie_id'] == $c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span class="err-msg" id="err-cat"></span>
        </div>

        <div class="form-group">
            <label for="contenu">Contenu *</label>
            <textarea id="contenu" name="contenu" rows="10"
                      placeholder="Rédigez votre article ici..."><?= htmlspecialchars($vals['contenu']) ?></textarea>
            <span class="err-msg" id="err-contenu"></span>
        </div>

        <div class="form-group">
            <label for="image">Image (optionnel)</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>

        <button type="submit" class="btn-submit">Publier l'article</button>
    </form>
</main>

<script src="../assets/js/validation.js"></script>
<?php require_once '../pied.php'; ?>
