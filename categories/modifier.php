<?php
$base_url = '../';
$base_url_path = '../';
$titre_page = "Modifier une catégorie";

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

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$cat = $stmt->fetch();
if (!$cat) { header('Location: liste.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom  = trim($_POST['nom'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if (strlen($nom) < 2) $erreurs[] = "Nom trop court.";

    if (empty($erreurs)) {
        $upd = $pdo->prepare("UPDATE categories SET nom=?, description=? WHERE id=?");
        $upd->execute([$nom, $desc, $id]);
        $_SESSION['msg'] = "Catégorie modifiée.";
        header('Location: liste.php');
        exit;
    }
    $cat['nom'] = $nom;
    $cat['description'] = $desc;
}

require_once '../entete.php';
require_once '../menu.php';
?>

<main class="container">
    <div class="page-header">
        <h1>Modifier la catégorie</h1>
        <a href="liste.php" class="btn-retour">← Retour</a>
    </div>

    <?php if (!empty($erreurs)): ?>
    <div class="alert alert-danger"><ul>
        <?php foreach ($erreurs as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?>
    </ul></div>
    <?php endif; ?>

    <form method="POST" action="modifier.php?id=<?= $id ?>" novalidate>
        <div class="form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($cat['nom']) ?>">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?= htmlspecialchars($cat['description'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn-submit">Enregistrer</button>
    </form>
</main>

<?php require_once '../pied.php'; ?>
