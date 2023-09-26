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

// Fonction pour lire les scores depuis un fichier JSON
function readScores() {
    $filename = '../Data/scores.json';
    if (file_exists($filename)) {
        $content = file_get_contents($filename);
        if ($content !== false) {
            return json_decode($content, true);
        }
    }
    return [];
}

// Fonction pour écrire les scores dans un fichier JSON
function writeScores($scores) {
    $filename = '../Data/scores.json';
    $success = file_put_contents($filename, json_encode($scores, JSON_PRETTY_PRINT)); // Indentation ajoutée ici
    return $success !== false;
}

$data = json_decode(file_get_contents("php://input"), true);

// Récupérer les données reçues dans des variables
$gameInstanceId = $data['gameInstanceId'];
$points = $data['points'];

$scores = readScores();

$gameIndex = -1;
foreach ($scores as $index => $game) {
    if ($game['gameInstanceId'] == $gameInstanceId) {
        $gameIndex = $index;
        break;
    }
}

if ($gameIndex === -1) {
    $newGame = [
        'gameInstanceId' => $gameInstanceId,
        'Players' => []
    ];
    $scores[] = $newGame;
    $gameIndex = count($scores) - 1;
}

foreach ($points as $point) {
    $userId = $point['userId'];
    $userName = $point['userName'];

    $playerIndex = -1;
    foreach ($scores[$gameIndex]['Players'] as $index => $player) {
        if ($player['userId'] == $userId) {
            $playerIndex = $index;
            break;
        }
    }
    if ($playerIndex === -1) {
        $newPlayer = [
            'userId' => $userId,
            'userName' => $userName,
            'score' => 1
        ];
        $scores[$gameIndex]['Players'][] = $newPlayer;
    } else {
        $scores[$gameIndex]['Players'][$playerIndex]['score'] += 1;
    }
}

if (writeScores($scores)) {
    $response = ['message' => 'Scores mis à jour avec succès', 'success' => true ];
} else {
    $response = ['message' => 'Erreur lors de la mise à jour des scores'];
}

echo json_encode($response);
?>

