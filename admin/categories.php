<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

// Message utilisateur
$message = '';
if (isset($_GET['message'])) {
    if ($_GET['message'] === 'categorie_supprimee') {
        $message = "<p class='message-success'>Catégorie supprimée avec succès.</p>";
    } elseif ($_GET['message'] === 'categorie_utilisee') {
        $message = "<p class='message-erreur'>Impossible de supprimer : des produits utilisent cette catégorie.</p>";
    } elseif ($_GET['message'] === 'erreur') {
        $message = "<p class='message-erreur'>Une erreur est survenue.</p>";
    }
}

// Récupérer les catégories
$stmt = $pdo->query("SELECT c1.*, c2.nom AS parent_nom 
                     FROM categories c1 
                     LEFT JOIN categories c2 ON c1.parent_id = c2.id 
                     ORDER BY c1.id DESC");
$categories = $stmt->fetchAll();
?>

<section class="admin-categories">
    <div class="container">
        <h1>Gestion des catégories</h1>

        <?= $message ?>

        <a href="categories_ajouter.php" class="btn">Ajouter une catégorie</a>

        <table class="table-admin">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Catégorie parente</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $categorie): ?>
                    <tr>
                        <td><?= $categorie['id'] ?></td>
                        <td><?= htmlspecialchars($categorie['nom']) ?></td>
                        <td><?= htmlspecialchars($categorie['parent_nom'] ?? 'Aucune') ?></td>
                        <td>
                            <a href="categories_modifier.php?id=<?= $categorie['id'] ?>">Modifier</a> |
                            <a href="categories_supprimer.php?id=<?= $categorie['id'] ?>" onclick="return confirm('Supprimer cette catégorie ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
