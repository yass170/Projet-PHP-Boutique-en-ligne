<?php
require_once __DIR__ . '/../includes/header.php';

// Rediriger si déjà connecté
if (isset($_SESSION['utilisateur'])) {
    header('Location: home.php');
    exit;
}

// Traitement du formulaire
$erreurs = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $date_naissance = $_POST['date_naissance'] ?? '';
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $telephone = trim($_POST['telephone'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $confirm_mdp = $_POST['confirm_mot_de_passe'] ?? '';

    // Validation
    if (!$nom) $erreurs['nom'] = 'Nom obligatoire.';
    if (!$prenom) $erreurs['prenom'] = 'Prénom obligatoire.';
    if (!$date_naissance) $erreurs['date_naissance'] = 'Date de naissance obligatoire.';
    if (!$email) $erreurs['email'] = 'Email invalide.';
    if (!$telephone || !preg_match('/^[0-9]{10}$/', $telephone)) $erreurs['telephone'] = 'Téléphone invalide.';
    if (strlen($mot_de_passe) < 12 || !preg_match('/[A-Z]/', $mot_de_passe) || !preg_match('/[0-9]/', $mot_de_passe)|| !preg_match('/[^a-zA-Z0-9]/', $mot_de_passe)) {
        $erreurs['mot_de_passe'] = 'Mot de passe trop faible.';
    }
    if ($mot_de_passe !== $confirm_mdp) $erreurs['confirm_mot_de_passe'] = 'Les mots de passe ne correspondent pas.';

    // Insertion si pas d'erreur
    if (empty($erreurs)) {
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreurs['email'] = 'Cet e-mail est déjà utilisé.';
        } else {
            $hash = password_hash($mot_de_passe, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, date_naissance, email, telephone, mot_de_passe) 
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $date_naissance, $email, $telephone, $hash]);

            $id = $pdo->lastInsertId();
            $_SESSION['utilisateur'] = [
                'id' => $id,
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'role' => 'utilisateur'
            ];
            header('Location: home.php');
            exit;
        }
    }
}
?>

<section class="inscription">
    <div class="container">
        <h1>Inscription</h1>
        <form method="post">
            <input type="text" name="nom" placeholder="Nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
            <small><?= $erreurs['nom'] ?? '' ?></small>

            <input type="text" name="prenom" placeholder="Prénom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
            <small><?= $erreurs['prenom'] ?? '' ?></small>

            <input type="date" name="date_naissance" value="<?= htmlspecialchars($_POST['date_naissance'] ?? '') ?>">
            <small><?= $erreurs['date_naissance'] ?? '' ?></small>

            <input type="email" name="email" placeholder="Adresse e-mail" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            <small><?= $erreurs['email'] ?? '' ?></small>

            <input type="text" name="telephone" placeholder="Téléphone (10 chiffres)" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
            <small><?= $erreurs['telephone'] ?? '' ?></small>
            <h3>Votre mot de passe doit contenir : 12 caractères, au moins une majuscule, au moins 1 chiffre, au moins un caractère spécial.</h3>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe">
            <small><?= $erreurs['mot_de_passe'] ?? '' ?></small>

            <input type="password" name="confirm_mot_de_passe" placeholder="Confirmez le mot de passe">
            <small><?= $erreurs['confirm_mot_de_passe'] ?? '' ?></small>

            <button type="submit">M'inscrire</button>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
