<?php
require_once __DIR__ . '/../includes/header.php';

// Redirection si non connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: index.php');
    exit;
}

// Produits recommandés (triés par vues, actifs uniquement)
$stmt = $pdo->query("SELECT * FROM produits WHERE actif = 1 ORDER BY vues DESC LIMIT 6");
$produits_recommandes = $stmt->fetchAll();

// Récupération des catégories principales
$stmt = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY nom");
$categories = $stmt->fetchAll();

// Fonction de normalisation (slug & nom d'image)
function normaliserNom($str) {
    $str = strtolower($str);
    return str_replace(
        [' ', "'", 'é','è','ê','ë','à','â','ä','ô','ö','î','ï','ù','û','ü','ç'],
        ['', '', 'e','e','e','e','a','a','a','o','o','i','i','u','u','u','c'],
        $str
    );
}
?>

<section class="accueil-connecte">
    <div class="container">
        <h1>Bienvenue, <?= htmlspecialchars($_SESSION['utilisateur']['prenom']) ?> !</h1>
        <p>Découvrez nos nouvelles sélections rien que pour vous.</p>
    </div>
</section>

<section class="categories-section">
    <?php foreach ($categories as $cat): ?>
        <?php $slug = normaliserNom($cat['nom']); ?>
        <div class="category-card">
            <a href="magasin.php?categorie=<?= $slug ?>">
                <img src="<?= SITE_URL ?>assets/images/categories/<?= $slug ?>.jpg" alt="<?= htmlspecialchars($cat['nom']) ?>">
                <p><?= strtoupper(htmlspecialchars($cat['nom'])) ?></p>
            </a>
        </div>
    <?php endforeach; ?>
</section>

<section class="recommandations">
    <div class="container">
        <h2>Produits recommandés</h2>
        <div class="produits-grid">
            <?php foreach ($produits_recommandes as $produit): ?>
                <div class="produit-carte <?= $produit['stock'] <= 0 ? 'rupture' : '' ?>">
                    <a href="produit.php?id=<?= $produit['id'] ?>">
                        <img src="<?= SITE_URL ?>assets/images/produits/<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
                        <?php if ($produit['stock'] <= 0): ?>
                            <div class="etiquette-rupture">Rupture</div>
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($produit['nom']) ?></h3>
                        <p><?= number_format($produit['prix'], 2, ',', ' ') ?> €</p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="newsletter">
    <div class="container">
        <h2>Inscrivez-vous à notre newsletter</h2>
        <form id="newsletter-form">
            <input type="email" name="email" placeholder="example@example.com" required>
            <button type="submit">S'abonner</button>
        </form>
        <p id="newsletter-message"></p>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
