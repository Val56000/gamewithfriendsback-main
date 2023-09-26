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

header('Content-Type: application/json');

// Récupérer et décoder les données JSON dans le corps de la requête 
$data = json_decode(file_get_contents("php://input"), true);

// La clé de l'instance que vous recherchez
$keyInstance = $data['instance'];

// Récupérer le contenu du fichier 'instances.json'
$jsonData = file_get_contents('../Data/instances.json');

// Convertir le JSON en tableau associatif
$instances = json_decode($jsonData, true);

// Initialiser un tableau pour stocker les joueurs correspondants à l'instance
$matchingPlayers = [];

// Parcourir toutes les instances pour trouver celles qui correspondent
foreach ($instances as $instanceData) {
    if ($instanceData['instance'] === $keyInstance) {
        $matchingPlayers[] = $instanceData['players'];
    }
}

// $matchingPlayers contient maintenant une liste de tous les joueurs correspondants

// Envoyer la réponse JSON
// Utilisez count() pour obtenir le nombre de joueurs dans l'instance
$numberOfPlayers = count($matchingPlayers);

// Envoyer la réponse JSON avec le nombre de joueurs
echo json_encode(['dataPlayers' => $matchingPlayers, 'numberOfPlayers' => $numberOfPlayers]);
?>