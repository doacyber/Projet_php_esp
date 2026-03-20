<?php
/**
 * Création d'un nouvel article
 */

$chemin_racine = '../';
require_once '../config.php';

@session_start();

// Protection de la page
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['editeur', 'administrateur'])) {
    die("Accès non autorisé !");
}

$erreur_form = "";
$db_handle = matos_connexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $t = trim($_POST['t'] ?? '');
    $d = trim($_POST['d'] ?? '');
    $c = trim($_POST['c'] ?? '');
    $cat = (int)($_POST['cat_id'] ?? 0);

    if ($t != '' && $c != '' && $cat > 0) {
        $img_name = null;
        if (!empty($_FILES['pic']['name'])) {
            $ext = strtolower(pathinfo($_FILES['pic']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                $img_name = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                move_uploaded_file($_FILES['pic']['tmp_name'], '../uploads/' . $img_name);
            }
        }

        $ins = $db_handle->prepare("INSERT INTO articles (titre, description_courte, contenu, categorie_id, auteur_id, image) VALUES (?,?,?,?,?,?)");
        $ins->execute([$t, $d, $c, $cat, $_SESSION['user_id'], $img_name]);
        
        header('Location: liste.php?ok=1');
        exit;
    } else {
        $erreur_form = "Les champs marqués d'une * sont obligatoires.";
    }
}

// Pour remplir le select
$q_cats = $db_handle->query("SELECT * FROM categories ORDER BY nom");
$mes_cats = $q_cats->fetchAll();

$page_titre = "Nouvel article";
include '../entete.php';
include '../menu.php';
?>

<main class="container">
    <div style="max-width:800px; margin: 0 auto; background:var(--card-bg); padding:3rem; border-radius:12px; border:1px solid var(--border);">
        <h1 style="margin-bottom:2rem">Rédiger une actu</h1>

        <?php if($erreur_form): ?>
            <p style="color:#ef4444; margin-bottom:1.5rem"><?= $erreur_form ?></p>
        <?php endif; ?>

        <form action="ajouter.php" method="POST" enctype="multipart/form-data">
            <div style="margin-bottom:1.5rem">
                <label style="display:block; margin-bottom:0.5rem">Titre de l'article *</label>
                <input type="text" name="t" required style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
            </div>

            <div style="margin-bottom:1.5rem">
                <label style="display:block; margin-bottom:0.5rem">Résumé (description courte)</label>
                <textarea name="d" rows="2" style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;"></textarea>
            </div>

            <div style="margin-bottom:1.5rem">
                <label style="display:block; margin-bottom:0.5rem">Catégorie *</label>
                <select name="cat_id" required style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
                    <option value="">-- Choisir une catégorie --</option>
                    <?php foreach($mes_cats as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= $cat['nom'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="margin-bottom:1.5rem">
                <label style="display:block; margin-bottom:0.5rem">Contene de l'article *</label>
                <textarea name="c" rows="10" required style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;"></textarea>
            </div>

            <div style="margin-bottom:2.5rem">
                <label style="display:block; margin-bottom:0.5rem">Image d'illustration (optionnelle)</label>
                <input type="file" name="pic" accept="image/*" style="font-size:0.9rem">
            </div>

            <button type="submit" class="btn-submit" style="width:100%">PUBLIER MAINTENANT</button>
        </form>
    </div>
</main>

<?php include '../pied.php'; ?>
