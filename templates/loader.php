<div class="loader" style="display: flex; align-items: center; justify-content: center; position: fixed; inset: 0; background: rgba(255,255,255,0.9); z-index: 9999;">
    <div class="loader-circle" style="width: 50px; height: 50px; border: 5px solid #ccc; border-top-color: #333; border-radius: 50%; animation: spin 1s linear infinite;"></div>
</div>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
.loader.fade-out {
    opacity: 0;
    transition: opacity 0.5s ease;
    pointer-events: none;
}
</style>
