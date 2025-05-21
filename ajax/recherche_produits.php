<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin') {
    http_response_code(403);
    exit;
}


$motCle = trim($_GET['q'] ?? '');

if ($motCle === '*' || $motCle === '') {
    // Retourner tous les produits si le champ est vide ou '*'
    $stmt = $pdo->query("SELECT p.*, c.nom AS categorie_nom 
                         FROM produits p 
                         LEFT JOIN categories c ON p.categorie_id = c.id 
                         ORDER BY p.id DESC");
    echo json_encode($stmt->fetchAll());
    exit;
}

$produits = rechercherProduitsParNom($pdo, $motCle);
echo json_encode($produits);


