<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/mailer.php';

header('Content-Type: application/json');

if (!isset($_SESSION['utilisateur']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé.']);
    exit;
}

$user_id = $_SESSION['utilisateur']['id'];

// Récupérer les articles du panier
$stmt = $pdo->prepare("SELECT pa.*, p.nom, p.prix, p.stock FROM paniers pa
                       JOIN produits p ON pa.produit_id = p.id
                       WHERE pa.utilisateur_id = ?");
$stmt->execute([$user_id]);
$articles = $stmt->fetchAll();

if (empty($articles)) {
    echo json_encode(['success' => false, 'message' => 'Votre panier est vide.']);
    exit;
}

// Calcul du total
$total = 0;
foreach ($articles as $a) {
    $total += $a['prix'] * $a['quantite'];
}

try {
    $pdo->beginTransaction();

    // Vérifier le stock avant de créer la commande
    foreach ($articles as $a) {
        if ($a['quantite'] > $a['stock']) {
            throw new Exception("Stock insuffisant pour le produit « " . $a['nom'] . " ».");
        }
    }

    // Création de la commande
    $stmt = $pdo->prepare("INSERT INTO commandes (utilisateur_id, total) VALUES (?, ?)");
    $stmt->execute([$user_id, $total]);
    $commande_id = $pdo->lastInsertId();

    // Enregistrement des produits de la commande + mise à jour du stock
    $insert = $pdo->prepare("INSERT INTO commandes_produits (commande_id, produit_id, taille, couleur, quantite, prix_unitaire)
                             VALUES (?, ?, ?, ?, ?, ?)");
    $updateStock = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");

    foreach ($articles as $a) {
        $insert->execute([
            $commande_id,
            $a['produit_id'],
            $a['taille'],
            $a['couleur'],
            $a['quantite'],
            $a['prix']
        ]);

        $updateStock->execute([$a['quantite'], $a['produit_id']]);
    }

    // Vider le panier
    $pdo->prepare("DELETE FROM paniers WHERE utilisateur_id = ?")->execute([$user_id]);

    // Envoyer l'e-mail de confirmation
    $body = "Bonjour " . htmlspecialchars($_SESSION['utilisateur']['prenom']) . ",\n\n";
    $body .= "Voici le récapitulatif de votre commande #$commande_id :\n\n";
    foreach ($articles as $a) {
        $body .= "- " . $a['nom'] . " (Taille : " . $a['taille'] . ", Couleur : " . $a['couleur'] . ") × " . $a['quantite'] . " → " . number_format($a['prix'], 2, ',', ' ') . " €\n";
    }
    $body .= "\nTotal : " . number_format($total, 2, ',', ' ') . " €\n\n";
    $body .= "Merci pour votre commande (simulation).";

    envoyerMail($_SESSION['utilisateur']['email'], "Confirmation commande #$commande_id", $body);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Commande confirmée avec succès.', 'commande_id' => $commande_id]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
