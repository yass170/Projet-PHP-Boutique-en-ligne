<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$erreurs = [];
$success = '';

// Clés attendues dans la table parametres_site
$cles = ['site_name'];

// Récupérer les valeurs actuelles
$parametres = [];
$stmt = $pdo->query("SELECT cle, valeur FROM parametres_site");
foreach ($stmt->fetchAll() as $row) {
    $parametres[$row['cle']] = $row['valeur'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($cles as $cle) {
        $valeur = trim($_POST[$cle] ?? '');
        $stmt = $pdo->prepare("UPDATE parametres_site SET valeur = ? WHERE cle = ?");
        $stmt->execute([$valeur, $cle]);
        $parametres[$cle] = $valeur;
    }
    $success = 'Paramètres mis à jour avec succès.';
}
?>

<section class="admin-settings">
    <div class="container">
        <h1>Paramètres du site</h1>

        <?php if ($success): ?>
            <p class="message-success"><?= $success ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="site_name">Nom du site :</label>
            <input type="text" name="site_name" value="<?= htmlspecialchars($parametres['site_name'] ?? '') ?>">

            <button type="submit">Enregistrer</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
