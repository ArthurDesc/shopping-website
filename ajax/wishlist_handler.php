<?php
session_start();
require_once '../includes/_db.php';
require_once '../classe/WishlistManager.php';

// Récupérer les données envoyées en JSON
$data = json_decode(file_get_contents('php://input'), true);
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Utilisateur non connecté',
        'redirect' => '/shopping-website/pages/connexion.php'
    ]);
    exit;
}

$wishlistManager = new WishlistManager($conn);
$action = $data['action'] ?? '';
$id_produit = $data['id_produit'] ?? null;
$id_utilisateur = $_SESSION['id_utilisateur'];

try {
    switch ($action) {
        case 'add':
            $success = $wishlistManager->addToWishlist($id_utilisateur, $id_produit);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Produit ajouté aux favoris' : 'Erreur lors de l\'ajout'
            ]);
            break;
            
        case 'remove':
            $success = $wishlistManager->removeFromWishlist($id_utilisateur, $id_produit);
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Produit retiré des favoris' : 'Erreur lors de la suppression'
            ]);
            break;
            
        case 'check':
            $isInWishlist = $wishlistManager->isInWishlist($id_utilisateur, $id_produit);
            echo json_encode([
                'success' => true,
                'inWishlist' => $isInWishlist
            ]);
            break;

        case 'count':
            $result = $wishlistManager->getWishlist($id_utilisateur);
            $count = $result->num_rows;
            echo json_encode([
                'success' => true,
                'count' => $count
            ]);
            break;
            
        default:
            throw new Exception('Action non reconnue');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 