document.addEventListener('DOMContentLoaded', function() {
    const avisForm = document.getElementById('avis-form');
    const avisList = document.getElementById('avis-list');

    // Charger les avis existants
    loadAvis();

    // Gestion de l'ajout d'avis
    avisForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const note = document.getElementById('note-input').value;
        const commentaire = document.getElementById('commentaire').value;
        addAvis(note, commentaire);
    });

    // Fonction pour charger les avis
    function loadAvis() {
        fetch(`${BASE_URL}ajax/avis_handler.php?action=get&id_produit=${ID_PRODUIT}`)
            .then(response => response.json())
            .then(avis => {
                avisList.innerHTML = '';
                avis.forEach(avis => {
                    avisList.appendChild(createAvisElement(avis));
                });
            });
    }

    // Fonction pour ajouter un avis
    function addAvis(note, commentaire) {
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('id_produit', ID_PRODUIT);
        formData.append('note', note);
        formData.append('commentaire', commentaire);

        fetch(`${BASE_URL}ajax/avis_handler.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(newAvis => {
            avisList.prepend(createAvisElement(newAvis));
            avisForm.reset();
        });
    }

    // Fonction pour créer un élément d'avis
    function createAvisElement(avis) {
        const div = document.createElement('div');
        div.className = 'avis';
        div.innerHTML = `
            <p>${avis.note}</p>
            <p>${avis.commentaire}</p>
            <button onclick="editAvis(${avis.id})">Modifier</button>
            <button onclick="deleteAvis(${avis.id})">Supprimer</button>
        `;
        return div;
    }

    // Fonction pour modifier un avis
    window.editAvis = function(avisId) {
        const newNote = prompt("Modifier votre note :");
        const newCommentaire = prompt("Modifier votre commentaire :");
        if (newNote && newCommentaire) {
            fetch(`${BASE_URL}ajax/avis_handler.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=edit&id_produit=${ID_PRODUIT}&id_avis=${avisId}&note=${encodeURIComponent(newNote)}&commentaire=${encodeURIComponent(newCommentaire)}`
            })
            .then(response => response.json())
            .then(() => loadAvis());
        }
    }

    // Fonction pour supprimer un avis
    window.deleteAvis = function(avisId) {
        if (confirm("Êtes-vous sûr de vouloir supprimer cet avis ?")) {
            fetch(`${BASE_URL}ajax/avis_handler.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete&id_produit=${ID_PRODUIT}&id_avis=${avisId}`
            })
            .then(response => response.json())
            .then(() => loadAvis());
        }
    }
});
