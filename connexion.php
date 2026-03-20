<?php
/**
 * Gestion de la connexion utilisateur
 */

require_once 'config.php';

session_start();

// Si déjà connecté, retour à l'accueil
if (isset($_SESSION['user_id'])) {
    header('Location: accueil.php');
    exit;
}

$messageErreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération des champs
    $loginSaisi = trim($_POST['username'] ?? '');
    $motDePasseSaisi = $_POST['password'] ?? '';

    if ($loginSaisi !== '' && $motDePasseSaisi !== '') {

        $connexion = matos_connexion();

        // Recherche du compte
        $requete = $connexion->prepare(
            "SELECT id, nom, prenom, role, mot_de_passe 
             FROM utilisateurs 
             WHERE login = :login"
        );

        $requete->execute(['login' => $loginSaisi]);

        $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

        if ($utilisateur) {

            // Vérification du mot de passe
            if (password_verify($motDePasseSaisi, $utilisateur['mot_de_passe'])) {

                // Initialisation session
                $_SESSION['user_id'] = $utilisateur['id'];
                $_SESSION['nom'] = $utilisateur['nom'];
                $_SESSION['prenom'] = $utilisateur['prenom'];
                $_SESSION['role'] = $utilisateur['role'];

                header('Location: accueil.php?status=connected');
                exit;

            } else {
                $messageErreur = "Mot de passe incorrect.";
            }

        } else {
            $messageErreur = "Utilisateur introuvable.";
        }

    } else {
        $messageErreur = "Veuillez renseigner tous les champs.";
    }
}

$page_titre = "Connexion";
$hide_connexion_btn = true;

include 'entete.php';
include 'menu.php';
?>

<main class="container">
    <div class="auth-card">

        <h1 class="auth-title">Espace utilisateur</h1>

        <?php if (!empty($messageErreur)): ?>
            <div class="alert-error">
                <?= htmlspecialchars($messageErreur) ?>
            </div>
        <?php endif; ?>

        <form action="connexion.php" method="POST">

            <div class="form-group">
                <label>Login</label>
                <input type="text" name="username" value="<?= htmlspecialchars($loginSaisi ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password">
            </div>

            <button type="submit" class="btn-primary">
                Se connecter
            </button>

        </form>

    </div>
</main>

<?php include 'pied.php'; ?>