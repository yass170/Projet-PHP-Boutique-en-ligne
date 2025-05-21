<?php

// Protection XSS de base
function e(string $valeur): string {
    return htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8');
}

// Vérifie si un utilisateur est connecté
function estConnecte(): bool {
    return isset($_SESSION['utilisateur']);
}

// Vérifie si l'utilisateur est admin
function estAdmin(): bool {
    return estConnecte() && $_SESSION['utilisateur']['role'] === 'admin';
}

// Formater une date (ex. affichage commandes)
function formatDate($datetime): string {
    return date('d/m/Y H:i', strtotime($datetime));
}
function normaliserNom($str) {
    $str = strtolower($str);
    $str = str_replace([' ', "'", 'é','è','ê','ë','à','â','ä','ô','ö','î','ï','ù','û','ü','ç'],
                       ['', '', 'e','e','e','e','a','a','a','o','o','i','i','u','u','u','c'], $str);
    return $str;
}

function rechercherProduitsParNom(PDO $pdo, string $motCle) {
    $motCle = '%' . $motCle . '%';
    $stmt = $pdo->prepare("SELECT p.*, c.nom AS categorie_nom 
                           FROM produits p
                           LEFT JOIN categories c ON p.categorie_id = c.id
                           WHERE p.nom LIKE ?
                           ORDER BY p.id DESC");
    $stmt->execute([$motCle]);
    return $stmt->fetchAll();
}
