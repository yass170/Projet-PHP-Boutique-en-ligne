<?php
require_once __DIR__ . '/../includes/header.php';

// Redirection si non connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['utilisateur']['id'];

// Récupération des commandes
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE utilisateur_id = ? ORDER BY date_commande DESC");
$stmt->execute([$user_id]);
$commandes = $stmt->fetchAll();
?>

<section class="mes-commandes">
    <div class="container">
        <h1>Mes commandes simulées</h1>

        <?php if (empty($commandes)): ?>
            <p>Vous n'avez encore passé aucune commande.</p>
        <?php else: ?>
            <table class="table-commandes">
                <thead>
                    <tr>
                        <th>N° Commande</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Détails</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $commande): ?>
                        <tr>
                            <td>#<?= $commande['id'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?></td>
                            <td><?= number_format($commande['total'], 2, ',', ' ') ?> €</td>
                            <td><a href="mes_commandes.php?commande=<?= $commande['id'] ?>">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php
        // Détails d'une commande
        if (isset($_GET['commande'])):
            $commande_id = (int) $_GET['commande'];
            $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ? AND utilisateur_id = ?");
            $stmt->execute([$commande_id, $user_id]);
            $commande = $stmt->fetch();

            if ($commande):
                $stmt = $pdo->prepare("SELECT cp.*, p.nom FROM commandes_produits cp
                                       JOIN produits p ON cp.produit_id = p.id
                                       WHERE cp.commande_id = ?");
                $stmt->execute([$commande_id]);
                $produits = $stmt->fetchAll();
        ?>
            <h2>Détails de la commande #<?= $commande['id'] ?></h2>
            <ul class="commande-details">
                <?php foreach ($produits as $prod): ?>
                    <li>
                        <?= htmlspecialchars($prod['nom']) ?> 
                        (Taille : <?= $prod['taille'] ?>, Couleur : <?= $prod['couleur'] ?>) × <?= $prod['quantite'] ?> 
                        → <?= number_format($prod['prix_unitaire'], 2, ',', ' ') ?> €
                    </li>
                <?php endforeach; ?>
                <li><strong>Total : <?= number_format($commande['total'], 2, ',', ' ') ?> €</strong></li>
            </ul>
        <?php
            endif;
        endif;
        ?>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
