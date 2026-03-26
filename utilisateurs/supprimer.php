<?php 
require_once '../config.php';
session_start();

// Vérification des droits
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    $_SESSION['flash_msg'] = "Accès non autorisé.";
    header('Location: ../accueil.php');
    exit;
}

// ID cible
$userId = (int)($_GET['id'] ?? 0);
$currentUserId = (int)($_SESSION['user_id'] ?? 0);

// Empêcher l'auto-suppression
if ($userId > 0 && $userId !== $currentUserId) {

    $db = matos_connexion();

    $stmt = $db->prepare("DELETE FROM utilisateurs WHERE id = :id");
    $stmt->execute(['id' => $userId]);

    $_SESSION['flash_msg'] = "Utilisateur supprimé avec succès.";

} else {
    $_SESSION['flash_msg'] = "Action invalide.";
}

// Redirection
header('Location: liste.php');
exit;