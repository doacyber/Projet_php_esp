<?php
$base_url = '../';
$base_url_path = '../';
$titre_page = "Modifier un utilisateur";

require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: ../connexion.php');
    exit;
}

$pdo = getConnexion();
$erreurs = [];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: liste.php'); exit; }

$stmt = $pdo->prepare("SELECT id, nom, prenom, login, role FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) { header('Location: liste.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom    = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $login  = trim($_POST['login'] ?? '');
    $role   = $_POST['role'] ?? 'editeur';
    $mdp    = $_POST['mot_de_passe'] ?? '';
    $mdp2   = $_POST['mdp_confirm'] ?? '';

    if (strlen($nom) < 2)    $erreurs[] = "Nom trop court.";
    if (strlen($prenom) < 2) $erreurs[] = "Prénom trop court.";
    if (strlen($login) < 3)  $erreurs[] = "Login trop court.";
    if (!in_array($role, ['editeur','administrateur'])) $erreurs[] = "Rôle invalide.";

    if (!empty($mdp)) {
        if (strlen($mdp) < 6) $erreurs[] = "Mot de passe trop court.";
        if ($mdp !== $mdp2)   $erreurs[] = "Les mots de passe ne correspondent pas.";
    }

    if (empty($erreurs)) {
        $chk = $pdo->prepare("SELECT id FROM utilisateurs WHERE login = ? AND id != ?");
        $chk->execute([$login, $id]);
        if ($chk->fetch()) $erreurs[] = "Ce login est déjà pris.";
    }

    if (empty($erreurs)) {
        if (!empty($mdp)) {
            $hash = password_hash($mdp, PASSWORD_DEFAULT);
            $upd = $pdo->prepare("UPDATE utilisateurs SET nom=?, prenom=?, login=?, role=?, mot_de_passe=? WHERE id=?");
            $upd->execute([$nom, $prenom, $login, $role, $hash, $id]);
        } else {
            $upd = $pdo->prepare("UPDATE utilisateurs SET nom=?, prenom=?, login=?, role=? WHERE id=?");
            $upd->execute([$nom, $prenom, $login, $role, $id]);
        }
        $_SESSION['msg'] = "Utilisateur modifié.";
        header('Location: liste.php');
        exit;
    }

    $user['nom']    = $nom;
    $user['prenom'] = $prenom;
    $user['login']  = $login;
    $user['role']   = $role;
}

require_once '../entete.php';
require_once '../menu.php';
?>

<main class="container">
    <div class="page-header">
        <h1>Modifier l'utilisateur</h1>
        <a href="liste.php" class="btn-retour">← Retour</a>
    </div>

    <?php if (!empty($erreurs)): ?>
    <div class="alert alert-danger"><ul>
        <?php foreach ($erreurs as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?>
    </ul></div>
    <?php endif; ?>

    <form method="POST" action="modifier.php?id=<?= $id ?>" novalidate>
        <div class="form-row">
            <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>">
            </div>
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="login">Login *</label>
            <input type="text" id="login" name="login" value="<?= htmlspecialchars($user['login']) ?>">
        </div>
        <div class="form-group">
            <label for="role">Rôle *</label>
            <select id="role" name="role">
                <option value="editeur" <?= $user['role']==='editeur' ? 'selected':'' ?>>Éditeur</option>
                <option value="administrateur" <?= $user['role']==='administrateur' ? 'selected':'' ?>>Administrateur</option>
            </select>
        </div>
        <p class="form-hint">Laisser vide pour ne pas changer le mot de passe.</p>
        <div class="form-row">
            <div class="form-group">
                <label for="mot_de_passe">Nouveau mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe">
            </div>
            <div class="form-group">
                <label for="mdp_confirm">Confirmer</label>
                <input type="password" id="mdp_confirm" name="mdp_confirm">
            </div>
        </div>
        <button type="submit" class="btn-submit">Enregistrer</button>
    </form>
</main>

<?php require_once '../pied.php'; ?>
