<?php
session_start();
require_once '../includes/_db.php';
require_once '../classe/Avis.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    echo json_encode(['error' => 'Utilisateur non connecté']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id_produit = $_POST['id_produit'] ?? $_GET['id_produit'] ?? null;

switch ($action) {
    case 'get':
        getAvis($id_produit);
        break;
    case 'add':
        addAvis($id_produit);
        break;
    case 'edit':
        editAvis();
        break;
    case 'delete':
        deleteAvis();
        break;
    default:
        echo json_encode(['error' => 'Action non reconnue']);
}

function getAvis($id_produit) {
    global $conn;
    $stmt = $conn->prepare("SELECT a.*, u.nom as nom_utilisateur FROM avis a JOIN utilisateurs u ON a.id_utilisateur = u.id_utilisateur WHERE a.id_produit = ? ORDER BY a.date_creation DESC");
    $stmt->bind_param("i", $id_produit);
    $stmt->execute();
    $result = $stmt->get_result();

    $avis = [];
    while ($row = $result->fetch_assoc()) {
        $avis[] = Avis::fromArray($row)->toArray();
    }

    echo json_encode($avis);
}

function addAvis($id_produit) {
    global $conn;
    
    // Validation des données
    $id_utilisateur = filter_var($_SESSION['id_utilisateur'], FILTER_VALIDATE_INT);
    $note = filter_var($_POST['note'], FILTER_VALIDATE_INT);
    $commentaire = trim(htmlspecialchars($_POST['commentaire']));

    // Vérifications supplémentaires
    if ($note < 1 || $note > 5) {
        echo json_encode(['error' => 'Note invalide']);
        return;
    }

    if (strlen($commentaire) < 10) {
        echo json_encode(['error' => 'Le commentaire doit faire au moins 10 caractères']);
        return;
    }

    $stmt = $conn->prepare("INSERT INTO avis (id_produit, id_utilisateur, note, commentaire) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $id_produit, $id_utilisateur, $note, $commentaire);
    $stmt->execute();

    $newAvisId = $stmt->insert_id;

    $stmt = $conn->prepare("SELECT a.*, u.nom as nom_utilisateur FROM avis a JOIN utilisateurs u ON a.id_utilisateur = u.id_utilisateur WHERE a.id_avis = ?");
    $stmt->bind_param("i", $newAvisId);
    $stmt->execute();
    $result = $stmt->get_result();
    $avisData = $result->fetch_assoc();

    $newAvis = Avis::fromArray($avisData);

    echo json_encode($newAvis->toArray());
}

function editAvis() {
    global $conn;
    $id_avis = $_POST['id_avis'];
    $note = $_POST['note'];
    $commentaire = $_POST['commentaire'];
    $id_utilisateur = $_SESSION['id_utilisateur'];

    $stmt = $conn->prepare("UPDATE avis SET note = ?, commentaire = ? WHERE id_avis = ? AND id_utilisateur = ?");
    $stmt->bind_param("isii", $note, $commentaire, $id_avis, $id_utilisateur);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Modification non autorisée ou avis non trouvé']);
    }
}

function deleteAvis() {
    global $conn;
    $id_avis = $_POST['id_avis'];
    $id_utilisateur = $_SESSION['id_utilisateur'];

    $stmt = $conn->prepare("DELETE FROM avis WHERE id_avis = ? AND id_utilisateur = ?");
    $stmt->bind_param("ii", $id_avis, $id_utilisateur);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Suppression non autorisée ou avis non trouvé']);
    }
}
