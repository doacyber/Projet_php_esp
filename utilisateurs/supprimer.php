<?php
require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrateur') {
    header('Location: ../connexion.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0 && $id !== (int)$_SESSION['user_id']) {
    $pdo = getConnexion();
    $del = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $del->execute([$id]);
    $_SESSION['msg'] = "Utilisateur supprimé.";
} else {
    $_SESSION['msg'] = "Action non autorisée.";
}

header('Location: liste.php');
exit;
