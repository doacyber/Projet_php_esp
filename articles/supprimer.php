<?php
require_once '../config.php';
session_start();

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['editeur', 'administrateur'])) {
    die("Stop ! Pas autorisé.");
}

$id_del = (int)($_GET['id'] ?? 0);

if ($id_del > 0) {
    $db = matos_connexion();
    $res = $db->prepare("DELETE FROM articles WHERE id = :id");
    $res->execute(['id' => $id_del]);
}

header('Location: liste.php?msg=delete_ok');
exit;
