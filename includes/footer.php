<?php require_once __DIR__ . '/../templates/alert_modal.php'; ?>
<?php require_once __DIR__ . '/../templates/loader.php'; ?>

</main>

<footer>
    <div class="container footer-container">
        <div class="footer-links">
            <a href="<?= SITE_URL ?>templates/page_en_construction.php?page=apropos">À propos</a>
            <a href="<?= SITE_URL ?>templates/page_en_construction.php?page=contact">Contact</a>
            <a href="<?= SITE_URL ?>templates/page_en_construction.php?page=cgv">Conditions générales</a>
            <a href="<?= SITE_URL ?>templates/page_en_construction.php?page=confidentialite">Politique de confidentialité</a>
        </div>
        <div class="footer-copy">
            &copy; <?= date('Y') ?> <?= SITE_NAME ?>. Tous droits réservés.
        </div>
    </div>
</footer>

</body>
</html>
