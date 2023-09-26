<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

$gameInstanceId = $data['gameInstanceId'];
$userIdToUpdate = $data['userId']; // ID du joueur à mettre à jour

// Chemin vers le fichier JSON
$filePath = '../Data/ready_next_category.json';

// Lire les données existantes (s'il y en a)
$existingData = [];
if (file_exists($filePath)) {
    $existingData = json_decode(file_get_contents($filePath), true);
}

// Rechercher l'instance avec le même "gameInstanceId"
$foundInstance = null;
foreach ($existingData as &$item) {
    if ($item['gameInstanceId'] === $gameInstanceId) {
        // Mettre à jour la valeur de "readyForNextCategory" à 'no' pour le joueur spécifique
        foreach ($item['players'] as &$player) {
            if ($player['userId'] === $userIdToUpdate) {
                $player['readyForNextCategory'] = 'no';
                break; // Sortir de la boucle dès que le joueur est mis à jour
            }
        }
        break;
    }
}

// Écrire les données mises à jour dans le fichier JSON avec une indentation
file_put_contents($filePath, json_encode($existingData, JSON_PRETTY_PRINT));

$response = ['message' => 'Statut "readyForNextCategory" mis à jour avec succès pour le joueur spécifique.', 'success' => true];
echo json_encode($response);
?>

