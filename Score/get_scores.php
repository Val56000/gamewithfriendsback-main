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

// Charger le contenu du fichier JSON des scores
$filePath = '../Data/scores.json';
$existingData = [];

if (file_exists($filePath)) {
    $existingData = json_decode(file_get_contents($filePath), true);
}

$data = json_decode(file_get_contents("php://input"), true);
$gameInstanceId = $data['gameInstanceId'];

// Rechercher l'instance avec le même "gameInstanceId"
$foundInstance = null;

foreach ($existingData as $instance) {
    if ($instance['gameInstanceId'] == $gameInstanceId) {
        $foundInstance = $instance;
        break;
    }
}

if ($foundInstance !== null) {
    // Trier les joueurs par score décroissant
    usort($foundInstance['Players'], function ($a, $b) {
        return $b['score'] - $a['score'];
    });

    echo json_encode($foundInstance['Players']);
} else {
    echo json_encode([]);
}
?>
