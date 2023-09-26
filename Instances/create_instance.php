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

//Récupérer et décoder les données JSON dans le corps de la requête 
$data = json_decode(file_get_contents("php://input"), true);



if ($data) {
    $file = '../Data/instances.json';

    // Ajouter les nouvelles données au fichier JSON existant ou créer un nouveau fichier
    if (file_exists($file)) {
        $existingData = json_decode(file_get_contents($file), true);
        $existingData[] = $data;
        file_put_contents($file, json_encode($existingData, JSON_PRETTY_PRINT));
    } else {
        file_put_contents($file, json_encode([$data], JSON_PRETTY_PRINT));
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Données non valides']);
}

?>