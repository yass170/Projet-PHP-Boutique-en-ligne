<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

// Message de confirmation
$message = '';
if (isset($_GET['message'])) {
    if ($_GET['message'] === 'utilisateur_supprime') {
        $message = "<p class='message-success'>Utilisateur supprimé avec succès.</p>";
    } elseif ($_GET['message'] === 'erreur') {
        $message = "<p class='message-erreur'>Une erreur est survenue lors de la suppression.</p>";
    }
}

// Récupération des utilisateurs
$stmt = $pdo->query("SELECT * FROM utilisateurs ORDER BY id DESC");
$utilisateurs = $stmt->fetchAll();
?>

<section class="admin-utilisateurs">
    <div class="container">
        <h1>Gestion des utilisateurs</h1>

        <?= $message ?>

        <table class="table-admin">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Date d'inscription</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['nom']) ?></td>
                        <td><?= htmlspecialchars($user['prenom']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= date('d/m/Y', strtotime($user['date_inscription'])) ?></td>
                        <td><?= $user['role'] ?></td>
                        <td>
                            <a href="utilisateur_details.php?id=<?= $user['id'] ?>">Voir</a> |
                            <form method="post" action="../ajax/utilisateur_supprimer.php" style="display:inline;" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                <input type="hidden" name="id_utilisateur" value="<?= $user['id'] ?>">
                                <button type="submit" class="lien-btn">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
