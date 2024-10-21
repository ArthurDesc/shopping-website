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
        $stmt->close();
        
        return $user && $user['role'] === 'admin';
    }

    public function getAllAdmins() {
        $sql = "SELECT * FROM utilisateurs WHERE role = 'admin'";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function promoteToAdmin($user_id) {
        $sql = "UPDATE utilisateurs SET role = 'admin' WHERE id_utilisateur = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$user_id]);
    }

    public function demoteFromAdmin($user_id) {
        $sql = "UPDATE utilisateurs SET role = 'user' WHERE id_utilisateur = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$user_id]);
    }

    public function getAdminDetails($user_id) {
        $sql = "SELECT * FROM utilisateurs WHERE id_utilisateur = ? AND role = 'admin'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAdminProfile($user_id, $nom, $prenom, $email) {
        $sql = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ? WHERE id_utilisateur = ? AND role = 'admin'";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nom, $prenom, $email, $user_id]);
    }

    public function changeAdminPassword($user_id, $new_password) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE utilisateurs SET motdepasse = ? WHERE id_utilisateur = ? AND role = 'admin'";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$hashed_password, $user_id]);
    }
}
