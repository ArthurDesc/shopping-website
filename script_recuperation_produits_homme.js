// Fonction pour récupérer les produits pour hommes
async function recupererProduitsHomme() {
  try {
    // Simuler une requête à la base de données
    const produits = await simulerRequeteBDD();
    
    // Filtrer les produits pour hommes
    const produitsHomme = produits.filter(produit => 
      produit.collection.toLowerCase().includes('homme') || 
      produit.nom.toLowerCase().includes('homme')
    );
    
    // Afficher les produits pour hommes
    console.log('Produits pour hommes :', produitsHomme);
    
    if (produitsHomme.length === 0) {
      console.log('Aucun produit pour homme trouvé.');
    } else {
      afficherProduits(produitsHomme);
    }
    
  } catch (erreur) {
    console.error('Erreur lors de la récupération des produits :', erreur);
  }
}

// Fonction pour simuler une requête à la base de données
function simulerRequeteBDD() {
  // Ces données proviennent de votre base de données fournie
  return Promise.resolve([
    { id_produit: 31, nom: ' T-shirt Performance', collection: 'Homme', prix: 30.00 },
    { id_produit: 32, nom: 'Pantalon de Yoga', collection: 'Femme', prix: 40.00 },
    { id_produit: 33, nom: 'Short de Course', collection: 'Homme', prix: 25.00 },
    { id_produit: 34, nom: 'Legging de Sport', collection: 'Femme', prix: 35.00 },
    { id_produit: 35, nom: 'Hoodie de Sport', collection: 'Homme', prix: 50.00 },
    // ... autres produits
  ]);
}

// Fonction pour afficher les produits (à implémenter selon vos besoins)
function afficherProduits(produits) {
  // Implémentez cette fonction pour afficher les produits sur votre page web
  produits.forEach(produit => {
    console.log(`${produit.nom} - ${produit.prix}€`);
  });
}

// Assurez-vous que le DOM est chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', () => {
  recupererProduitsHomme();
});
