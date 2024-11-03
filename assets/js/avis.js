document.addEventListener("DOMContentLoaded", function () {
  // Initialiser tous les RateYo en lecture seule
  $(".rateyo-readonly").each(function() {
    $(this).rateYo({
      rating: $(this).data("rating"),
      readOnly: true,
      starWidth: "20px"
    });
  });

  const commentForm = document.getElementById("comment-form");
  const commentsList = document.getElementById("comments-list");

  if (!commentForm) {
    console.error("Le formulaire de commentaire n'a pas été trouvé");
    return;
  }

  commentForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    try {
      const formData = new FormData(this);

      // Debug des données envoyées
      console.log("Données envoyées:", Object.fromEntries(formData));

      const response = await fetch("/shopping-website/ajax/add_comment.php", {
        method: "POST",
        body: formData,
      });

      // Debug de la réponse
      console.log("Status:", response.status);
      console.log("Headers:", Object.fromEntries(response.headers));

      if (!response.ok) {
        const text = await response.text();
        console.error("Réponse serveur:", text);
        throw new Error(`Erreur serveur: ${response.status}`);
      }

      const data = await response.json();
      console.log("Données reçues:", data);

      if (data.success) {
        const newComment = createCommentElement(data.comment);
        if (newComment) {
          commentsList.insertBefore(newComment, commentsList.firstChild);
          commentForm.reset();
          // Utilisation du nouveau toast
          showBottomToast("Votre avis a été ajouté avec succès", "success");
        }
      } else {
        showBottomToast(
          data.message || "Erreur lors de l'ajout du commentaire",
          "error"
        );
      }
    } catch (error) {
      console.error("Erreur détaillée:", error);
      showBottomToast(
        "Une erreur s'est produite lors de l'ajout du commentaire",
        "error"
      );
    }
  });

  function createCommentElement(comment) {
    // Vérification que comment existe
    if (!comment) {
      console.error("Données de commentaire manquantes");
      return null;
    }

    const div = document.createElement("div");
    div.className = "mb-6 p-6 border rounded-xl shadow-lg bg-white";
    div.setAttribute("data-avis-id", comment.id_avis); // Ajout de l'attribut data-avis-id

    // Récupérer l'ID de l'utilisateur connecté depuis le formulaire
    const currentUserId = document.querySelector(
      'input[name="id_utilisateur"]'
    ).value;

    // Utilisation de l'opérateur ?? pour fournir des valeurs par défaut
    const nomUtilisateur = comment.nom_utilisateur ?? "Anonyme";
    const note = comment.note ?? 0;
    const commentaire = comment.commentaire ?? "Aucun commentaire";
    const dateCreation = comment.date_creation
      ? formatDate(comment.date_creation)
      : "Date non disponible";

    // Création des boutons d'action si l'utilisateur est l'auteur
    const actionButtons =
      currentUserId == comment.id_utilisateur
        ? `
            <button onclick="modifierAvis(${comment.id_avis})" class="text-blue-600 hover:text-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
            </button>
            <button onclick="supprimerAvis(${comment.id_avis})" class="text-red-600 hover:text-red-800">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
            </button>
        `
        : "";

    div.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center">
                    <span class="font-semibold mr-2">${nomUtilisateur}</span>
                    <div class="rateyo-readonly" data-rating="${comment.note}"></div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">${dateCreation}</span>
                    ${actionButtons}
                </div>
            </div>
            <p class="text-gray-700 mt-2 commentaire-texte">${commentaire}</p>
        `;

    // Réinitialiser RateYo pour le nouveau commentaire
    setTimeout(() => {
      $(div).find(".rateyo-readonly").rateYo({
        rating: comment.note,
        readOnly: true,
        starWidth: "20px"
      });
    }, 0);

    return div;
  }

  function formatDate(dateString) {
    try {
      const date = new Date(dateString);
      if (isNaN(date.getTime())) {
        return "Date non disponible";
      }
      return date.toLocaleDateString("fr-FR", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
    } catch (e) {
      console.error("Erreur de formatage de date:", e);
      return "Date non disponible";
    }
  }

  // Fonction pour modifier un avis
  window.modifierAvis = function (idAvis) {
    const avisElement = document.querySelector(`[data-avis-id="${idAvis}"]`);
    const commentaireElement = avisElement.querySelector("p");
    const currentValue = commentaireElement.textContent.trim();
    const noteActuelle = parseFloat(
      avisElement.querySelector(".rateyo-readonly").getAttribute("data-rating")
    );

    // Création du champ d'édition
    const container = document.createElement("div");
    container.className = "flex flex-col gap-2";

    // Textarea pour le commentaire
    const textarea = document.createElement("textarea");
    textarea.value = currentValue;
    textarea.className = "w-full px-3 py-2 bg-white border rounded-lg";

    // Système de notation avec RateYo
    const ratingDiv = document.createElement("div");
    ratingDiv.id = `rateYo-edit-${idAvis}`;
    
    // Supprimer tout le code de création des étoiles (lignes 193-243)
    // et le remplacer par l'initialisation de RateYo dans un setTimeout
    setTimeout(() => {
        $(`#rateYo-edit-${idAvis}`).rateYo({
            rating: noteActuelle,
            fullStar: true,
            starWidth: "20px",
            onChange: function (rating) {
                $(this).next().val(rating);
            }
        });
    }, 0);

    // Input caché pour stocker la note
    const ratingInput = document.createElement("input");
    ratingInput.type = "hidden";
    ratingInput.name = `rating_${idAvis}`;
    ratingInput.value = noteActuelle;

    // Boutons d'action
    const buttonsDiv = document.createElement("div");
    buttonsDiv.className = "flex justify-end gap-2 mt-2";
    buttonsDiv.innerHTML = `
            <button class="save-btn px-3 py-1.5 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
            </button>
        `;

    container.appendChild(textarea);
    container.appendChild(ratingDiv);
    container.appendChild(buttonsDiv);

    // Cacher le commentaire original et afficher le formulaire
    commentaireElement.style.display = "none";
    commentaireElement.after(container);

    // Gestionnaire de sauvegarde
    buttonsDiv
      .querySelector(".save-btn")
      .addEventListener("click", async () => {
        const newCommentaire = textarea.value.trim();
        const newNote = $(`#rateYo-edit-${idAvis}`).rateYo("rating");

        try {
          const response = await fetch(
            "/shopping-website/ajax/update_avis.php",
            {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({
                id_avis: idAvis,
                commentaire: newCommentaire,
                note: newNote,
              }),
            }
          );

          const data = await response.json();
          if (data.success) {
            commentaireElement.textContent = newCommentaire;
            commentaireElement.style.display = "block";
            container.remove();
            showBottomToast("Avis modifié avec succès", "success");
            // Mettre à jour l'affichage des étoiles
            updateStarDisplay(avisElement, newNote);
          } else {
            throw new Error(data.message);
          }
        } catch (error) {
          showBottomToast(
            error.message || "Erreur lors de la modification",
            "error"
          );
        }
      });
  };

  function updateStarDisplay(avisElement, newNote) {
    $(avisElement.querySelector(".rateyo-readonly"))
        .rateYo("rating", newNote)
        .rateYo("option", "rating", newNote);
  }

  // Fonction pour supprimer un avis
  window.supprimerAvis = function (idAvis) {
    if (confirm("Êtes-vous sûr de vouloir supprimer cet avis ?")) {
      fetch("/shopping-website/ajax/delete_avis.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id_avis: idAvis }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            document.querySelector(`[data-avis-id="${idAvis}"]`).remove();
          } else {
            alert("Erreur lors de la suppression : " + data.message);
          }
        })
        .catch((error) => {
          console.error("Erreur:", error);
          alert("Une erreur est survenue lors de la suppression");
        });
    }
  };

  window.annulerModification = function (idAvis) {
    const commentElement = document.querySelector(`[data-avis-id="${idAvis}"]`);
    if (!commentElement) return;

    const commentaireElement =
      commentElement.querySelector(".commentaire-texte");
    if (commentaireElement) {
      commentaireElement.style.display = "block";
    }

    // Supprimer le formulaire de modification
    const form = commentElement.querySelector("div.mt-4.space-y-4").parentNode;
    if (form) {
      form.remove();
    }
  };

  // Initialiser RateYo pour le formulaire d'ajout d'avis
  $("#rateYo").rateYo({
    rating: 0,
    fullStar: true,
    starWidth: "25px",
    onChange: function (rating) {
      $("#rating-input").val(rating);
    }
  });
});

async function getAvis(productId) {
  try {
    const response = await fetch(
      `/shopping-website/ajax/get_avis.php?id_produit=${productId}`
    );
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const contentType = response.headers.get("content-type");
    if (!contentType || !contentType.includes("application/json")) {
      throw new Error("La réponse n'est pas au format JSON!");
    }
    return await response.json();
  } catch (error) {
    console.error("Erreur lors de la récupération des avis:", error);
    return [];
  }
}

// Modification de la fonction d'affichage
function displayAvis(avis) {
  const avisList = document.getElementById("comments-list");
  if (!avisList) {
    console.error("La liste des commentaires n'a pas été trouvée");
    return;
  }

  avisList.innerHTML = "";
  if (Array.isArray(avis)) {
    avis.forEach((avis) => {
      try {
        const avisElement = createCommentElement(avis);
        if (avisElement) {
          avisList.appendChild(avisElement);
        }
      } catch (error) {
        console.error("Erreur lors de la création d'un élément avis:", error);
      }
    });
  } else {
    console.error("Les avis reçus ne sont pas dans un format valide");
  }
}

function showBottomToast(message, type = "success") {
  // Créer l'élément toast
  const toast = document.createElement("div");
  toast.className = `fixed bottom-5 right-5 p-4 rounded-md text-white ${
    type === "success" ? "bg-green-500" : "bg-red-500"
  } shadow-lg transition-opacity duration-500 ease-in-out opacity-0`;
  toast.style.zIndex = "1000";
  toast.innerHTML = message;

  // Ajouter le toast au body
  document.body.appendChild(toast);

  // Faire apparaître le toast
  requestAnimationFrame(() => {
    toast.style.opacity = "1";
  });

  // Faire disparaître le toast après 3 secondes
  setTimeout(() => {
    toast.style.opacity = "0";
    setTimeout(() => {
      document.body.removeChild(toast);
    }, 500);
  }, 3000);
}
