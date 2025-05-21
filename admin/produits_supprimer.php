<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: produits.php?message=erreur');
    exit;
}

// Vérifie si le produit existe
$stmt = $pdo->prepare("SELECT id FROM produits WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) {
    header('Location: produits.php?message=erreur');
    exit;
}

// Supprimer tailles et couleurs liées
$pdo->prepare("DELETE FROM produits_tailles WHERE produit_id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM produits_couleurs WHERE produit_id = ?")->execute([$id]);

// Supprimer le produit lui-même
$pdo->prepare("DELETE FROM produits WHERE id = ?")->execute([$id]);

header('Location: produits.php?message=produit_supprime');
exit;
