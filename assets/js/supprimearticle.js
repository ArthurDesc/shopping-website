document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('a[href^="panier.php?del="]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.href.split('=')[1];
            const productElement = this.closest('.flex.items-center');
            
            Swal.fire({
                title: 'Supprimer l\'article ?',
                text: "Voulez-vous retirer cet article du panier ?",
                icon: 'warning',
                iconColor: '#EF4444',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                reverseButtons: true,
                customClass: {
                    container: 'font-sans',
                    popup: 'rounded-xl shadow-xl border-0',
                    title: 'text-xl font-medium text-gray-800',
                    htmlContainer: 'text-gray-600',
                    confirmButton: 'bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 shadow-lg hover:shadow-md',
                    cancelButton: 'bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors duration-200 shadow-md hover:shadow-sm',
                    actions: 'gap-3',
                    icon: 'border-red-500'
                },
                buttonsStyling: false,
                showClass: {
                    popup: 'animate__animated animate__fadeIn animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOut animate__faster'
                },
                background: '#ffffff'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`panier.php?del=${productId}&ajax=1`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            productElement.style.transition = 'all 0.5s ease';
                            productElement.style.opacity = '0';
                            productElement.style.transform = 'translateX(100px)';
                            
                            setTimeout(() => {
                                productElement.remove();
                                
                                const remainingProducts = document.querySelectorAll('.flex.items-center');
                                if (remainingProducts.length === 0) {
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

                                if (data.newTotal !== undefined) {
                                    updateTotalPrice(data.newTotal);
                                }

                                Swal.fire({
                                    title: 'Article supprimé !',
                                    text: 'L\'article a été retiré de votre panier',
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    customClass: {
                                        container: 'font-sans',
                                        popup: 'rounded-xl shadow-xl border-0',
                                        title: 'text-xl font-medium text-gray-800',
                                        htmlContainer: 'text-gray-600',
                                        icon: 'border-green-500'
                                    },
                                    showClass: {
                                        popup: 'animate__animated animate__fadeIn animate__faster'
                                    },
                                    hideClass: {
                                        popup: 'animate__animated animate__fadeOut animate__faster'
                                    },
                                    background: '#ffffff'
                                });
                            }, 500);
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors de la suppression',
                            icon: 'error',
                            iconColor: '#EF4444',
                            showCancelButton: true,
                            confirmButtonText: 'OK',
                            customClass: {
                                container: 'font-sans',
                                popup: 'rounded-xl shadow-xl border-0',
                                title: 'text-xl font-medium text-gray-800',
                                htmlContainer: 'text-gray-600',
                                confirmButton: 'bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 shadow-lg hover:shadow-md',
                                icon: 'border-red-500'
                            },
                            buttonsStyling: false,
                            showClass: {
                                popup: 'animate__animated animate__fadeIn animate__faster'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOut animate__faster'
                            },
                            background: '#ffffff'
                        });
                    });
                }
            });
        });
    });
}); 