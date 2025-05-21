<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: utilisateurs.php?message=erreur');
    exit;
}

// Récupérer l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: utilisateurs.php?message=erreur');
    exit;
}

// Récupérer ses commandes
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE utilisateur_id = ? ORDER BY date_commande DESC");
$stmt->execute([$id]);
$commandes = $stmt->fetchAll();

// Changement de rôle
if (isset($_POST['changer_role']) && in_array($_POST['nouveau_role'], ['admin', 'utilisateur'])) {
    $pdo->prepare("UPDATE utilisateurs SET role = ? WHERE id = ?")->execute([$_POST['nouveau_role'], $id]);
    $user['role'] = $_POST['nouveau_role'];
    $message = "<p class='message-success'>Rôle mis à jour.</p>";
}
?>

<section class="admin-details-user">
    <div class="container">
        <h1>Détails de l’utilisateur #<?= $user['id'] ?></h1>

        <?= $message ?? '' ?>

        <p><strong>Nom :</strong> <?= htmlspecialchars($user['nom']) ?></p>
        <p><strong>Prénom :</strong> <?= htmlspecialchars($user['prenom']) ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Téléphone :</strong> <?= htmlspecialchars($user['telephone']) ?></p>
        <p><strong>Date de naissance :</strong> <?= htmlspecialchars($user['date_naissance']) ?></p>
        <p><strong>Date d’inscription :</strong> <?= date('d/m/Y', strtotime($user['date_inscription'])) ?></p>
        <p><strong>Rôle :</strong> <?= htmlspecialchars($user['role']) ?></p>

        <form method="post" style="margin-top: 20px;">
            <label for="nouveau_role">Changer le rôle :</label>
            <select name="nouveau_role">
                <option value="utilisateur" <?= $user['role'] === 'utilisateur' ? 'selected' : '' ?>>Utilisateur</option>
                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
            </select>
            <button type="submit" name="changer_role">Mettre à jour</button>
        </form>

        <h2 style="margin-top: 30px;">Historique des commandes</h2>

        <?php if (empty($commandes)): ?>
            <p>Aucune commande trouvée.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($commandes as $commande): ?>
                    <li>
                        Commande #<?= $commande['id'] ?> — 
                        <?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?> — 
                        Total : <?= number_format($commande['total'], 2, ',', ' ') ?> €
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
