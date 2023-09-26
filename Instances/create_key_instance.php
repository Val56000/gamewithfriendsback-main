<?php
// Autoriser l'accès depuis n'importe quelle origine
header("Access-Control-Allow-Origin: *");

// Autoriser les en-têtes de demande spécifiques
header("Access-Control-Allow-Headers: Content-Type");

// Autoriser les méthodes HTTP spécifiques
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Autoriser l'envoi de cookies (si nécessaire)
header("Access-Control-Allow-Credentials: true");

// Répondre à une requête OPTIONS avec un code de statut 200 (OK) pour les pré-vérifications CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

// Définit l'en-tête de réponse comme étant JSON
header('Content-Type: application/json');

// Génère un entier aléatoire entre 100000 et 999999 (6 chiffres)
$chiffres = mt_rand(100000, 999999);

// Crée un tableau associatif avec l'entier généré
$response = array('chiffres' => strval($chiffres));

// Convertit le tableau associatif en format JSON et l'affiche
echo json_encode($response);
?>
