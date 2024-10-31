<!-- Modal de confirmation de déconnexion -->
<div id="modal-deconnexion" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4 sm:p-0 hidden">
    <div class="bg-white w-full max-w-md m-auto flex-col flex rounded-lg shadow-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Confirmation de déconnexion</h2>
            <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir vous déconnecter ?</p>
            <div class="flex flex-col-reverse sm:flex-row sm:space-x-4">
                <button id="cancel-deconnexion" class="button-shadow w-full sm:flex-1 px-4 py-2 bg-gray-200 text-gray-700 text-base font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 mt-2 sm:mt-0">
                    Annuler
                </button>
                <a href="<?php echo url('pages/deconnexion.php'); ?>" class="button-shadow w-full sm:flex-1 px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-center">
                    Se déconnecter
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalDeconnexion = document.getElementById('modal-deconnexion');
    const btnsDeconnexion = document.querySelectorAll('[id="btn-deconnexion"], [id="btn-deconnexion-profil"]');
    const btnCancelDeconnexion = document.getElementById('cancel-deconnexion');

    if (modalDeconnexion && btnCancelDeconnexion) {
        btnsDeconnexion.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                modalDeconnexion.classList.remove('hidden');
            });
        });

        btnCancelDeconnexion.addEventListener('click', function() {
            modalDeconnexion.classList.add('hidden');
        });

        modalDeconnexion.addEventListener('click', function(e) {
            if (e.target === modalDeconnexion) {
                modalDeconnexion.classList.add('hidden');
            }
        });
    }
});
</script> 