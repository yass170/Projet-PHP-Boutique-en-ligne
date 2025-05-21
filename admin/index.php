<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

// Statistiques générales
$nbProduits = $pdo->query("SELECT COUNT(*) FROM produits")->fetchColumn();
$nbUtilisateurs = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$nbCommandes = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();

// Produits les plus consultés
$stmt = $pdo->query("SELECT id, nom, vues FROM produits ORDER BY vues DESC LIMIT 5");
$produits_populaires = $stmt->fetchAll();
?>

<section class="admin-dashboard">
    <div class="container">
        <h1>Tableau de bord administrateur</h1>

        <div class="stats">
            <div class="stat-box">
                <h2><?= $nbProduits ?></h2>
                <p>Produits en catalogue</p>
            </div>
            <div class="stat-box">
                <h2><?= $nbUtilisateurs ?></h2>
                <p>Utilisateurs inscrits</p>
            </div>
            <div class="stat-box">
                <h2><?= $nbCommandes ?></h2>
                <p>Commandes simulées</p>
            </div>
        </div>

        <div class="top-produits">
            <h2>Produits les plus consultés</h2>
            <ul>
                <?php foreach ($produits_populaires as $p): ?>
                    <li>#<?= $p['id'] ?> - <?= htmlspecialchars($p['nom']) ?> (<?= $p['vues'] ?> vues)</li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="admin-liens">
            <a href="produits.php">Gérer les produits</a> |
            <a href="categories.php">Gérer les catégories</a> |
            <a href="utilisateurs.php">Gérer les utilisateurs</a> |
            <a href="site_settings.php">Paramètres du site</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
