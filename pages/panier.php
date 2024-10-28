<?php
ob_start(); // Démarre la mise en mémoire tampon de sortie
include_once "../includes/_db.php";
require_once "../includes/session.php";
require_once "../classe/Panier.php"; // Gardez cette ligne

$panier = new Panier();

// Traitement de la mise à jour de la quantité (à supprimer ou commenter)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produit']) && isset($_POST['action'])) {
    // Code à supprimer ou commenter
    // ...
}

// Ajout d'un script AJAX pour gérer les mises à jour du panier
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[action="panier.php"]'); // Sélectionner tous les formulaires de mise à jour

    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Empêcher le rafraîchissement de la page

            const formData = new FormData(form); // Récupérer les données du formulaire

            fetch('panier.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.text())
            .then(data => {
                console.log(data); // Pour le débogage
                location.reload(); // Optionnel : recharger la page pour mettre à jour l'affichage
            })
            .catch(error => console.error('Erreur:', error));
        });
    });

    // Gestion de la quantité en JavaScript
    const quantityForms = document.querySelectorAll('form[action="panier.php"]');
    quantityForms.forEach(form => {
        const decreaseButton = form.querySelector('button[name="action"][value="decrease"]');
        const increaseButton = form.querySelector('button[name="action"][value="increase"]');
        const quantityDisplay = form.querySelector('span');

        decreaseButton.addEventListener('click', function() {
            let quantity = parseInt(quantityDisplay.textContent);
            if (quantity > 1) {
                quantityDisplay.textContent = quantity - 1;
                updateQuantity(form, quantity - 1);
            }
        });

        increaseButton.addEventListener('click', function() {
            let quantity = parseInt(quantityDisplay.textContent);
            quantityDisplay.textContent = quantity + 1;
            updateQuantity(form, quantity + 1);
        });
    });

    function updateQuantity(form, quantity) {
        const idProduitInput = form.querySelector('input[name="id_produit"]');
        const formData = new FormData();
        formData.append('id_produit', idProduitInput.value);
        formData.append('quantite', quantity);
        formData.append('update', true); // Indiquer que c'est une mise à jour

        fetch('panier.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.text())
        .then(data => {
            console.log('Response from server:', data); // Pour le débogage
            location.reload(); // Optionnel : recharger la page pour mettre à jour l'affichage
        })
        .catch(error => console.error('Erreur:', error));
    }
});
</script>
<?php

// Traitement de la mise à jour de la quantité
if (isset($_POST['update'])) {
    $id_update = $_POST['id_produit'];
    $quantity = $_POST['quantite'];

    // Vérifier si la quantité est valide
    if (is_numeric($quantity) && $quantity > 0) {
        // Récupérer le produit pour vérifier le stock
        $stmt = $conn->prepare("SELECT stock FROM produits WHERE id_produit = ?");
        $stmt->bind_param("i", $id_update);
        $stmt->execute();
        $result = $stmt->get_result();
        $produit = $result->fetch_assoc();

        // Vérifier si la quantité demandée ne dépasse pas le stock
        if ($quantity <= $produit['stock']) {
            $panier->mettreAJourQuantite($id_update, intval($quantity));
            echo "Quantité mise à jour avec succès."; // Message de succès
        } else {
            echo "La quantité demandée dépasse le stock disponible."; // Message d'erreur
            $panier->retirerProduit($id_update); // Retirer le produit si la quantité n'est pas valide
        }
    } else {
        $panier->retirerProduit($id_update); // Retirer le produit si la quantité n'est pas valide
        echo "Produit retiré du panier en raison d'une quantité invalide."; // Message d'erreur
    }
}

// Supprimer les produits
if (isset($_GET['del'])) {
    $id_del = $_GET['del'];
    $panier->retirerProduit($id_del);
}

// Inclusion du header
include '../includes/_header.php';
?>



<main class="flex-grow container mx-auto px-4 py-8 mt-16"> 
    <?php 
    $total = 0;
    $contenuPanier = $panier->getContenu();
    ?>
    <div class="flex flex-col lg:flex-row lg:space-x-8">
        <!-- Liste des produits -->
        <div class="w-full <?= !empty($contenuPanier) ? 'lg:w-2/3' : '' ?> mb-8 lg:mb-0">
            <?php
            if (empty($contenuPanier)) {
                echo '<div class="text-center p-6">';
                echo '<h2 class="text-2xl font-bold mb-4 text-blue-400">Panier vide !</h2>';
                echo '<img src="../assets/images/panier.png" alt="Panier vide" class="w-32 h-32 mx-auto mb-6">';
                echo '<p class="text-gray-700 mb-6">Votre panier est actuellement vide.</p>';
                echo '<div class="flex flex-col items-center space-y-4">';
                echo '<a href="produit.php" class="btn btn-small">Continuer vos achats</a>'; // Ajout de la classe btn-small
                if (!isset($_SESSION['id_utilisateur'])) {
                    echo '<a href="auth.php" class="text-blue-600 underline text-sm px-6 py-3 rounded-full hover:no-underline">Connectez-vous pour récupérer votre panier</a>';
                }
                echo '</div>';
                echo '</div>';
            } else {
                $ids = array_map(function ($key) {
                    return explode('_', $key)[0];
                }, array_keys($contenuPanier));
                $ids = array_unique($ids);

                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $sql = "SELECT * FROM produits WHERE id_produit IN ($placeholders)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($product = $result->fetch_assoc()) {
                    foreach ($contenuPanier as $key => $quantity) {
                        list($id, $taille) = explode('_', $key . '_');
                        if ($id == $product['id_produit']) {
                            $product_total = $product['prix'] * intval($quantity);
                            $total += $product_total;

                            $img = $product['image_url'] ?? '';
                            $nom = htmlspecialchars($product['nom'] ?? '', ENT_QUOTES, 'UTF-8');

                            if (is_array($img)) {
                                $img = htmlspecialchars($img[0] ?? 'default-image.png', ENT_QUOTES, 'UTF-8');
                            } else {
                                $img = htmlspecialchars($img ?? 'default-image.png', ENT_QUOTES, 'UTF-8');
                            }
            ?>
                            <div class="flex items-center border-b border-gray-200 py-4">
                                <img src="../assets/images/produits/<?= $img ?>" alt="<?= $nom ?>" class="w-24 h-24 object-cover rounded mr-4">
                                <div class="flex-grow">
                                    <h3 class="font-semibold"><?= $nom ?> <?= $taille ? "(Taille: $taille)" : '' ?></h3>
                                    <p class="text-gray-600"><?= number_format($product['prix'], 2); ?>€</p>
                                    <form method="post" action="panier.php" class="flex items-center mt-2">
                                        <input type="hidden" name="id_produit" value="<?= $key ?>">
                                        <button type="submit" name="action" value="decrease" class="bg-gray-200 text-gray-600 px-2 py-1 rounded-l">-</button>
                                        <span class="px-4 py-1 bg-gray-100"><?= $quantity ?></span>
                                        <button type="submit" name="action" value="increase" class="bg-gray-200 text-gray-600 px-2 py-1 rounded-r">+</button>
                                    </form>
                                </div>
                                <a href="panier.php?del=<?= urlencode($key); ?>" class="text-red-500 hover:text-red-700 ml-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </a>
                            </div>
            <?php
                        }
                    }
                }
            }
            ?>
        </div>

        <?php if (!empty($contenuPanier)): ?>
        <!-- Résumé du panier -->
        <div class="w-full lg:w-1/3">
            <div class="bg-gray-50 rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">Résumé du Panier</h2>
                <div class="mb-4">
                    <p class="text-lg">Total à payer :</p>
                    <p class="text-2xl font-bold text-green-600"><?= number_format($total, 2); ?>€</p>
                </div>
                <div class="flex flex-col space-y-2">
                    <?php if(isset($_SESSION['id_utilisateur'])): ?>
                        <a href="process_paiement.php" class="button button-green">
                            Procéder au paiement
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                            </svg>
                        </a>
                    <?php else: ?>
                        <a href="auth.php" class="button button-green">
                            Se connecter pour payer
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                        </a>
                    <?php endif; ?>
                    <a href="produit.php" class="button">
                        Continuer vos achats
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
    
    <script src="../assets/js/scripts.js" defer></script>
    <script src="../assets/js/navbar.js" defer></script>
    <script src="https://js.stripe.com/v3/"></script>
</main>

<form id="payment-form">
    <div id="card-element">
        <!-- Stripe Elements will create input elements here -->
    </div>
    <button id="submit">Payer</button>
    <div id="payment-result"></div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const showPaymentFormButton = document.getElementById('show-payment-form');
    const paymentForm = document.getElementById('payment-form');

    showPaymentFormButton.addEventListener('click', function() {
        paymentForm.style.display = 'block'; // Afficher le formulaire
        showPaymentFormButton.style.display = 'none'; // Masquer le bouton
    });

    const stripe = Stripe('pk_test_51Q7Hl1P5XJmDt2UGKTXg2A7p3bt8nsP1POLDv881WalxO2rQzdN7CxuflpPdoft3pCcEMnlLxLfTOxeh58sHpLbN00ITmhtq3O');
    const elements = stripe.elements();
    const cardElement = elements.create('card');
    cardElement.mount('#card-element');

    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        console.log('Form submitted');

        const { paymentMethod, error } = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
        });

        if (error) {
            console.error('Error creating payment method:', error);
            document.getElementById('payment-result').innerText = error.message;
        } else {
            console.log('Payment method created:', paymentMethod);

            const formData = new FormData();
            formData.append('paymentMethodId', paymentMethod.id);

            const response = await fetch('process_paiement.php', {
                method: 'POST',
                body: formData,
            });

            const result = await response.json();
            console.log('Server response:', result);
            document.getElementById('payment-result').innerText = result.message;
        }
    });
});
</script>

<!-- Ajout d'un espace supplémentaire avant le footer -->
<div class="mt-20"></div>

<?php include '../includes/_footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.learn-more');
    buttons.forEach(button => {
        const textWidth = button.querySelector('.button-text').offsetWidth;
        button.style.width = `${textWidth + 60}px`; // 60px pour le cercle et un peu d'espace
    });
});
</script>

<style>
    .glow-button {
        position: relative;
        overflow: hidden;
    }
    .glow-button::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 80%);
        transform: translate(
            calc(var(--mouse-x, 0) - 50%),
            calc(var(--mouse-y, 0) - 50%)
        );
    }
</style>

<style>
    #payment-form {
        display: none; /* Masquer le formulaire par défaut */
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-button'); // Assurez-vous que vos boutons ont cette classe
    const cartLogo = document.querySelector('.cart-logo'); // Assurez-vous que c'est la bonne classe ou ID

    // Vérifiez si le logo du panier existe
    if (cartLogo) {
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Change la couleur du logo du panier
                cartLogo.style.color = 'green'; // Changez la couleur ici
                cartLogo.style.backgroundColor = 'lightgreen'; // Optionnel : changez le fond
                cartLogo.textContent = parseInt(cartLogo.textContent) + 1; // Incrémente le nombre d'articles
            });
        });
    }
});
</script>














