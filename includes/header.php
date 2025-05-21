<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

// Récupération dynamique des catégories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY nom");
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= SITE_NAME ?></title>
    <link rel="stylesheet" href="<?= SITE_URL ?>assets/css/style.css">
    <script src="<?= SITE_URL ?>assets/js/script.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<header>
    <div class="container">
        <div class="logo">
        <?php if (isset($_SESSION['utilisateur'])): ?>
            <a href="<?= SITE_URL ?>public/home.php"><h1><?= SITE_NAME ?></h1></a>
            <?php else :?>
                <a href="<?= SITE_URL ?>public/index.php"><h1><?= SITE_NAME ?></h1></a>
            <?php endif; ?>
        </div>

        <nav id="main-nav" class="nav-closed">
            <ul>
                <li><a href="<?= SITE_URL ?>public/index.php">Accueil</a></li>

                <!-- Menu déroulant Catégories -->
                <li class="dropdown">
                <a href="#" class="toggle-submenu">Catégories</a>
                    <ul class="submenu">
                        <?php foreach ($categories as $cat): ?>
                            <?php
                                $slug = strtolower(str_replace(
                                    [' ', "'", 'é','è','ê','ë','à','â','ä','ô','ö','î','ï','ù','û','ü','ç'],
                                    ['','','e','e','e','e','a','a','a','o','o','i','i','u','u','u','c'],
                                    $cat['nom']
                                ));
                            ?>
                            <li><a href="<?= SITE_URL ?>public/magasin.php?categorie=<?= $slug ?>"><?= htmlspecialchars($cat['nom']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>

                <?php if (isset($_SESSION['utilisateur'])): ?>
                    <li><a href="<?= SITE_URL ?>public/panier.php">Panier</a></li>
                    <li><a href="<?= SITE_URL ?>public/compte.php">Mon compte</a></li>
                    <li><a href="<?= SITE_URL ?>public/mes_commandes.php">Mes commandes</a></li>
                    <?php if ($_SESSION['utilisateur']['role'] === 'admin'): ?>
                        <li><a href="<?= SITE_URL ?>admin/index.php">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="<?= SITE_URL ?>public/deconnexion.php">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="<?= SITE_URL ?>public/connexion.php">Connexion</a></li>
                    <li><a href="<?= SITE_URL ?>public/inscription.php">Inscription</a></li>
                <?php endif; ?>
            </ul>

        <button id="burger-btn" class="burger">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
<main class="main-content">
