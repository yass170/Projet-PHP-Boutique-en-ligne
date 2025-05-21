<?php
require_once __DIR__ . '/../includes/header.php';

if (!isset($_SESSION['utilisateur'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['utilisateur']['id'];

// Récupérer les articles du panier
$stmt = $pdo->prepare("SELECT pa.*, p.nom, p.prix, p.stock FROM paniers pa
                       JOIN produits p ON p.id = pa.produit_id
                       WHERE pa.utilisateur_id = ?");
$stmt->execute([$user_id]);
$articles = $stmt->fetchAll();

if (empty($articles)) {
    echo "<p class='container'>Votre panier est vide.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$total = 0;
foreach ($articles as $a) {
    $total += $a['prix'] * $a['quantite'];
}

// Traitement de la commande
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();

    try {
        // Vérification des stocks
        foreach ($articles as $a) {
            if ($a['quantite'] > $a['stock']) {
                throw new Exception("Le produit « " . htmlspecialchars($a['nom']) . " » est en rupture de stock ou quantité insuffisante.");
            }
        }

        // Créer la commande
        $stmt = $pdo->prepare("INSERT INTO commandes (utilisateur_id, total) VALUES (?, ?)");
        $stmt->execute([$user_id, $total]);
        $commande_id = $pdo->lastInsertId();

        // Enregistrer les produits de la commande + mettre à jour le stock
        $insert = $pdo->prepare("INSERT INTO commandes_produits (commande_id, produit_id, taille, couleur, quantite, prix_unitaire)
                                 VALUES (?, ?, ?, ?, ?, ?)");
        $update = $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");

        foreach ($articles as $a) {
            $insert->execute([
                $commande_id,
                $a['produit_id'],
                $a['taille'],
                $a['couleur'],
                $a['quantite'],
                $a['prix']
            ]);

            $update->execute([$a['quantite'], $a['produit_id']]);
        }

        // Supprimer le panier
        $pdo->prepare("DELETE FROM paniers WHERE utilisateur_id = ?")->execute([$user_id]);

        // Envoi mail
        require_once __DIR__ . '/../includes/mailer.php';

        $body = "Bonjour " . htmlspecialchars($_SESSION['utilisateur']['prenom']) . ",\n\n";
        $body .= "Voici le récapitulatif de votre commande #$commande_id :\n\n";
        foreach ($articles as $a) {
            $body .= "- " . $a['nom'] . " (Taille : " . $a['taille'] . ", Couleur : " . $a['couleur'] . ") × " . $a['quantite'] . " → " . number_format($a['prix'], 2, ',', ' ') . " €\n";
        }
        $body .= "\nTotal : " . number_format($total, 2, ',', ' ') . " €\n\n";
        $body .= "Merci pour votre commande (simulation).\n";

        envoyerMail($_SESSION['utilisateur']['email'], "Confirmation commande #$commande_id", $body);

        $pdo->commit();

        header("Location: mes_commandes.php?confirm=$commande_id");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<p class='container erreur'>Erreur : " . $e->getMessage() . "</p>";
    }
}
?>

<section class="commande">
    <div class="container">
        <h1>Résumé de votre commande</h1>
        <ul class="commande-liste">
            <?php foreach ($articles as $a): ?>
                <li>
                    <?= htmlspecialchars($a['nom']) ?> (Taille : <?= $a['taille'] ?>, Couleur : <?= $a['couleur'] ?>)
                    × <?= $a['quantite'] ?> → <?= number_format($a['prix'], 2, ',', ' ') ?> €
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="total">Total : <strong><?= number_format($total, 2, ',', ' ') ?> €</strong></p>

        <form method="post">
            <button type="submit">Confirmer la commande</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
