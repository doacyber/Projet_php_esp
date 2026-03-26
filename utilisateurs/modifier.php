<?php
$chemin_racine = '../';
require_once '../config.php';

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    die("Accès réservé aux admins.");
}

$db_link = matos_connexion();
$msg_alerte = "";

$uid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if($uid <= 0) {
    header('Location: liste.php');
    exit;
}

// On récupère le profil
$stmt_get = $db_link->prepare("SELECT id, nom, prenom, login, role FROM utilisateurs WHERE id = ?");
$stmt_get->execute([$uid]);
$profil = $stmt_get->fetch();

if(!$profil) {
    header('Location: liste.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n = trim($_POST['u_nom'] ?? '');
    $p = trim($_POST['u_prenom'] ?? '');
    $l = trim($_POST['u_login'] ?? '');
    $r = $_POST['u_role'] ?? 'editeur';
    $m = $_POST['u_mdp'] ?? '';

    if ($n != '' && $p != '' && $l != '') {
        // Update de base
        $sql_up = "UPDATE utilisateurs SET nom = ?, prenom = ?, login = ?, role = ? WHERE id = ?";
        $params = [$n, $p, $l, $r, $uid];
        
        $op = $db_link->prepare($sql_up);
        $op->execute($params);

        // Si on a tapé un nouveau pass  
        if ($m != '') {
            $h = password_hash($m, PASSWORD_DEFAULT);
            $db_link->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?")->execute([$h, $uid]);
        }

        $_SESSION['flash_msg'] = "Profil de $p mis à jour.";
        header('Location: liste.php');
        exit;
    } else {
        $msg_alerte = "Merci de remplir les champs obligatoires.";
    }
}

$page_titre = "Modifier Profil";
include '../entete.php';
include '../menu.php';
?>

<main class="container">
    <div style="max-width:700px; margin:0 auto; background:var(--card-bg); padding:3rem; border:1px solid var(--border); border-radius:12px;">
        <h1>Profil de <?= htmlspecialchars($profil['prenom']) ?></h1>
        
        <?php if($msg_alerte): ?>
            <p style="color:#ef4444; margin:1.5rem 0"><?= $msg_alerte ?></p>
        <?php endif; ?>

        <form action="modifier.php?id=<?= $uid ?>" method="POST">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim); font-size:0.8rem">PRÉNOM</label>
                    <input type="text" name="u_prenom" value="<?= htmlspecialchars($profil['prenom']) ?>" required style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim); font-size:0.8rem">NOM</label>
                    <input type="text" name="u_nom" value="<?= htmlspecialchars($profil['nom']) ?>" required style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
                </div>
            </div>

            <div style="margin-bottom:1.5rem">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim); font-size:0.8rem">IDENTIFIANT (LOGIN)</label>
                <input type="text" name="u_login" value="<?= htmlspecialchars($profil['login']) ?>" required style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
            </div>

            <div style="margin-bottom:1.5rem">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim); font-size:0.8rem">RÔLE</label>
                <select name="u_role" style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
                    <option value="editeur" <?= $profil['role']=='editeur'?'selected':'' ?>>Éditeur</option>
                    <option value="administrateur" <?= $profil['role']=='administrateur'?'selected':'' ?>>Administrateur</option>
                </select>
            </div>

            <div style="margin-top:2rem; padding-top:2rem; border-top:1px solid var(--border);">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim); font-size:0.8rem">CHANGER LE MOT DE PASSE (LAISSER VIDE SI INCHANGÉ)</label>
                <input type="password" name="u_mdp" style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
            </div>

            <button type="submit" class="btn-submit" style="width:100%; margin-top:2.5rem">METTRE À JOUR</button>
        </form>
    </div>
</main>

<?php include '../pied.php'; ?>
