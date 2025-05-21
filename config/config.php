<?php
// Paramètres de connexion à la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_simulation');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}

// Fonction pour charger les paramètres dynamiques
function getParam($cle) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT valeur FROM parametres_site WHERE cle = :cle");
    $stmt->execute(['cle' => $cle]);
    $result = $stmt->fetch();
    return $result ? $result['valeur'] : null;
}

// Paramètres globaux du site
define('SITE_NAME', getParam('site_name'));
define('SITE_URL', getParam('site_url'));
define('ADMIN_EMAIL', getParam('email_admin'));

// SMTP pour PHPMailer
define('SMTP_HOST', getParam('smtp_host'));
define('SMTP_PORT', getParam('smtp_port'));
define('SMTP_USER', getParam('smtp_user'));
define('SMTP_PASS', getParam('smtp_pass'));
define('SMTP_SECURE', getParam('smtp_secure'));
define('SMTP_APP_NAME', getParam('smtp_app_name'));

// Fuseau horaire par défaut
date_default_timezone_set('Europe/Paris');
