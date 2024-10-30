document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('a[href^="panier.php?del="]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.href.split('=')[1];
            const productElement = this.closest('.flex.items-center');
            
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Voulez-vous retirer cet article du panier ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Utilisation de fetch avec XMLHttpRequest header
                    fetch(`panier.php?del=${productId}&ajax=1`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Animation de suppression
                            productElement.style.transition = 'all 0.5s ease';
                            productElement.style.opacity = '0';
                            productElement.style.transform = 'translateX(100px)';
                            
                            setTimeout(() => {
                                productElement.remove();
                                
                                // Vérifier si le panier est vide
                                const remainingProducts = document.querySelectorAll('.flex.items-center');
                                if (remainingProducts.length === 0) {
                                    // Afficher le message de panier vide sans recharger
                                    const mainContainer = document.querySelector('main .container');
                                    mainContainer.innerHTML = `
                                        <div class="text-center p-6">
                                            <h2 class="text-2xl font-bold mb-4 text-blue-400">Panier vide !</h2>
                                            <img src="../assets/images/panier.png" alt="Panier vide" class="w-32 h-32 mx-auto mb-6">
                                            <p class="text-gray-700 mb-6">Votre panier est actuellement vide.</p>
                                            <div class="flex flex-col items-center space-y-4">
                                                <a href="produit.php" class="btn btn-small">Continuer vos achats</a>
                                            </div>
                                        </div>
                                    `;
                                }

                                // Mettre à jour le total
                                if (data.newTotal !== undefined) {
                                    updateTotalPrice(data.newTotal);
                                }
                            }, 500);

                            Swal.fire({
                                icon: 'success',
                                title: 'Article supprimé !',
                                showConfirmButton: false,
                                timer: 1500,
                                position: 'top-end',
                                toast: true
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors de la suppression'
                        });
                    });
                }
            });
        });
    });
}); 