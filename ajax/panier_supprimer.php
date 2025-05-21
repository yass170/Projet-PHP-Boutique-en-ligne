<?php
session_start();
require_once __DIR__ . '/../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé.']);
    exit;
}

$user_id = $_SESSION['utilisateur']['id'];
$id_panier = (int) ($_POST['id_panier'] ?? 0);

if ($id_panier <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID invalide.']);
    exit;
}

// Vérification de l'appartenance
$stmt = $pdo->prepare("SELECT id FROM paniers WHERE id = ? AND utilisateur_id = ?");
$stmt->execute([$id_panier, $user_id]);
$verif = $stmt->fetch();

if ($verif) {
    $stmt = $pdo->prepare("DELETE FROM paniers WHERE id = ?");
    $stmt->execute([$id_panier]);
    echo json_encode(['success' => true, 'message' => 'Article supprimé du panier.']);
    exit;
}

http_response_code(404);
echo json_encode(['success' => false, 'message' => 'Article non trouvé dans votre panier.']);
exit;
