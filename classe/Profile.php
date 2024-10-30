<?php

class Profile {
    private $conn;
    private $id_utilisateur;
    private $data;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Charger le profil
    public function loadProfile($id_utilisateur) {
        $this->id_utilisateur = $id_utilisateur;
        $stmt = $this->conn->prepare("SELECT nom, prenom, email, adresse, telephone FROM utilisateurs WHERE id_utilisateur = ?");
        $stmt->bind_param("i", $id_utilisateur);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $this->data = $result->fetch_assoc();
            return true;
        }
        return false;
    }

    // Mettre à jour un champ unique
    public function updateSingleField($field, $value) {
        // Liste des champs autorisés
        $allowed_fields = ['nom', 'prenom', 'adresse', 'telephone', 'email'];
        
        if (!in_array($field, $allowed_fields)) {
            throw new Exception('Champ non autorisé');
        }

        // Validation spécifique selon le champ
        $this->validateField($field, $value);

        $stmt = $this->conn->prepare("UPDATE utilisateurs SET $field = ? WHERE id_utilisateur = ?");
        $stmt->bind_param("si", $value, $this->id_utilisateur);
        
        return $stmt->execute();
    }

    // Validation des champs
    private function validateField($field, $value) {
        switch ($field) {
            case 'nom':
            case 'prenom':
                if (strlen($value) < 2 || !preg_match("/^[A-Za-zÀ-ÿ\s-]{2,}$/", $value)) {
                    throw new Exception("Le $field doit contenir au moins 2 caractères et uniquement des lettres");
                }
                break;

            case 'telephone':
                if (!preg_match("/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/", $value)) {
                    throw new Exception("Format de téléphone invalide");
                }
                break;

            case 'adresse':
                if (strlen($value) < 5) {
                    throw new Exception("L'adresse doit contenir au moins 5 caractères");
                }
                break;

            case 'email':
                // Regex plus robuste pour l'email
                $emailRegex = "/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/";
                if (!preg_match($emailRegex, $value)) {
                    throw new Exception("Format d'email invalide");
                }
                // Vérification de la longueur
                if (strlen($value) > 254) {
                    throw new Exception("L'adresse email est trop longue");
                }
                // Vérification du domaine
                $domain = substr(strrchr($value, "@"), 1);
                if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
                    throw new Exception("Le domaine de l'email n'existe pas");
                }
                break;
        }
    }

    // Mettre à jour le mot de passe
    public function updatePassword($current_password, $new_password) {
        try {
            // Vérifier l'ancien mot de passe
            $stmt = $this->conn->prepare("SELECT motdepasse FROM utilisateurs WHERE id_utilisateur = ?");
            $stmt->bind_param("i", $this->id_utilisateur);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if (!$user || !password_verify($current_password, $user['motdepasse'])) {
                throw new Exception('Mot de passe actuel incorrect');
            }

            // Validation du nouveau mot de passe
            if (strlen($new_password) < 8) {
                throw new Exception('Le nouveau mot de passe doit contenir au moins 8 caractères');
            }

            // Hasher et mettre à jour le nouveau mot de passe
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE utilisateurs SET motdepasse = ? WHERE id_utilisateur = ?");
            $stmt->bind_param("si", $hashed_password, $this->id_utilisateur);
            
            if (!$stmt->execute()) {
                throw new Exception('Erreur lors de la mise à jour du mot de passe');
            }

            return true;
        } catch (Exception $e) {
            throw $e;
        }
    }

    // Récupérer les données du profil
    public function getData() {
        return $this->data;
    }

    // Vérifier si un email existe déjà (pour la mise à jour d'email)
    public function isEmailExists($email) {
        $stmt = $this->conn->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ? AND id_utilisateur != ?");
        $stmt->bind_param("si", $email, $this->id_utilisateur);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
}
