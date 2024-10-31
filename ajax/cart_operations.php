<?php
session_start();
require_once "../includes/_db.php";
require_once "../classe/Panier.php";

header('Content-Type: application/json');

$panier = new Panier();
$response = ['success' => false, 'message' => ''];

// Au début du fichier après les requires
error_log('POST data: ' . print_r($_POST, true));

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        $id_produit = $_POST['id_produit'] ?? '';
        $taille = $_POST['taille'] ?? null;
        
        switch($action) {
            case 'update':
                $quantite = (int)$_POST['quantite'];
                // Vérifier le stock avant la mise à jour
                $stmt = $conn->prepare("SELECT stock FROM produits WHERE id_produit = ?");
                $stmt->bind_param("i", $id_produit);
                $stmt->execute();
                $result = $stmt->get_result();
                $produit = $result->fetch_assoc();
                
                if ($produit && $quantite <= $produit['stock']) {
                    // Utiliser directement mettreAJourQuantite au lieu de manipuler la session
                    $panier->mettreAJourQuantite($id_produit, $quantite, $taille);
                    
                    // Récupérer les informations mises à jour
                    $cartInfo = $panier->getCartInfo();
                    $response = array_merge(
                        ['success' => true],
                        $cartInfo,
                    );
                } else {
                    throw new Exception("Stock insuffisant");
                }
                break;
                
            case 'remove':
                $panier->retirerProduit($id_produit, $taille);
                $cartInfo = $panier->getCartInfo();
                $response = array_merge(
                    ['success' => true],
                    $cartInfo,
                    ['message' => 'Article supprimé']
                );
                break;
                
            default:
                throw new Exception("Action non reconnue");
        }
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

// Avant d'envoyer la réponse
error_log('Response: ' . json_encode($response));

echo json_encode($response);