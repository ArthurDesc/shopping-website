<?php
class ArticleManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addArticle($nom, $description, $prix, $stock, $categories) {
        // Code pour ajouter un article
    }

    public function updateArticle($id, $nom, $description, $prix, $stock) {
        // Code pour modifier un article
    }

    public function deleteArticle($id) {
        // Code pour supprimer un article
    }

    public function getArticle($id) {
        // Code pour récupérer les détails d'un article
    }

    public function getAllArticles() {
        // Code pour récupérer tous les articles
    }

    public function addCategoryToArticle($article_id, $category_id) {
        // Code pour attribuer une catégorie à un article
    }

    public function removeCategoryFromArticle($article_id, $category_id) {
        // Code pour retirer une catégorie d'un article
    }

    public function getArticleCategories($article_id) {
        // Code pour récupérer toutes les catégories d'un article
    }
}
