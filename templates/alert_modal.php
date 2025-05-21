<div id="alert-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <p id="alert-message">Message de confirmation</p>
        <div class="modal-buttons">
            <button id="alert-confirm">Confirmer</button>
            <button id="alert-cancel">Annuler</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('alert-modal');
    const message = document.getElementById('alert-message');
    const confirmBtn = document.getElementById('alert-confirm');
    const cancelBtn = document.getElementById('alert-cancel');

    window.showAlertModal = function(text, onConfirm) {
        message.textContent = text;
        modal.style.display = 'flex';

        const cleanup = () => {
            modal.style.display = 'none';
            confirmBtn.onclick = null;
            cancelBtn.onclick = null;
        };

        confirmBtn.onclick = () => {
            onConfirm();
            cleanup();
        };

        cancelBtn.onclick = () => {
            cleanup();
        };
    };
});
</script>
