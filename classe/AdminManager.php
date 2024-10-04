<?php
class AdminManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function isAdmin($user_id) {
        $sql = "SELECT role FROM utilisateurs WHERE id_utilisateur = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        return $user && $user['role'] === 'admin';
    }

    public function getAllAdmins() {
        $sql = "SELECT * FROM utilisateurs WHERE role = 'admin'";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function promoteToAdmin($user_id) {
        $sql = "UPDATE utilisateurs SET role = 'admin' WHERE id_utilisateur = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    public function demoteFromAdmin($user_id) {
        $sql = "UPDATE utilisateurs SET role = 'user' WHERE id_utilisateur = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    public function getAdminDetails($user_id) {
        $sql = "SELECT * FROM utilisateurs WHERE id_utilisateur = ? AND role = 'admin'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateAdminProfile($user_id, $nom, $prenom, $email) {
        $sql = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ? WHERE id_utilisateur = ? AND role = 'admin'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $nom, $prenom, $email, $user_id);
        return $stmt->execute();
    }

    public function changeAdminPassword($user_id, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE utilisateurs SET motdepasse = ? WHERE id_utilisateur = ? AND role = 'admin'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $hashed_password, $user_id);
        return $stmt->execute();
    }
}
