<?php
$chemin_racine = '../';
require_once '../config.php';

@session_start();
if($_SESSION['role'] == 'invite') {
    die("Pas le droit d'être ici !");
}

$msg_err = "";
$conn_db = matos_connexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_cat = trim($_POST['n'] ?? '');
    $desc_cat = trim($_POST['d'] ?? '');

    if($nom_cat != '') {
        $prep = $conn_db->prepare("INSERT INTO categories (nom, description) VALUES (?, ?)");
        $prep->execute([$nom_cat, $desc_cat]);
        header('Location: liste.php?ok=1');
        exit;
    } else {
        $msg_err = "Le nom est obligatoire, merci.";
    }
}

$page_titre = "Nouvelle Thématique";
include '../entete.php';
include '../menu.php';
?>

<main class="container">
    <div style="max-width:600px; margin:0 auto; background:var(--card-bg); padding:3rem; border-radius:12px; border:1px solid var(--border);">
        <h1 style="margin-bottom:2rem">Ajouter une thématique</h1>

        <?php if($msg_err): ?>
            <p style="color:#ef4444; margin-bottom:1.5rem"><?= $msg_err ?></p>
        <?php endif; ?>

        <form action="ajouter.php" method="POST">
            <div style="margin-bottom:1.5rem">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim); font-size:0.8rem">NOM DE LA CATÉGORIE *</label>
                <input type="text" name="n" required style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
            </div>

            <div style="margin-bottom:2rem">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim); font-size:0.8rem">DESCRIPTION (FACULTATIF)</label>
                <textarea name="d" rows="3" style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;"></textarea>
            </div>

            <button type="submit" class="btn-submit" style="width:100%; padding:1rem">CRÉER LA CATÉGORIE</button>
        </form>
    </div>
</main>

<?php include '../pied.php'; ?>
