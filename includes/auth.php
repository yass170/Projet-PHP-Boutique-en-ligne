<?php

// Assure que l'utilisateur est connecté
function requireLogin(): void {
    if (!isset($_SESSION['utilisateur'])) {
        header('Location: ../public/index.php');
        exit;
    }
}

// Assure que l'utilisateur est administrateur
function requireAdmin(): void {
    if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin') {
        header('Location: ../public/index.php');
        exit;
    }
}
