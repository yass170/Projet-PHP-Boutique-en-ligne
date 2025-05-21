<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$erreurs = [];
$success = '';

// Fonction de normalisation du nom
function normaliserNom($str) {
    $str = strtolower($str);
    $str = str_replace([' ', "'", 'é','è','ê','ë','à','â','ä','ô','ö','î','ï','ù','û','ü','ç'],
                       ['', '', 'e','e','e','e','a','a','a','o','o','i','i','u','u','u','c'], $str);
    return $str;
}

// ID à modifier
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$categorie = $stmt->fetch();

if (!$categorie) {
    echo "<p>Catégorie introuvable.</p>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

// Récupérer les autres catégories pour liste déroulante
$categories_existantes = $pdo->prepare("SELECT * FROM categories WHERE id != ? ORDER BY nom");
$categories_existantes->execute([$id]);
$categories_existantes = $categories_existantes->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

    if (!$nom) {
        $erreurs[] = 'Le nom est requis.';
    }

    if (empty($erreurs)) {
        // Mise à jour BDD
        $stmt = $pdo->prepare("UPDATE categories SET nom = ?, parent_id = ? WHERE id = ?");
        $stmt->execute([$nom, $parent_id, $id]);

        // Gestion image si nouvelle image uploadée
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $type = mime_content_type($_FILES['image']['tmp_name']);
            if ($type === 'image/jpeg') {
                $nom_fichier = normaliserNom($nom) . '.jpg';
                $destination = __DIR__ . '/../assets/images/categories/' . $nom_fichier;
                move_uploaded_file($_FILES['image']['tmp_name'], $destination);
            } else {
                $erreurs[] = "L'image doit être au format JPG.";
            }
        }

        if (empty($erreurs)) {
            $success = 'Catégorie mise à jour avec succès.';
        }
    }
}
?>

<section class="admin-modifier-categorie">
    <div class="container">
        <h1>Modifier une catégorie</h1>

        <?php if (!empty($erreurs)): ?>
            <div class="erreurs">
                <ul><?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
            </div>
        <?php elseif ($success): ?>
            <p class="message-success"><?= $success ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="text" name="nom" value="<?= htmlspecialchars($categorie['nom']) ?>" required>

            <label>Nouvelle image (JPG uniquement, 256x256px) :</label>
            <input type="file" name="image" accept=".jpg,.jpeg">

            <label>Catégorie parente (optionnelle) :</label>
            <select name="parent_id">
                <option value="">-- Aucune --</option>
                <?php foreach ($categories_existantes as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $categorie['parent_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Enregistrer les modifications</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
