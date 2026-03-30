<?php
require_once '../config.php';
session_start();

if ($_SESSION['role'] == 'invite') {
    exit("Permission refusée.");
}

$c_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($c_id > 0) {
    $handle = matos_connexion();
    $suppr = $handle->prepare("DELETE FROM categories WHERE id = ?");
    $suppr->execute([$c_id]);
}

header('Location: liste.php?deleted=1');
exit;
