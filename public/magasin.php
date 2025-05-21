<?php
require_once __DIR__ . '/../includes/header.php';


if (!isset($_GET['categorie'])) {
    header('Location: ../templates/page_en_construction.php');
    exit;
}

$categorie_slug = $_GET['categorie'];

// Fonction pour transformer le slug en nom lisible
function slugToNom($slug) {
    return str_replace(
        ['homme', 'femme'],
        ['Homme', 'Femme'],
        ucfirst(strtolower($slug))
    );
}

$nom_categorie = slugToNom($categorie_slug);

// Récupération de toutes les catégories pour retrouver celle demandée
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

$categorie = null;
foreach ($categories as $cat) {
    $slug_categorie = strtolower(str_replace(
        [' ', "'", 'é','è','ê','ë','à','â','ä','ô','ö','î','ï','ù','û','ü','ç'],
        ['','','e','e','e','e','a','a','a','o','o','i','i','u','u','u','c'],
        $cat['nom']
    ));

    if ($slug_categorie === $categorie_slug) {
        $categorie = $cat;
        break;
    }
}

if (!$categorie) {
    header("Location: ../templates/page_en_construction.php?page=$categorie_slug");
    exit;
}

// Préparation des filtres dynamiques
$where = "WHERE actif = 1 AND categorie_id = ?";
$params = [$categorie['id']];

if (!empty($_GET['taille'])) {
    $where .= " AND id IN (SELECT produit_id FROM produits_tailles WHERE taille = ?)";
    $params[] = $_GET['taille'];
}

if (!empty($_GET['couleur'])) {
    $where .= " AND id IN (SELECT produit_id FROM produits_couleurs WHERE couleur = ?)";
    $params[] = $_GET['couleur'];
}

if (!empty($_GET['prix_min'])) {
    $where .= " AND prix >= ?";
    $params[] = $_GET['prix_min'];
}

if (!empty($_GET['prix_max'])) {
    $where .= " AND prix <= ?";
    $params[] = $_GET['prix_max'];
}

// Gestion du tri
$order = "";
if (!empty($_GET['tri'])) {
    switch ($_GET['tri']) {
        case 'prix_asc': $order = "ORDER BY prix ASC"; break;
        case 'prix_desc': $order = "ORDER BY prix DESC"; break;
        case 'nouveautes': $order = "ORDER BY id DESC"; break;
    }
}

// Récupération des produits filtrés
$stmt = $pdo->prepare("SELECT * FROM produits $where $order");
$stmt->execute($params);
$produits = $stmt->fetchAll();

if (count($produits) == 0) {
    echo "Aucun produit ne correspond a votre recherche, ou la catégorie sur laquelle vous cliquez est hors projet.<br>";
    echo '<a href="javascript:history.back()" class="btn">Revenir en arrière</a>';
    exit;
}
?>

<section class="produits-categorie">
    <div class="container">
        <h1>Vêtements <?= htmlspecialchars($categorie['nom']) ?></h1>

        <!-- Filtres -->
        <div class="filtres" >
            <form method="get" action="magasin.php">
                <input type="hidden" name="categorie" value="<?= htmlspecialchars($categorie_slug) ?>">

                <label>Taille :</label>
                <select name="taille">
                    <option value="">Toutes</option>
                    <?php foreach (['XS', 'S', 'M', 'L', 'XL'] as $taille): ?>
                        <option value="<?= $taille ?>" <?= ($_GET['taille'] ?? '') === $taille ? 'selected' : '' ?>><?= $taille ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Couleur :</label>
                <select name="couleur">
                    <option value="">Toutes</option>
                    <?php foreach (['Rouge', 'Bleu', 'Blanc', 'Noir'] as $couleur): ?>
                        <option value="<?= $couleur ?>" <?= ($_GET['couleur'] ?? '') === $couleur ? 'selected' : '' ?>><?= $couleur ?></option>
                    <?php endforeach; ?>
                </select>

                <label>Prix min :</label>
                <input type="number" name="prix_min" value="<?= htmlspecialchars($_GET['prix_min'] ?? '') ?>">

                <label>Prix max :</label>
                <input type="number" name="prix_max" value="<?= htmlspecialchars($_GET['prix_max'] ?? '') ?>">

                <label>Tri :</label>
                <select name="tri">
                    <option value="">Par défaut</option>
                    <option value="prix_asc" <?= ($_GET['tri'] ?? '') === 'prix_asc' ? 'selected' : '' ?>>Prix croissant</option>
                    <option value="prix_desc" <?= ($_GET['tri'] ?? '') === 'prix_desc' ? 'selected' : '' ?>>Prix décroissant</option>
                    <option value="nouveautes" <?= ($_GET['tri'] ?? '') === 'nouveautes' ? 'selected' : '' ?>>Nouveautés</option>
                </select>

                <button type="submit">Filtrer</button>
            </form>

        </div>            

        <!-- Grille des produits -->
        <div class="produits-grid">
            <?php foreach ($produits as $produit): ?>
                <div class="produit-carte <?= $produit['stock'] <= 0 ? 'rupture' : '' ?>">
                    <a href="produit.php?id=<?= $produit['id'] ?>">
                        <div class="image-wrapper">
                            <img src="<?= SITE_URL ?>assets/images/produits/<?= htmlspecialchars($produit['image']) ?>" alt="<?= htmlspecialchars($produit['nom']) ?>">
                            <?php if ($produit['stock'] <= 0): ?>
                                <div class="badge-rupture">Rupture de stock</div>
                            <?php endif; ?>
                        </div>
                        <h3><?= htmlspecialchars($produit['nom']) ?></h3>
                        <p><?= number_format($produit['prix'], 2, ',', ' ') ?> €</p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
