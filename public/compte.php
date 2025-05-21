<?php
require_once __DIR__ . '/../includes/header.php';

// Redirection si non connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['utilisateur']['id'];
$success = '';
$erreurs = [];

// Modifier les infos
if (isset($_POST['action']) && $_POST['action'] === 'modifier_infos') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);

    if (!$nom) $erreurs['nom'] = 'Champ obligatoire.';
    if (!$prenom) $erreurs['prenom'] = 'Champ obligatoire.';
    if (!$telephone || !preg_match('/^[0-9]{10}$/', $telephone)) $erreurs['telephone'] = 'Téléphone invalide.';

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, telephone = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $telephone, $user_id]);
        $_SESSION['utilisateur']['nom'] = $nom;
        $_SESSION['utilisateur']['prenom'] = $prenom;
        $success = 'Informations mises à jour.';
    }
}

// Modifier mot de passe
if (isset($_POST['action']) && $_POST['action'] === 'modifier_mdp') {
    $ancien = $_POST['ancien_mdp'];
    $nouveau = $_POST['nouveau_mdp'];
    $confirm = $_POST['confirm_mdp'];

    $stmt = $pdo->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id = ?");
    $stmt->execute([$user_id]);
    $hash = $stmt->fetchColumn();

    if (!password_verify($ancien, $hash)) {
        $erreurs['ancien'] = 'Mot de passe actuel incorrect.';
    } elseif ($nouveau !== $confirm) {
        $erreurs['confirm'] = 'Les mots de passe ne correspondent pas.';
    } elseif (strlen($nouveau) < 8 || !preg_match('/[A-Z]/', $nouveau) || !preg_match('/[0-9]/', $nouveau)) {
        $erreurs['nouveau'] = 'Mot de passe trop faible.';
    } else {
        $newHash = password_hash($nouveau, PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?")->execute([$newHash, $user_id]);
        $success = 'Mot de passe mis à jour.';
    }
}

// Supprimer compte
if (isset($_POST['action']) && $_POST['action'] === 'supprimer_compte') {
    $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?")->execute([$user_id]);
    session_destroy();
    header('Location: index.php?deleted=1');
    exit;
}

// Récupération des infos
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<section class="mon-compte">
    <div class="container">
        <h1>Mon compte</h1>

        <?php if ($success): ?>
            <p class="success"><?= $success ?></p>
        <?php endif; ?>

        <h2>Informations personnelles</h2>
        <form method="post">
            <input type="hidden" name="action" value="modifier_infos">
            <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" placeholder="Nom">
            <small><?= $erreurs['nom'] ?? '' ?></small>

            <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" placeholder="Prénom">
            <small><?= $erreurs['prenom'] ?? '' ?></small>

            <input type="text" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>" placeholder="Téléphone">
            <small><?= $erreurs['telephone'] ?? '' ?></small>

            <button type="submit">Modifier</button>
        </form>

        <h2>Modifier le mot de passe</h2>
        <form method="post">
            <input type="hidden" name="action" value="modifier_mdp">
            <input type="password" name="ancien_mdp" placeholder="Ancien mot de passe">
            <small><?= $erreurs['ancien'] ?? '' ?></small>

            <input type="password" name="nouveau_mdp" placeholder="Nouveau mot de passe">
            <small><?= $erreurs['nouveau'] ?? '' ?></small>

            <input type="password" name="confirm_mdp" placeholder="Confirmation du mot de passe">
            <small><?= $erreurs['confirm'] ?? '' ?></small>

            <button type="submit">Changer le mot de passe</button>
        </form>

        <h2>Supprimer mon compte</h2>
        <form method="post" onsubmit="return confirm('Confirmer la suppression de votre compte ? Cette action est irréversible.')">
            <input type="hidden" name="action" value="supprimer_compte">
            <button type="submit" class="danger">Supprimer mon compte</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
