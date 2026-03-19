<?php
$base_url = '../';
$base_url_path = '../';
$titre_page = "Gestion des utilisateurs";

require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: ../connexion.php');
    exit;
}

require_once '../entete.php';
require_once '../menu.php';

$pdo = getConnexion();
$msg = $_SESSION['msg'] ?? '';
unset($_SESSION['msg']);

$users = $pdo->query("SELECT id, nom, prenom, login, role, created_at FROM utilisateurs ORDER BY created_at DESC")->fetchAll();
?>

<main class="container">
    <div class="page-header">
        <h1>Utilisateurs</h1>
        <a href="ajouter.php" class="btn-ajouter">+ Nouvel utilisateur</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="table-responsive">
    <table class="table-admin">
        <thead>
            <tr><th>#</th><th>Prénom Nom</th><th>Login</th><th>Rôle</th><th>Inscrit le</th><th>Actions</th></tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></td>
                <td><?= htmlspecialchars($u['login']) ?></td>
                <td><span class="badge-role badge-<?= $u['role'] ?>"><?= $u['role'] ?></span></td>
                <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                <td class="td-actions">
                    <a href="modifier.php?id=<?= $u['id'] ?>" class="btn-edit">Modifier</a>
                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                    <a href="supprimer.php?id=<?= $u['id'] ?>" class="btn-del"
                       onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</main>

<?php require_once '../pied.php'; ?>
