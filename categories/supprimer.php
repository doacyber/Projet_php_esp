<?php
require_once '../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['editeur','administrateur'])) {
    header('Location: ../connexion.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $pdo = getConnexion();
    $del = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $del->execute([$id]);
    $_SESSION['msg'] = "Catégorie supprimée.";
}

header('Location: liste.php');
exit;
