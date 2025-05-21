<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

// Messages de retour
$message = '';
if (isset($_GET['message'])) {
    if ($_GET['message'] === 'produit_supprime') {
        $message = "<p class='message-success'>Produit supprimé avec succès.</p>";
    } elseif ($_GET['message'] === 'erreur') {
        $message = "<p class='message-erreur'>Une erreur est survenue.</p>";
    }
}

// Récupération des produits
$stmt = $pdo->query("SELECT p.*, c.nom AS categorie_nom 
                     FROM produits p
                     LEFT JOIN categories c ON p.categorie_id = c.id
                     ORDER BY p.id DESC");
$produits = $stmt->fetchAll();
?>

<section class="admin-produits">
    <div class="container">
        <h1>Gestion des produits</h1>

        <?= $message ?>

        <a href="produits_ajouter.php" class="btn">Ajouter un produit</a>
        <div class="box">
    <input type="text" class="input" id="search-input" placeholder="Rechercher..." />
    <i class="fa fa-search"></i>
</div>

        <table class="table-admin">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Catégorie</th>
                    <th>Prix</th>
                    <th>Stock</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produits as $produit): ?>
                    <tr>
                        <td><?= $produit['id'] ?></td>
                        <td><?= htmlspecialchars($produit['nom']) ?></td>
                        <td><?= htmlspecialchars($produit['categorie_nom'] ?? 'Non défini') ?></td>
                        <td><?= number_format($produit['prix'], 2, ',', ' ') ?> €</td>
                        <td><?= $produit['stock'] ?></td>
                        <td><?= $produit['actif'] ? 'Actif' : 'Inactif' ?></td>
                        <td>
                            <a href="produits_modifier.php?id=<?= $produit['id'] ?>">Modifier</a> |
                            <a href="produits_supprimer.php?id=<?= $produit['id'] ?>" onclick="return confirm('Confirmer la suppression de ce produit ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
