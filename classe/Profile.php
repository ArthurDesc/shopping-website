<?php
class Profile {
    private $id;
    private $nom;
    private $prenom;
    private $email;
    private $telephone;
    private $adresse;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Getters et Setters
    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getEmail() { return $this->email; }
    public function getTelephone() { return $this->telephone; }
    public function getAdresse() { return $this->adresse; }

    public function setNom($nom) { $this->nom = $nom; }
    public function setPrenom($prenom) { $this->prenom = $prenom; }
    public function setEmail($email) { $this->email = $email; }
    public function setTelephone($telephone) { $this->telephone = $telephone; }
    public function setAdresse($adresse) { $this->adresse = $adresse; }

    // Charger le profil depuis la BDD
    public function loadProfile($id_utilisateur) {
        $stmt = $this->conn->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = ?");
        $stmt->bind_param("i", $id_utilisateur);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            $this->id = $user['id_utilisateur'];
            $this->nom = $user['nom'];
            $this->prenom = $user['prenom'];
            $this->email = $user['email'];
            $this->telephone = $user['telephone'];
            $this->adresse = $user['adresse'];
            return true;
        }
        return false;
    }

    // Mettre à jour le profil
    public function updateProfile($data) {
        // Validation des données
        $this->validateData($data);

        // Vérification email unique
        if ($this->isEmailTaken($data['email'])) {
            throw new Exception('Cet email est déjà utilisé');
        }

        try {
            $this->conn->begin_transaction();

            $stmt = $this->conn->prepare("
                UPDATE utilisateurs 
                SET nom = ?, prenom = ?, email = ?, telephone = ?, adresse = ?, date_modification = NOW()
                WHERE id_utilisateur = ?
            ");

            $stmt->bind_param("sssssi", 
                $data['nom'],
                $data['prenom'],
                $data['email'],
                $data['telephone'],
                $data['adresse'],
                $this->id
            );

            if (!$stmt->execute()) {
                throw new Exception('Erreur lors de la mise à jour du profil');
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    // Validation des données
    private function validateData($data) {
        if (empty($data['nom']) || empty($data['prenom']) || empty($data['email'])) {
            throw new Exception('Tous les champs obligatoires doivent être remplis');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Format d\'email invalide');
        }

        if (!empty($data['telephone'])) {
            if (!preg_match("/^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/", $data['telephone'])) {
                throw new Exception('Format de numéro de téléphone invalide');
            }
        }

        if (strlen($data['nom']) < 2 || strlen($data['prenom']) < 2) {
            throw new Exception('Le nom et le prénom doivent faire au moins 2 caractères');
        }

        if (!preg_match("/^[A-Za-zÀ-ÿ\s-]+$/", $data['nom']) || !preg_match("/^[A-Za-zÀ-ÿ\s-]+$/", $data['prenom'])) {
            throw new Exception('Le nom et le prénom ne peuvent contenir que des lettres, espaces et tirets');
        }
    }

    // Vérifier si l'email est déjà utilisé
    private function isEmailTaken($email) {
        $stmt = $this->conn->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ? AND id_utilisateur != ?");
        $stmt->bind_param("si", $email, $this->id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // Convertir en tableau pour JSON
    public function toArray() {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse
        ];
    }
} 