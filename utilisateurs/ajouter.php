<?php
/**
 * Inscription d'un nouveau membre de l'équipe
 */

$chemin_racine = '../';
require_once '../config.php';

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: ../connexion.php');
    exit;
}

$bdd_admin = matos_connexion();
$alerte = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['p_prenom'] ?? '');
    $last  = trim($_POST['p_nom'] ?? '');
    $log   = trim($_POST['p_login'] ?? '');
    $way   = $_POST['p_mdp'] ?? '';
    $way2  = $_POST['p_mdp2'] ?? '';
    $lvl   = $_POST['p_role'] ?? 'editeur';

    // Des petites vérifs manuelles
    if ($first != '' && $last != '' && $log != '' && $way != '') {
        if ($way === $way2) {
            // Check si le login existe deja
            $chk = $bdd_admin->prepare("SELECT id FROM utilisateurs WHERE login = ?");
            $chk->execute([$log]);
            if ($chk->fetch()) {
                $alerte = "Identifiant déjà pris, changez-le.";
            } else {
                $hash_pass = password_hash($way, PASSWORD_DEFAULT);
                $query = $bdd_admin->prepare("INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES (?,?,?,?,?)");
                $query->execute([$last, $first, $log, $hash_pass, $lvl]);
                
                $_SESSION['flash_msg'] = "L'utilisateur $first a été créé avec succès.";
                header('Location: liste.php');
                exit;
            }
        } else {
            $alerte = "Les mots de passe ne collent pas !";
        }
    } else {
        $alerte = "Champs manquants, merci de tout remplir.";
    }
}

$page_titre = "Nouveau Staff";
include '../entete.php';
include '../menu.php';
?>

<main class="container">
    <div style="max-width:700px; margin:0 auto; background:var(--card-bg); padding:3rem; border:1px solid var(--border); border-radius:12px;">
        <h1 style="margin-bottom:2rem">Ajouter un collaborateur</h1>

        <?php if($alerte): ?>
            <div style="color:#ef4444; margin-bottom:2rem"><?= $alerte ?></div>
        <?php endif; ?>

        <form action="ajouter.php" method="POST">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim); font-size:0.8rem">PRÉNOM</label>
                    <input type="text" name="p_prenom" required style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim); font-size:0.8rem">NOM</label>
                    <input type="text" name="p_nom" required style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
                </div>
            </div>

            <div style="margin-bottom:1.5rem">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim); font-size:0.8rem">NOM D'UTILISATEUR (LOGIN)</label>
                <input type="text" name="p_login" required style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem">
                <div>
                    <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim); font-size:0.8rem">MOT DE PASSE</label>
                    <input type="password" name="p_mdp" required style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim); font-size:0.8rem">CONFIRMATION</label>
                    <input type="password" name="p_mdp2" required style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
                </div>
            </div>

            <div style="margin-bottom:2.5rem">
                <label style="display:block; margin-bottom:0.5rem; color:var(--text-dim); font-size:0.8rem">RÔLE DANS L'ÉQUIPE</label>
                <select name="p_role" style="width:100%; padding:0.8rem; background:#000; border:1px solid var(--border); color:#fff; border-radius:4px;">
                    <option value="editeur">Éditeur (Articles/Cats)</option>
                    <option value="administrateur">Administrateur (Tout)</option>
                </select>
            </div>

            <button type="submit" class="btn-submit" style="width:100%; padding:1.2rem">CRÉER LE COMPTE</button>
        </form>
    </div>
</main>

<?php include '../pied.php'; ?>
