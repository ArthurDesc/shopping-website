<?php
class AdminManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function isAdmin($user_id) {
        // Code pour vérifier si l'utilisateur est un administrateur
    }
}
