<?php
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['utilisateur'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['utilisateur']['id'];

// Récupérer les articles du panier avec le stock actuel
$stmt = $pdo->prepare("
    SELECT p.id AS produit_id, p.nom, p.image, p.prix, p.stock,
           pa.id, pa.taille, pa.couleur, pa.quantite
    FROM paniers pa
    JOIN produits p ON p.id = pa.produit_id
    WHERE pa.utilisateur_id = ?
");
$stmt->execute([$user_id]);
$articles = $stmt->fetchAll();

$total = 0;
$erreur_stock = false;

if (isset($_GET['message'])) {
    if ($_GET['message'] === 'article_supprime') {
        echo "<p class='message-success'>Article supprimé du panier.</p>";
    } elseif ($_GET['message'] === 'erreur') {
        echo "<p class='message-erreur'>Une erreur est survenue.</p>";
    }
}
?>

<section class="panier">
    <div class="container">
        <h1>Mon panier</h1>

        <?php if (empty($articles)): ?>
            <p>Votre panier est vide.</p>
        <?php else: ?>
            <table class="table-panier">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Taille</th>
                        <th>Couleur</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): 
                        $stock_dispo = $article['stock'];
                        $quantite = $article['quantite'];
                        $sous_total = $article['prix'] * $quantite;
                        $total += $sous_total;

                        $stock_insuffisant = $quantite > $stock_dispo;
                        if ($stock_insuffisant) $erreur_stock = true;
                    ?>
                        <tr class="<?= $stock_insuffisant ? 'stock-insuffisant' : '' ?>">
                            <td>
                                <img src="<?= SITE_URL ?>assets/images/produits/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['nom']) ?>" width="60">
                                <?= htmlspecialchars($article['nom']) ?>
                                <?php if ($stock_insuffisant): ?>
                                    <span class="alerte-stock">Stock insuffisant (max <?= $stock_dispo ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($article['taille']) ?></td>
                            <td><?= htmlspecialchars($article['couleur']) ?></td>
                            <td><?= $quantite ?></td>
                            <td><?= number_format($sous_total, 2, ',', ' ') ?> €</td>
                            <td>
                                <form method="post" action="../ajax/panier_supprimer.php" onsubmit="return confirm('Supprimer cet article ?');">
                                    <input type="hidden" name="id_panier" value="<?= $article['id'] ?>">
                                    <button type="submit" class="btn-supprimer">🗑</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="panier-resume">
                <p>Total : <strong><?= number_format($total, 2, ',', ' ') ?> €</strong></p>

                <?php if ($erreur_stock): ?>
                    <p class="message-erreur">Certains articles dépassent le stock disponible. Veuillez les modifier avant de passer commande.</p>
                <?php endif; ?>

                <div class="actions-panier">
                    <a href="javascript:history.back()" class="btn">Continuer mes achats</a>
                    <?php if (!$erreur_stock): ?>
                        <a href="commande.php" class="btn">Confirmer la commande</a>
                    <?php else: ?>
                        <button class="btn disabled" disabled>Commande indisponible</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
