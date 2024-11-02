document.addEventListener("DOMContentLoaded", function () {
  initWishlistButtons();
  updateWishlistCount();
});

function initWishlistButtons() {
  const wishlistButtons = document.querySelectorAll(".wishlist-btn");

  wishlistButtons.forEach((button) => {
    const input = button.querySelector(".wishlist-input");
    const productId = input.dataset.productId;

    // Vérifier l'état initial
    checkWishlistStatus(productId, input);

    // Ajouter l'écouteur d'événement
    input.addEventListener("change", function () {
      handleWishlistToggle(this, productId);
    });
  });
}

function handleWishlistToggle(checkbox, productId) {
  const action = checkbox.checked ? "add" : "remove";
  const productCard = checkbox.closest(".product-card");

  fetch("/shopping-website/ajax/wishlist_handler.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      action: action,
      id_produit: productId,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showWishlistToast(data.message, "success");
        updateWishlistCount();

        // Si on est sur la page wishlist et qu'on retire un produit
        if (
          action === "remove" &&
          window.location.pathname.includes("wishlist.php")
        ) {
          // Animation de suppression
          productCard.style.transition = "all 0.5s ease";
          productCard.style.opacity = "0";
          productCard.style.transform = "translateX(100px)";

          setTimeout(() => {
            productCard.remove();

            // Vérifier si la liste est vide
            const remainingProducts =
              document.querySelectorAll(".product-card");
            if (remainingProducts.length === 0) {
              const mainContainer = document.querySelector("main .container");
              mainContainer.innerHTML = `
                            <div class="text-center p-6 min-h-[50vh] flex flex-col justify-center items-center">
                                <img src="../assets/images/icons/blueHeart.png" alt="Coeur bleu" class="w-24 h-24 mb-4 mx-auto">
                                <h2 class="text-2xl font-bold mb-4 text-blue-400">Liste de favoris vide !</h2>
                                <p class="text-gray-700 mb-6">Votre liste de favoris est actuellement vide.</p>
                                <div class="flex flex-col items-center space-y-4">
                                    <a href="produit.php" class="btn btn-small">Découvrir nos produits</a>
                                </div>
                            </div>
                        `;
            }
          }, 500);
        }
      } else {
        checkbox.checked = !checkbox.checked;
        if (data.redirect) {
          window.location.href = data.redirect;
        } else {
          showToast(data.message, "error");
        }
      }
    })
    .catch((error) => {
      console.error("Erreur:", error);
      checkbox.checked = !checkbox.checked;
      showToast("Une erreur est survenue", "error");
    });
}

function checkWishlistStatus(productId, checkbox) {
  fetch("/shopping-website/ajax/wishlist_handler.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      action: "check",
      id_produit: productId,
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        checkbox.checked = data.inWishlist;
      }
    })
    .catch((error) => console.error("Erreur:", error));
}

function updateWishlistCount() {
  fetch("/shopping-website/ajax/wishlist_handler.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      action: "count",
    }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Mettre à jour le compteur dans le header
        const wishlistCounter = document.querySelector(".wishlist-counter");
        if (wishlistCounter) {
          wishlistCounter.textContent = data.count;
          wishlistCounter.style.display = data.count > 0 ? "block" : "none";
        }

        // Mettre à jour le compteur sur la page wishlist
        const wishlistCount = document.getElementById("wishlistCount");
        if (wishlistCount) {
          wishlistCount.textContent = `${data.count} article${
            data.count > 1 ? "s" : ""
          }`;
        }
      }
    })
    .catch((error) => console.error("Erreur:", error));
}

function clearAllWishlists() {
  Swal.fire({
    title: "Êtes-vous sûr ?",
    text: "Voulez-vous vider votre liste de favoris ?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Oui, tout supprimer",
    cancelButtonText: "Annuler",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("/shopping-website/ajax/wishlist_handler.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          action: "clear_all",
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            showToast("Liste de souhaits vidée avec succès", "success");
            updateWishlistCount();
            // Animation de suppression
            const wishlistContainer = document.querySelector(".grid");
            if (wishlistContainer) {
              wishlistContainer.style.transition = "all 0.5s ease";
              wishlistContainer.style.opacity = "0";
              wishlistContainer.style.transform = "translateY(20px)";

              setTimeout(() => {
                location.reload();
              }, 500);
            }
          } else {
            showToast(data.message || "Erreur lors de la suppression", "error");
          }
        })
        .catch((error) => {
          console.error("Erreur:", error);
          showToast("Une erreur est survenue", "error");
        });
    }
  });
}
