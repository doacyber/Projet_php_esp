<?php
$base_url = '';
$base_url_path = '';
$titre_page = "Connexion";

require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: accueil.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';

    if (empty($login) || empty($mdp)) {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        $pdo = getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE login = ?");
        $stmt->execute([$login]);
        $user = $stmt->fetch();

        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login']   = $user['login'];
            $_SESSION['nom']     = $user['nom'];
            $_SESSION['prenom']  = $user['prenom'];
            $_SESSION['role']    = $user['role'];
            header('Location: accueil.php');
            exit;
        } else {
            $erreur = "Login ou mot de passe incorrect.";
        }
    }
}

$page_connexion = true;
require_once 'entete.php';
require_once 'menu.php';
?>

<main class="container">
    <div class="form-wrapper">
        <h1>Connexion</h1>

        <?php if ($erreur): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>

        <form id="formConnexion" method="POST" action="connexion.php" novalidate>
            <div class="form-group">
                <label for="login">Login</label>
                <input type="text" id="login" name="login"
                       value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"
                       placeholder="Votre login">
                <span class="err-msg" id="err-login"></span>
            </div>
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="••••••••">
                <span class="err-msg" id="err-mdp"></span>
            </div>
            <button type="submit" class="btn-submit">Se connecter</button>
        </form>
    </div>
</main>

<script src="assets/js/validation.js"></script>
<?php require_once 'pied.php'; ?>
