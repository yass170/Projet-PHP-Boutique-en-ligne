<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$erreurs = [];
$success = '';

// Récupération des catégories existantes pour liste déroulante
$categories_existantes = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll();

// Fonction pour normaliser le nom de fichier
function normaliserNom($str) {
    $str = strtolower($str);
    $str = str_replace([' ', "'", 'é','è','ê','ë','à','â','ä','ô','ö','î','ï','ù','û','ü','ç'],
                       ['', '', 'e','e','e','e','a','a','a','o','o','i','i','u','u','u','c'], $str);
    return $str;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $parent_id = !empty($_POST['parent_id']) ? (int) $_POST['parent_id'] : null;

    if (!$nom) {
        $erreurs[] = 'Le nom est requis.';
    }

    $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $mime = mime_content_type($_FILES['image']['tmp_name']);
    
    if (!in_array($extension, ['jpg', 'jpeg']) || !in_array($mime, ['image/jpeg', 'image/pjpeg'])) {
        $erreurs[] = "L'image doit être au format JPG ou JPEG uniquement.";
    }

    if (empty($erreurs)) {
        $pdo->prepare("INSERT INTO categories (nom, parent_id) VALUES (?, ?)")
            ->execute([$nom, $parent_id]);

        $categorie_id = $pdo->lastInsertId();
        $nom_fichier = normaliserNom($nom) . '.jpg';

        $destination = __DIR__ . '/../assets/images/categories/' . $nom_fichier;
        move_uploaded_file($_FILES['image']['tmp_name'], $destination);

        $success = 'Catégorie ajoutée avec succès.';
    }
}
?>

<section class="admin-ajout-categorie">
    <div class="container">
        <h1>Ajouter une catégorie</h1>

        <?php if (!empty($erreurs)): ?>
            <div class="erreurs">
                <ul><?php foreach ($erreurs as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
            </div>
        <?php elseif ($success): ?>
            <p class="message-success"><?= $success ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="text" name="nom" placeholder="Nom de la catégorie" required>

            <label>Image (format JPG, 256x256px recommandé) :</label>
            <input type="file" name="image" accept=".jpg,.jpeg" required>

            <label>Catégorie parente (optionnelle) :</label>
            <select name="parent_id">
                <option value="">-- Aucune --</option>
                <?php foreach ($categories_existantes as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Ajouter</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
