<?php

$chemin_racine = '../';
require_once '../config.php';

session_start();

// Uniquement pour l'admin
if ($_SESSION['role'] !== 'administrateur') {
    die("Dégage, t'as pas les droits !");
}

$conn = matos_connexion();
$msg_compte = $_SESSION['flash_msg'] ?? '';
unset($_SESSION['flash_msg']);

$sql_users = "SELECT id, nom, prenom, login, role, created_at FROM utilisateurs ORDER BY created_at DESC";
$mes_utilisateurs = $conn->query($sql_users)->fetchAll();

$page_titre = "Staff JAKARLO ESP";
include '../entete.php';
include '../menu.php';
?>

<main class="container">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:3rem">
        <h1 style="font-size:2.5rem; font-weight:900">Utilisateurs</h1>
        <a href="ajouter.php" class="btn-ajouter" style="padding:1rem 2rem">+ Créer un compte</a>
    </div>

    <?php if ($msg_compte): ?>
        <p style="background:#065f46; color:#fff; padding:1rem; border-radius:4px; margin-bottom:2rem"><?= $msg_compte ?></p>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table-admin">
            <thead>
                <tr>
                    <th>Prénom & Nom</th>
                    <th>Identifiant</th>
                    <th>Rôle</th>
                    <th>Depuis le...</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($mes_utilisateurs as $user): ?>
                    <tr>
                        <td>
                            <div style="font-weight:700"><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></div>
                        </td>
                        <td style="color:var(--text-dim)"><?= htmlspecialchars($user['login']) ?></td>
                        <td>
                            <span style="font-size:0.75rem; font-weight:800; text-transform:uppercase; color:<?= $user['role']=='administrateur' ? 'var(--accent)' : '#94a3b8' ?>">
                                <?= $user['role'] ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <div style="display:flex; gap:1rem">
                                <a href="modifier.php?id=<?= $user['id'] ?>" style="color:var(--accent)">Éditer</a>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="supprimer.php?id=<?= $user['id'] ?>" 
                                       onclick="return confirm('Supprimer ce compte ?')" 
                                       style="color:#ef4444">Supprimer</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../pied.php'; ?>
