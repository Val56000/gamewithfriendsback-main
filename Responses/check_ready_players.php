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

// Chemin vers le fichier JSON
$filePath = '../Data/ready_next_category.json';

// Lire les données existantes (s'il y en a)
$existingData = [];
if (file_exists($filePath)) {
    $existingData = json_decode(file_get_contents($filePath), true);
}

// Rechercher l'instance avec le même "gameInstanceId"
$foundInstance = null;
foreach ($existingData as $item) {
    if ($item['gameInstanceId'] === $gameInstanceId) {
        $foundInstance = $item;
        break;
    }
}

if ($foundInstance !== null) {
    // Vérifier si la clé "players" existe dans l'instance
    if (array_key_exists('players', $foundInstance)) {
        // Compter le nombre de joueurs dont "readyForNextCategory" est égal à "yes"
        $numberOfPlayers = count(array_filter($foundInstance['players'], function ($player) {
            return $player['readyForNextCategory'] === 'yes';
        }));
        
        $response = ['numberOfPlayers' => $numberOfPlayers];
        echo json_encode($response);
    } else {
        $response = ['error' => 'La clé "players" n\'existe pas dans l\'instance.'];
        echo json_encode($response);
    }
} else {
    $response = ['error' => 'Aucune instance trouvée avec le gameInstanceId fourni.'];
    echo json_encode($response);
}
?>

