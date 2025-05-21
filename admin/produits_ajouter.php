<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$erreurs = [];
$success = '';

// Récupération des catégories
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $categorie_id = intval($_POST['categorie_id'] ?? 0);
    $tailles = $_POST['tailles'] ?? [];
    $couleurs = $_POST['couleurs'] ?? [];
    $image = $_FILES['image'] ?? null;

    // Validation basique
    if (!$nom) $erreurs[] = 'Nom obligatoire.';
    if (!$description) $erreurs[] = 'Description obligatoire.';
    if ($prix <= 0) $erreurs[] = 'Prix invalide.';
    if ($stock < 0) $erreurs[] = 'Stock invalide.';
    if (!$categorie_id) $erreurs[] = 'Catégorie requise.';
    if (empty($tailles)) $erreurs[] = 'Sélectionnez au moins une taille.';
    if (empty($couleurs)) $erreurs[] = 'Sélectionnez au moins une couleur.';
    if (!$image || $image['error'] !== 0) $erreurs[] = 'Image obligatoire.';
    elseif (pathinfo($image['name'], PATHINFO_EXTENSION) !== 'png') $erreurs[] = 'Image en .png uniquement.';

    // Traitement
    if (empty($erreurs)) {
        $nom_fichier = strtolower(str_replace([' ', 'é', 'è', 'ê', 'à', 'ç', "'"], ['','e','e','e','a','c',''], $nom)) . '.png';
        move_uploaded_file($image['tmp_name'], __DIR__ . '/../assets/images/produits/' . $nom_fichier);

        // Insertion produit
        $stmt = $pdo->prepare("INSERT INTO produits (nom, description, prix, stock, image, categorie_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $description, $prix, $stock, $nom_fichier, $categorie_id]);
        $produit_id = $pdo->lastInsertId();

        // Insertion tailles
        $stmtTaille = $pdo->prepare("INSERT INTO produits_tailles (produit_id, taille) VALUES (?, ?)");
        foreach ($tailles as $taille) {
            $stmtTaille->execute([$produit_id, $taille]);
        }

        // Insertion couleurs
        $stmtCouleur = $pdo->prepare("INSERT INTO produits_couleurs (produit_id, couleur) VALUES (?, ?)");
        foreach ($couleurs as $couleur) {
            $stmtCouleur->execute([$produit_id, $couleur]);
        }

        $success = 'Produit ajouté avec succès.';
    }
}
?>

<section class="admin-ajout-produit">
    <div class="container">
        <h1>Ajouter un produit</h1>

        <?php if (!empty($erreurs)): ?>
            <div class="erreurs">
                <ul>
                    <?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
                </ul>
            </div>
        <?php elseif ($success): ?>
            <p class="message-success"><?= $success ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="text" name="nom" placeholder="Nom du produit" required>

            <textarea name="description" placeholder="Description" required></textarea>

            <input type="number" step="0.01" name="prix" placeholder="Prix" required>

            <input type="number" name="stock" placeholder="Stock" required>

            <label for="categorie_id">Catégorie :</label>
            <select name="categorie_id" required>
                <option value="">-- Choisir une catégorie --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="image">Image (.png uniquement) :</label>
            <input type="file" name="image" accept="image/png" required>

            <label for="tailles">Tailles disponibles :</label>
            <select name="tailles[]" multiple required>
                <option value="XS">XS</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
            </select>

            <label for="couleurs">Couleurs disponibles :</label>
            <select name="couleurs[]" multiple required>
                <option value="Noir">Noir</option>
                <option value="Blanc">Blanc</option>
                <option value="Bleu">Bleu</option>
                <option value="Rouge">Rouge</option>
            </select>

            <button type="submit">Enregistrer</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
