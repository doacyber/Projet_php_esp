<?php
/**
 * Edition d'une catégorie
 */

$chemin_racine = '../';
require_once '../config.php';

session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['administrateur', 'editeur'])) {
    exit("Interdit !");
}

$base_db = matos_connexion();
$msg_info = "";

$cat_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($cat_id <= 0) {
    header('Location: liste.php');
    exit;
}

// On récupère les infos actuelles
$query = $base_db->prepare("SELECT * FROM categories WHERE id = ?");
$query->execute([$cat_id]);
$info_cat = $query->fetch();

if(!$info_cat) {
    header('Location: liste.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveau_nom = trim($_POST['txt_nom'] ?? '');
    $nouvelle_desc = trim($_POST['txt_desc'] ?? '');
    
    if($nouveau_nom != '') {
        $upd = $base_db->prepare("UPDATE categories SET nom = ?, description = ? WHERE id = ?");
        $upd->execute([$nouveau_nom, $nouvelle_desc, $cat_id]);
        header('Location: liste.php?ok=1');
        exit;
    } else {
        $msg_info = "Le nom ne peut pas être vide.";
    }
}

$page_titre = "Modifier la catégorie";
include '../entete.php';
include '../menu.php';
?>

<main class="container">
    <div style="max-width:600px; margin:0 auto; background:var(--card-bg); padding:3rem; border-radius:12px; border:1px solid var(--border);">
        <h1>Modifier : <?= htmlspecialchars($info_cat['nom']) ?></h1>
        
        <?php if($msg_info): ?>
            <p style="color:#ef4444; margin:1.5rem 0"><?= $msg_info ?></p>
        <?php endif; ?>

        <form action="modifier.php?id=<?= $cat_id ?>" method="POST">
            <div style="margin-bottom:1.5rem">
                <label style="display:block; margin-bottom:0.5rem">Nom de la catégorie</label>
                <input type="text" name="txt_nom" value="<?= htmlspecialchars($info_cat['nom']) ?>" required style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
            </div>

            <div style="margin-bottom:2rem">
                <label style="display:block; margin-bottom:0.5rem">Description courte</label>
                <textarea name="txt_desc" rows="4" style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;"><?= htmlspecialchars($info_cat['description']) ?></textarea>
            </div>

            <button type="submit" class="btn-submit" style="width:100%">ENREGISTRER LES MODIFS</button>
        </form>
    </div>
</main>

<?php include '../pied.php'; ?>
