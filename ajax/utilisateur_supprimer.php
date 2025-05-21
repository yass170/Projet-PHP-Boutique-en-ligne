<?php
session_start();
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}

$id = (int) ($_POST['id_utilisateur'] ?? 0);
if ($id <= 0 || $id === $_SESSION['utilisateur']['id']) {
    http_response_code(403);
    exit;
}

// Vérifier si l'utilisateur existe
$stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
if ($stmt->fetch()) {
    $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?")->execute([$id]);
    header('Location: ../admin/utilisateurs.php?message=utilisateur_supprime');
    exit;
}

header('Location: ../admin/utilisateurs.php?message=erreur');
exit;
