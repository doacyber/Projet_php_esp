<?php
$base_url = '../';
$base_url_path = '../';
$titre_page = "Ajouter un utilisateur";

require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: ../connexion.php');
    exit;
}

$erreurs = [];
$vals = ['nom'=>'','prenom'=>'','login'=>'','role'=>'editeur'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom    = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $login  = trim($_POST['login'] ?? '');
    $mdp    = $_POST['mot_de_passe'] ?? '';
    $mdp2   = $_POST['mdp_confirm'] ?? '';
    $role   = $_POST['role'] ?? 'editeur';

    $vals = compact('nom','prenom','login','role');

    if (strlen($nom) < 2)    $erreurs[] = "Nom trop court.";
    if (strlen($prenom) < 2) $erreurs[] = "Prénom trop court.";
    if (strlen($login) < 3)  $erreurs[] = "Login trop court (min 3 caractères).";
    if (strlen($mdp) < 6)    $erreurs[] = "Mot de passe trop court (min 6 caractères).";
    if ($mdp !== $mdp2)      $erreurs[] = "Les mots de passe ne correspondent pas.";
    if (!in_array($role, ['editeur','administrateur'])) $erreurs[] = "Rôle invalide.";

    if (empty($erreurs)) {
        $pdo = getConnexion();
        $chk = $pdo->prepare("SELECT id FROM utilisateurs WHERE login = ?");
        $chk->execute([$login]);
        if ($chk->fetch()) {
            $erreurs[] = "Ce login est déjà utilisé.";
        } else {
            $hash = password_hash($mdp, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES (?,?,?,?,?)");
            $ins->execute([$nom, $prenom, $login, $hash, $role]);
            $_SESSION['msg'] = "Utilisateur créé.";
            header('Location: liste.php');
            exit;
        }
    }
}

require_once '../entete.php';
require_once '../menu.php';
?>

<main class="container">
    <div class="page-header">
        <h1>Nouvel utilisateur</h1>
        <a href="liste.php" class="btn-retour">← Retour</a>
    </div>

    <?php if (!empty($erreurs)): ?>
    <div class="alert alert-danger"><ul>
        <?php foreach ($erreurs as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?>
    </ul></div>
    <?php endif; ?>

    <form method="POST" action="ajouter.php" id="formUser" novalidate>
        <div class="form-row">
            <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($vals['prenom']) ?>">
                <span class="err-msg" id="err-prenom"></span>
            </div>
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($vals['nom']) ?>">
                <span class="err-msg" id="err-nom"></span>
            </div>
        </div>
        <div class="form-group">
            <label for="login">Login *</label>
            <input type="text" id="login" name="login" value="<?= htmlspecialchars($vals['login']) ?>">
            <span class="err-msg" id="err-login"></span>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe *</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe">
                <span class="err-msg" id="err-mdp"></span>
            </div>
            <div class="form-group">
                <label for="mdp_confirm">Confirmer *</label>
                <input type="password" id="mdp_confirm" name="mdp_confirm">
                <span class="err-msg" id="err-mdp2"></span>
            </div>
        </div>
        <div class="form-group">
            <label for="role">Rôle *</label>
            <select id="role" name="role">
                <option value="editeur" <?= $vals['role']==='editeur' ? 'selected' : '' ?>>Éditeur</option>
                <option value="administrateur" <?= $vals['role']==='administrateur' ? 'selected' : '' ?>>Administrateur</option>
            </select>
        </div>
        <button type="submit" class="btn-submit">Créer le compte</button>
    </form>
</main>

<script src="../assets/js/validation.js"></script>
<?php require_once '../pied.php'; ?>
