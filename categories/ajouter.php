<?php
$base_url = '../';
$base_url_path = '../';
$titre_page = "Ajouter une catégorie";

require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['editeur','administrateur'])) {
    header('Location: ../connexion.php');
    exit;
}

$erreurs = [];
$nom = $desc = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom  = trim($_POST['nom'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if (strlen($nom) < 2) $erreurs[] = "Le nom doit faire au moins 2 caractères.";

    if (empty($erreurs)) {
        $pdo = getConnexion();
        $ins = $pdo->prepare("INSERT INTO categories (nom, description) VALUES (?, ?)");
        $ins->execute([$nom, $desc]);
        $_SESSION['msg'] = "Catégorie ajoutée.";
        header('Location: liste.php');
        exit;
    }
}

require_once '../entete.php';
require_once '../menu.php';
?>

<main class="container">
    <div class="page-header">
        <h1>Nouvelle catégorie</h1>
        <a href="liste.php" class="btn-retour">← Retour</a>
    </div>

    <?php if (!empty($erreurs)): ?>
    <div class="alert alert-danger"><ul>
        <?php foreach ($erreurs as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?>
    </ul></div>
    <?php endif; ?>

    <form method="POST" action="ajouter.php" id="formCat" novalidate>
        <div class="form-group">
            <label for="nom">Nom *</label>
            <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>" placeholder="Ex: Technologie">
            <span class="err-msg" id="err-nom"></span>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3"><?= htmlspecialchars($desc) ?></textarea>
        </div>
        <button type="submit" class="btn-submit">Ajouter</button>
    </form>
</main>

<script src="../assets/js/validation.js"></script>
<?php require_once '../pied.php'; ?>
