<?php
require_once __DIR__ . '/../includes/header.php';

$page = htmlspecialchars($_GET['page'] ?? 'Page');
?>

<section class="page-construction">
    <div class="container">
        <h1>Page hors projet</h1>
        <p>La page « <?= ucfirst($page) ?> » n'est pas incluse dans ce projet.</p>
        <a href="javascript:history.back()" class="btn">Revenir en arrière</a>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
