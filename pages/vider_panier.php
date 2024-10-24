<?php
session_start();
// Vider le panier
unset($_SESSION['panier']);
// Renvoyer une réponse de succès
echo json_encode(['success' => true]);
