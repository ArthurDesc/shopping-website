<?php
ob_start(); // Démarre la mise en mémoire tampon de sortie
require_once '../includes/_db.php';
require_once '../includes/_header.php';


// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Démarrer la session si elle n'est pas déjà active
}

if (isset($_POST['ajouter_au_panier']) && isset($_POST['id_produit'])) {
    $id_produit = $_POST['id_produit'];
    $panier->ajouter($id_produit);
    // Redirigez vers la même page pour éviter les soumissions multiples
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
$search = isset($_GET['q']) ? $_GET['q'] : '';
$stmt = $conn->prepare("SELECT * FROM produits WHERE nom LIKE ? OR description LIKE ?");
$searchTerm = '%' . $search . '%';
$stmt->bind_param("ss", $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

// Définir le chemin de base pour les images des produits
$image_base_path = '../assets/images/produits/';

// Ajouter une variable pour le nombre de résultats
$nombre_de_resultats = $result->num_rows; // Compte le nombre de résultats
?>

<div class="container mx-auto px-4">
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center">
        <h2 class="text-xl font-semibold text-center sm:text-left">Résultats de recherche pour "<?php echo htmlspecialchars($search); ?>"</h2>
        <form method="get" action="" class="flex items-center mt-4 sm:mt-0">
            <div class="wave-group">
                <input required type="text" name="q" class="input" value="<?php echo htmlspecialchars($search); ?>">
                <span class="bar"></span>
                <label class="label">
                    <span class="label-char" style="--index: 0">R</span>
                    <span class="label-char" style="--index: 1">e</span>
                    <span class="label-char" style="--index: 2">c</span>
                    <span class="label-char" style="--index: 3">h</span>
                    <span class="label-char" style="--index: 4">e</span>
                    <span class="label-char" style="--index: 5">r</span>
                    <span class="label-char" style="--index: 6">c</span>
                    <span class="label-char" style="--index: 7">h</span>
                    <span class="label-char" style="--index: 8">e</span>
                    <span class="label-char" style="--index: 9">r</span>
                </label>
                <button type="submit" class="search-button">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="search-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
    <!-- Champ de recherche avec autocomplétion -->
    

    <section class="products_list">
        <?php 
        if ($result->num_rows > 0) {
            echo '<div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8 mt-6">';
            while ($row = $result->fetch_assoc()) { 
                $image_url = $image_base_path . ($row['image_url'] ?? 'default_product.jpg');
                
                if (!file_exists($image_url) || empty($row['image_url'])) {
                    $image_url = $image_base_path . 'default_product.jpg';
                }
        ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden product-card flex flex-col h-full">
                <a href="<?php echo BASE_URL; ?>pages/detail.php?id=<?php echo $row['id_produit']; ?>" class="block flex-grow flex flex-col">
                    <div class="relative pb-[90%] flex-grow">
                        <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($row['nom']); ?>" class="absolute inset-0 w-full h-full object-cover object-center">
                    </div>
                    <div class="p-3 flex-shrink-0">
                        <h3 class="text-sm font-semibold mb-1 truncate"><?php echo htmlspecialchars($row['nom']); ?></h3>
                        <p class="text-xs text-gray-600 mb-1"><?php echo htmlspecialchars($row['marque']); ?></p>
                    </div>
                </a>
                <div class="product-price-cart-container px-3 pb-3 mt-auto flex justify-between items-center">
                    <p class="product-price text-sm text-blue-600 font-bold"><?php echo number_format($row['prix'], 2); ?> €</p>
                    <button type="button" class="product-cart-button open-modal-btn flex items-center justify-center" data-product-id="<?php echo $row['id_produit']; ?>" data-product-price="<?php echo $row['prix']; ?>" onclick="ouvrirModalTaille(<?php echo $row['id_produit']; ?>, <?php echo $row['prix']; ?>)">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="product-cart-icon">
                            <path d="M7.2 9.8C7.08 9.23 7.55 8.69 8.14 8.69H8.84V10.69C8.84 11.24 9.29 11.69 9.84 11.69C10.39 11.69 10.84 11.24 10.84 10.69V8.69H16.84V10.69C16.84 11.24 17.29 11.69 17.84 11.69C18.39 11.69 18.84 11.24 18.84 10.69V8.69H19.54C20.13 8.69 20.55 9.06 20.62 9.55L21.76 17.55C21.85 18.15 21.38 18.69 20.77 18.69H7.07C6.46 18.69 5.99 18.15 6.08 17.55L7.2 9.8ZM10.84 5.69C10.84 4.04 12.2 2.69 13.84 2.69C15.49 2.69 16.84 3.69 16.84 5.69V6.69H10.84V5.69ZM23.82 18.41L22.39 8.41C22.25 7.43 21.41 6.69 20.41 6.69H18.84V5.69C18.84 2.69 16.6 0.69 13.84 0.69C11.08 0.69 8.84 2.93 8.84 5.69V6.69H7.57C6.58 6.69 5.43 7.43 5.29 8.41L3.86 18.41C3.69 19.62 4.62 20.69 5.84 20.69H21.84C23.06 20.69 23.99 19.62 23.82 18.41Z" fill="currentColor"/>
                        </svg>
                    </button>
                </div>
            </div>
        <?php 
            }
            echo '</div>';
        } else {
            echo "<p class='mt-4'>Aucun produit trouvé pour cette recherche.</p>";
        }
        ?>
    </section>
</div>


<?php require_once '../includes/_footer.php'; ?>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/scripts.js" defer></script>
<script src="<?php echo BASE_URL; ?>assets/js/navbar.js" defer></script>

<!-- Ajoutez ce modal à la fin de votre fichier, juste avant la fermeture de la balise body -->
<div id="modal-taille" class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4 sm:p-0 hidden">
    <div class="bg-white w-full max-w-md m-auto flex-col flex rounded-lg shadow-lg">
        <div class="p-6">
            <h2 class="text-xl font-semibold mb-4">Choisissez une taille</h2>
            <div id="sizeError" class="text-red-500 text-sm mb-2 hidden"></div>
            <select id="productSize" class="w-full px-3 py-2 border rounded-md mb-4">
                <!-- Les options seront ajoutées dynamiquement -->
            </select>
            <div class="flex flex-col-reverse sm:flex-row sm:space-x-4">
                <button id="cancelBtn" onclick="fermerModalTaille()" class="button-shadow w-full sm:flex-1 px-4 py-2 bg-gray-200 text-gray-700 text-base font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300 mt-2 sm:mt-0">
                    Annuler
                </button>
                <div class="cart-add-button button-shadow" id="addToCartBtn" data-tooltip="">
                    <div class="cart-add-button-wrapper">
                        <div class="cart-add-button-text">Ajouter au panier</div>
                        <span class="cart-add-button-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart2" viewBox="0 0 16 16">
                                <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"></path>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let produitActuel = null;
let prixActuel = null;

function ouvrirModalTaille(idProduit, prix) {
    produitActuel = idProduit;
    prixActuel = prix;
    document.getElementById('modal-taille').classList.remove('hidden');
    // Chargement des tailles disponibles
    const select = document.getElementById('productSize');
    select.innerHTML = '<option value="">Choisissez une taille</option>';
    ['S', 'M', 'L', 'XL'].forEach(taille => {
        select.innerHTML += `<option value="${taille}">${taille}</option>`;
    });
    // Mettre à jour le prix dans le tooltip
    document.getElementById('addToCartBtn').setAttribute('data-tooltip', `${prixActuel} €`);
}

function fermerModalTaille() {
    document.getElementById('modal-taille').classList.add('hidden');
    produitActuel = null;
    prixActuel = null;
}

// Ajoutez cet écouteur d'événements pour le bouton "Ajouter au panier" dans le modal
document.getElementById('addToCartBtn').addEventListener('click', ajouterAuPanier);

function ajouterAuPanier() {
    const taille = document.getElementById('productSize').value;
    if (!taille) {
        document.getElementById('sizeError').textContent = 'Veuillez sélectionner une taille';
        document.getElementById('sizeError').classList.remove('hidden');
        return;
    }
    
    // Utilisation de FormData pour envoyer les données
    const formData = new FormData();
    formData.append('ajouter_au_panier', '1');
    formData.append('id_produit', produitActuel);
    formData.append('taille', taille);

    // Envoyer une requête AJAX pour ajouter le produit au panier
    fetch('<?php echo url("ajax/add_to_cart.php"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fermer le modal
            fermerModalTaille();
            // Mettre à jour le compteur du panier
            updateCartCount(data.cartCount);
        } else {
            console.error('Erreur : ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

function updateCartCount(count) {
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
    }
}

// Réinitialiser le message d'erreur lorsqu'une taille est sélectionnée
document.getElementById('productSize').addEventListener('change', function() {
    document.getElementById('sizeError').classList.add('hidden');
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentProductId = null;
    let currentProductPrice = null;

    // Ouvrir le modal
    document.querySelectorAll('.open-modal-btn').forEach(button => {
        button.addEventListener('click', function() {
            currentProductId = this.dataset.productId;
            currentProductPrice = this.dataset.productPrice;
            
            // Charger les tailles disponibles pour ce produit
            document.getElementById('productSize').innerHTML = `
                <option value="">Choisissez une taille</option>
                <option value="S">S</option>
                <option value="M">M</option>
                <option value="L">L</option>
                <option value="XL">XL</option>
            `;
            
            // Mettre à jour le prix dans le tooltip
            document.getElementById('addToCartBtn').setAttribute('data-tooltip', `${currentProductPrice} €`);
            
            document.getElementById('modal-container').classList.remove('hidden');
        });
    });

    // Fermer le modal
    document.getElementById('modal-container').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Ajouter cette fonction pour le bouton Annuler
    document.getElementById('cancelBtn').addEventListener('click', function() {
        closeModal();
    });

    function closeModal() {
        document.getElementById('modal-container').classList.add('hidden');
        // Réinitialiser la sélection de taille et le message d'erreur
        document.getElementById('productSize').value = '';
        document.getElementById('sizeError').classList.add('hidden');
    }

    // Ajouter au panier
    document.getElementById('addToCartBtn').addEventListener('click', function() {
        const selectedSize = document.getElementById('productSize').value;
        if (!selectedSize) {
            // Afficher le message d'erreur dans le modal
            document.getElementById('sizeError').textContent = 'Veuillez choisir une taille';
            document.getElementById('sizeError').classList.remove('hidden');
            return;
        }

        // Cacher le message d'erreur si une taille est sélectionnée
        document.getElementById('sizeError').classList.add('hidden');

        // Envoyer la requête AJAX pour ajouter au panier
        fetch('<?php echo url("ajax/add_to_cart.php"); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                id_produit: currentProductId,
                taille: selectedSize,
                quantite: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                // Mettre à jour le compteur du panier si nécessaire
                updateCartCount(data.cartCount);
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur s\'est produite lors de l\'ajout au panier.');
        });
    });

    // Réinitialiser le message d'erreur lorsqu'une taille est sélectionnée
    document.getElementById('productSize').addEventListener('change', function() {
        document.getElementById('sizeError').classList.add('hidden');
    });

    function updateCartCount(count) {
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = count;
        }
    }
});
</script>

</body>
</html>
