<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: produits.php');
    exit;
}

// Récupération du produit
$stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch();

if (!$produit) {
    echo "<p class='container'>Produit introuvable.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Catégories
$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll();

// Tailles & couleurs actuelles
$tailles_actuelles = $pdo->prepare("SELECT taille FROM produits_tailles WHERE produit_id = ?");
$tailles_actuelles->execute([$id]);
$tailles_actuelles = array_column($tailles_actuelles->fetchAll(), 'taille');

$couleurs_actuelles = $pdo->prepare("SELECT couleur FROM produits_couleurs WHERE produit_id = ?");
$couleurs_actuelles->execute([$id]);
$couleurs_actuelles = array_column($couleurs_actuelles->fetchAll(), 'couleur');

$success = '';
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $categorie_id = intval($_POST['categorie_id'] ?? 0);
    $tailles = $_POST['tailles'] ?? [];
    $couleurs = $_POST['couleurs'] ?? [];

    $nouvelle_image = $_FILES['image'] ?? null;
    $image_filename = $produit['image'];

    if ($nouvelle_image && $nouvelle_image['error'] === 0) {
        if (pathinfo($nouvelle_image['name'], PATHINFO_EXTENSION) !== 'png') {
            $erreurs[] = 'L’image doit être un fichier .png';
        } else {
            $image_filename = strtolower(str_replace([' ', 'é', 'è', 'ê', 'à', 'ç', "'"], ['','e','e','e','a','c',''], $nom)) . '.png';
            move_uploaded_file($nouvelle_image['tmp_name'], __DIR__ . '/../assets/images/produits/' . $image_filename);
        }
    }

    if (empty($nom) || empty($description) || $prix <= 0 || $stock < 0 || !$categorie_id || empty($tailles) || empty($couleurs)) {
        $erreurs[] = 'Tous les champs sont obligatoires.';
    }

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("UPDATE produits SET nom = ?, description = ?, prix = ?, stock = ?, image = ?, categorie_id = ? WHERE id = ?");
        $stmt->execute([$nom, $description, $prix, $stock, $image_filename, $categorie_id, $id]);

        // Supprimer les anciennes tailles et couleurs
        $pdo->prepare("DELETE FROM produits_tailles WHERE produit_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM produits_couleurs WHERE produit_id = ?")->execute([$id]);

        // Réinsérer tailles
        $stmtT = $pdo->prepare("INSERT INTO produits_tailles (produit_id, taille) VALUES (?, ?)");
        foreach ($tailles as $taille) {
            $stmtT->execute([$id, $taille]);
        }

        // Réinsérer couleurs
        $stmtC = $pdo->prepare("INSERT INTO produits_couleurs (produit_id, couleur) VALUES (?, ?)");
        foreach ($couleurs as $couleur) {
            $stmtC->execute([$id, $couleur]);
        }

        $success = 'Produit mis à jour avec succès.';
    }
}
?>

<section class="admin-modifier-produit">
    <div class="container">
        <h1>Modifier le produit</h1>

        <?php if (!empty($erreurs)): ?>
            <div class="erreurs">
                <ul><?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
            </div>
        <?php elseif ($success): ?>
            <p class="message-success"><?= $success ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="text" name="nom" value="<?= htmlspecialchars($produit['nom']) ?>" required>
            <textarea name="description" required><?= htmlspecialchars($produit['description']) ?></textarea>
            <input type="number" step="0.01" name="prix" value="<?= $produit['prix'] ?>" required>
            <input type="number" name="stock" value="<?= $produit['stock'] ?>" required>

            <label for="categorie_id">Catégorie :</label>
            <select name="categorie_id" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $produit['categorie_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <p>Image actuelle : <?= htmlspecialchars($produit['image']) ?></p>
            <input type="file" name="image" accept="image/png">

            <label>Tailles :</label>
            <select name="tailles[]" multiple required>
                <?php foreach (['XS','S','M','L','XL'] as $taille): ?>
                    <option value="<?= $taille ?>" <?= in_array($taille, $tailles_actuelles) ? 'selected' : '' ?>><?= $taille ?></option>
                <?php endforeach; ?>
            </select>

            <label>Couleurs :</label>
            <select name="couleurs[]" multiple required>
                <?php foreach (['Noir','Blanc','Bleu','Rouge'] as $couleur): ?>
                    <option value="<?= $couleur ?>" <?= in_array($couleur, $couleurs_actuelles) ? 'selected' : '' ?>><?= $couleur ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Enregistrer les modifications</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
