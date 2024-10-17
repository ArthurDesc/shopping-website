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

function getRelatedProducts($product_id, $category_ids = null, $limit = 3) {
    global $conn;
    $related_products = array();
    
    if (empty($category_ids)) {
        // Si aucune catégorie n'est fournie, récupérez les catégories du produit
        $sql = "SELECT id_categorie FROM produit_categorie WHERE id_produit = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $category_ids = [];
        while ($row = $result->fetch_assoc()) {
            $category_ids[] = $row['id_categorie'];
        }
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
    $params = array_merge($category_ids, [$product_id, $limit]);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $related_products[] = $row;
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
