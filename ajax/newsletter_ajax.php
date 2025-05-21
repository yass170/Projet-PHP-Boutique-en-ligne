<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/mailer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Adresse email invalide.']);
    exit;
}
if($email != $_SESSION['utilisateur']['email']){
    echo json_encode(['success' => false,'message'=>'Ce n\'est pas votre email.']);
    exit;
}

$sujet = "Confirmation abonnement a la newsletter " . SITE_NAME;
$corps = "Bonjour,\n\nVous êtes maintenant inscrit(e) à la newsletter de " . SITE_NAME . ".\n\nMerci de votre intérêt !";

if (envoyerMail($email, $sujet, $corps)) {
    echo json_encode(['success' => true, 'message' => 'Merci pour votre inscription !']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de lenvoi de lemail.']);
}
