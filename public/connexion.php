<?php
require_once __DIR__ . '/../includes/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';

// Rediriger si déjà connecté
if (isset($_SESSION['utilisateur'])) {
    header('Location: home.php');
    exit;
}

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if ($email && $mot_de_passe) {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            // Connexion réussie
            $_SESSION['utilisateur'] = [
                'id' => $user['id'],
                'nom' => $user['nom'],
                'prenom' => $user['prenom'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            header('Location: home.php');
            exit;
        } else {
            $erreur = 'Email ou mot de passe incorrect.';
        }
    } else {
        $erreur = 'Veuillez remplir tous les champs.';
    }
}
?>

<section class="connexion">
    <div class="container">
        <h1>Connexion</h1>

        <?php if ($erreur): ?>
            <p class="erreur"><?= $erreur ?></p>
        <?php endif; ?>

        <form method="post">
            <input type="email" name="email" placeholder="Adresse e-mail" required>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <button type="submit">Se connecter</button>
        </form>

        <div class="inscription-lien">
            <p>Pas encore de compte ?</p>
            <a href="inscription.php" class="btn">Inscription</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
