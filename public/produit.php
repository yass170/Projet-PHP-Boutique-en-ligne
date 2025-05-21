<?php
require_once __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "<p>Produit introuvable.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Récupération du produit
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ? AND actif = 1");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) {
    echo "<p>Produit introuvable ou inactif.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Incrément du compteur de vues
$pdo->prepare("UPDATE produits SET vues = vues + 1 WHERE id = ?")->execute([$id]);

// Récupération des tailles disponibles
$stmt = $pdo->prepare("SELECT DISTINCT taille FROM produits_tailles WHERE produit_id = ?");
$stmt->execute([$id]);
$tailles = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Récupération des couleurs disponibles
$stmt = $pdo->prepare("SELECT DISTINCT couleur FROM produits_couleurs WHERE produit_id = ?");
$stmt->execute([$id]);
$couleurs = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Produits similaires
$stmt = $pdo->prepare("SELECT * FROM produits WHERE categorie_id = ? AND id != ? AND actif = 1 ORDER BY vues DESC LIMIT 4");
$stmt->execute([$produit['categorie_id'], $id]);
$similaires = $stmt->fetchAll();

// Traitement de l'ajout au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['utilisateur'])) {
    $produit_id = (int)($_POST['produit_id'] ?? 0);
    $taille = $_POST['taille'] ?? '';
    $couleur = $_POST['couleur'] ?? '';
    $quantite = (int)($_POST['quantite'] ?? 1);
    $utilisateur_id = $_SESSION['utilisateur']['id'];

    if ($quantite > $produit['stock']) {
        echo "<p class='container erreur'>La quantité demandée dépasse le stock disponible.</p>";
    } elseif ($produit_id > 0 && $quantite > 0 && $taille && $couleur) {
        $stmt = $pdo->prepare("SELECT id, quantite FROM paniers WHERE utilisateur_id = ? AND produit_id = ? AND taille = ? AND couleur = ?");
        $stmt->execute([$utilisateur_id, $produit_id, $taille, $couleur]);
        $existant = $stmt->fetch();

        if ($existant) {
            $newQuantite = $existant['quantite'] + $quantite;
            if ($newQuantite > $produit['stock']) {
                echo "<p class='container erreur'>Stock insuffisant pour cette quantité ajoutée.</p>";
            } else {
                $pdo->prepare("UPDATE paniers SET quantite = ? WHERE id = ?")
                    ->execute([$newQuantite, $existant['id']]);
                header("Location: produit.php?id=" . $produit_id . "&ajout=ok");
                exit;
            }
        } else {
            $pdo->prepare("INSERT INTO paniers (utilisateur_id, produit_id, taille, couleur, quantite) VALUES (?, ?, ?, ?, ?)")
                ->execute([$utilisateur_id, $produit_id, $taille, $couleur, $quantite]);
            header("Location: produit.php?id=" . $produit_id . "&ajout=ok");
            exit;
        }
    }
}
?>

<section class="fiche-produit">
    <div class="container">
        <div class="produit-detail">
            <div class="produit-image <?= $produit['stock'] <= 0 ? 'rupture' : '' ?>">
                <img src="<?= SITE_URL ?>assets/images/produits/<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
                <?php if ($produit['stock'] <= 0): ?>
                    <div class="etiquette-rupture">Rupture de stock</div>
                <?php endif; ?>
            </div>

            <div class="infos">
                <h1><?= htmlspecialchars($produit['nom']) ?></h1>
                <p class="reference">Réf. produit : #<?= $produit['id'] ?></p>
                <p class="prix"><?= number_format($produit['prix'], 2, ',', ' ') ?> €</p>
                <p><?= nl2br(htmlspecialchars($produit['description'])) ?></p>

                <?php if ($produit['stock'] <= 0): ?>
                    <p class="rupture-message">Ce produit est actuellement en rupture de stock.</p>
                <?php else: ?>
                    <form method="post" id="ajout-panier-form">
                        <input type="hidden" name="produit_id" value="<?= $produit['id'] ?>">

                        <label for="taille">Taille :</label>
                        <select name="taille" required>
                            <?php foreach ($tailles as $taille): ?>
                                <option value="<?= $taille ?>"><?= $taille ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label for="couleur">Couleur :</label>
                        <select name="couleur" required>
                            <?php foreach ($couleurs as $couleur): ?>
                                <option value="<?= $couleur ?>"><?= $couleur ?></option>
                            <?php endforeach; ?>
                        </select>

                        <label for="quantite">Quantité :</label>
                        <input type="number" name="quantite" value="1" min="1" max="<?= $produit['stock'] ?>" required>

                        <?php if (isset($_SESSION['utilisateur'])): ?>
                            <button type="submit">Ajouter au panier</button>
                        <?php else: ?>
                            <div class="modal-ajout">
                                <button type="button" onclick="showConnexionModal()">Ajouter au panier</button>
                            </div>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($similaires): ?>
            <div class="produits-similaires">
                <h2>Produits similaires</h2>
                <div class="produits-grid">
                    <?php foreach ($similaires as $s): ?>
                        <div class="produit-carte <?= $s['stock'] <= 0 ? 'rupture' : '' ?>">
                            <a href="produit.php?id=<?= $s['id'] ?>">
                                <img src="<?= SITE_URL ?>assets/images/produits/<?= htmlspecialchars($s['image']) ?>" alt="<?= htmlspecialchars($s['nom']) ?>">
                                <?php if ($s['stock'] <= 0): ?>
                                    <div class="etiquette-rupture">Rupture</div>
                                <?php endif; ?>
                                <h3><?= htmlspecialchars($s['nom']) ?></h3>
                                <p><?= number_format($s['prix'], 2, ',', ' ') ?> €</p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function showConnexionModal() {
    alert("Vous devez être connecté pour ajouter un article au panier.");
    if (confirm("Se connecter maintenant ?")) {
        window.location.href = "connexion.php";
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
