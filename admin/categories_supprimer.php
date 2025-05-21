<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: categories.php?message=erreur');
    exit;
}

// Vérifier si la catégorie existe
$stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ?");
$stmt->execute([$id]);
$categorie = $stmt->fetch();

if (!$categorie) {
    header('Location: categories.php?message=erreur');
    exit;
}

// Vérifier si des produits utilisent cette catégorie
$stmt = $pdo->prepare("SELECT COUNT(*) FROM produits WHERE categorie_id = ?");
$stmt->execute([$id]);
$nb_produits = $stmt->fetchColumn();

if ($nb_produits > 0) {
    header('Location: categories.php?message=categorie_utilisee');
    exit;
}

// Supprimer la catégorie
$pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);

header('Location: categories.php?message=categorie_supprimee');
exit;
