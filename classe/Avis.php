<?php

class Avis {
    private $id_avis;
    private $id_produit;
    private $id_utilisateur;
    private $note;
    private $commentaire;
    private $date_creation;
    private $nom_utilisateur;

    public function __construct($id_avis, $id_produit, $id_utilisateur, $note, $commentaire, $date_creation, $nom_utilisateur) {
        $this->id_avis = $id_avis;
        $this->id_produit = $id_produit;
        $this->id_utilisateur = $id_utilisateur;
        $this->note = $note;
        $this->commentaire = $commentaire;
        $this->date_creation = $date_creation;
        $this->nom_utilisateur = $nom_utilisateur;
    }

    // Getters
    public function getIdAvis() {
        return $this->id_avis;
    }

    public function getIdProduit() {
        return $this->id_produit;
    }

    public function getIdUtilisateur() {
        return $this->id_utilisateur;
    }

    public function getNote() {
        return $this->note;
    }

    public function getCommentaire() {
        return $this->commentaire;
    }

    public function getDateCreation() {
        return $this->date_creation;
    }

    public function getNomUtilisateur() {
        return $this->nom_utilisateur;
    }

    // Setters
    public function setNote($note) {
        $this->note = $note;
    }

    public function setCommentaire($commentaire) {
        $this->commentaire = $commentaire;
    }

    // Méthode pour formater la date de création
    public function getFormattedDate() {
        $date = new DateTime($this->date_creation);
        return $date->format('d/m/Y H:i');
    }

    // Méthode pour vérifier si la note est valide
    public function isNoteValide() {
        return $this->note >= 1 && $this->note <= 5;
    }

    public function toArray() {
        return [
            'id' => $this->id_avis,
            'id_produit' => $this->id_produit,
            'id_utilisateur' => $this->id_utilisateur,
            'note' => $this->note,
            'commentaire' => $this->commentaire,
            'date_creation' => $this->date_creation,
            'nom_utilisateur' => $this->nom_utilisateur
        ];
    }

    public static function fromArray($data) {
        return new self(
            $data['id_avis'],
            $data['id_produit'],
            $data['id_utilisateur'],
            $data['note'],
            $data['commentaire'],
            $data['date_creation'],
            $data['nom_utilisateur']
        );
    }
}
