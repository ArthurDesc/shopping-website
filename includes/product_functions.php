<?php
function getProductReviews($product_id) {
    global $conn;
    $sql = "SELECT a.*, u.nom as nom_utilisateur 
            FROM avis a 
            JOIN utilisateurs u ON a.id_utilisateur = u.id_utilisateur 
            WHERE a.id_produit = ? 
            ORDER BY a.date_creation DESC";  // Tri par date de création décroissante
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getRelatedProducts($id_produit, $limit = 4) {
    global $conn;
    $related_products = array();
    
    $sql = "SELECT id_categorie FROM produit_categorie WHERE id_produit = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_produit);
    $stmt->execute();
    $result = $stmt->get_result();
    $category_ids = [];
    while ($row = $result->fetch_assoc()) {
        $category_ids[] = $row['id_categorie'];
    }
    
    if (empty($category_ids)) {
        // Si toujours pas de catégories, retournez un tableau vide
        return $related_products;
    }
    
    $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
    $sql = "SELECT DISTINCT p.* FROM produits p
            JOIN produit_categorie pc ON p.id_produit = pc.id_produit
            WHERE pc.id_categorie IN ($placeholders) AND p.id_produit != ?
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $types = str_repeat('i', count($category_ids)) . 'ii';
    $params = array_merge($category_ids, [$id_produit, $limit]);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $related_products = $result->fetch_all(MYSQLI_ASSOC);
    
    // Vérifiez si $related_products est un tableau et non vide
    if (!is_array($related_products) || empty($related_products)) {
        $related_products = array(); // Assurez-vous que c'est un tableau vide si aucun résultat n'est trouvé
    }
    
    if (count($related_products) < $limit) {
        // ... (code pour obtenir plus de produits)
    }
    
    return $related_products;
}

function getProductDetails($product_id) {
    global $conn;
    $sql = "SELECT * FROM produits WHERE id_produit = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
