<?php
session_start();
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès refusé.']);
    exit;
}

$user_id = $_SESSION['utilisateur']['id'];
$produit_id = (int) ($_POST['produit_id'] ?? 0);
$taille = trim($_POST['taille'] ?? '');
$couleur = trim($_POST['couleur'] ?? '');
$quantite = (int) ($_POST['quantite'] ?? 1);

if ($produit_id <= 0 || !$taille || !$couleur || $quantite < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données invalides.']);
    exit;
}

// Vérifie si le produit est actif et récupère son stock
$stmt = $pdo->prepare("SELECT stock FROM produits WHERE id = ? AND actif = 1");
$stmt->execute([$produit_id]);
$produit = $stmt->fetch();

if (!$produit) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Produit introuvable.']);
    exit;
}

$stock_disponible = (int) $produit['stock'];

// Vérifie si l’article est déjà dans le panier
$stmt = $pdo->prepare("SELECT id, quantite FROM paniers WHERE utilisateur_id = ? AND produit_id = ? AND taille = ? AND couleur = ?");
$stmt->execute([$user_id, $produit_id, $taille, $couleur]);
$existant = $stmt->fetch();

$quantite_finale = $quantite;
if ($existant) {
    $quantite_finale += (int)$existant['quantite'];
}

if ($quantite_finale > $stock_disponible) {
    echo json_encode([
        'success' => false,
        'message' => "Stock insuffisant. Merci de réessayer ultérieurement."
    ]);
    exit;
}

// Mise à jour ou insertion dans le panier
if ($existant) {
    $stmt = $pdo->prepare("UPDATE paniers SET quantite = ? WHERE id = ?");
    $stmt->execute([$quantite_finale, $existant['id']]);
} else {
    $stmt = $pdo->prepare("INSERT INTO paniers (utilisateur_id, produit_id, taille, couleur, quantite) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $produit_id, $taille, $couleur, $quantite]);
}

echo json_encode([
    'success' => true,
    'message' => 'Produit ajouté au panier avec succès.',
    'quantite_totale' => $quantite_finale
]);
exit;
