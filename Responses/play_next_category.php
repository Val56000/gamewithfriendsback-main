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
$categoryId = $data['categoryId'];
$userId = $data['userId'];
$userName = $data['userName'];
$readyForNextCategory = $data['readyForNextCategory']; // Nouvelle donnée readyForNextCategory

// Chemin vers le fichier JSON
$filePath = '../Data/ready_next_category.json';

// Lire les données existantes (s'il y en a)
$existingData = [];
if (file_exists($filePath)) {
    $existingData = json_decode(file_get_contents($filePath), true);
}

// Rechercher si une instance avec le même "gameInstanceId" existe déjà
$foundInstanceIndex = -1;
foreach ($existingData as $index => $item) {
    if ($item['gameInstanceId'] === $gameInstanceId) {
        $foundInstanceIndex = $index;
        break;
    }
}

// Si une instance avec le même "gameInstanceId" existe déjà
if ($foundInstanceIndex !== -1) {
    // Rechercher si l'utilisateur existe déjà dans cette instance
    $userIndex = array_search($userId, array_column($existingData[$foundInstanceIndex]['players'], 'userId'));
    if ($userIndex !== false) {
        // Mettre à jour "readyForNextCategory" à 'yes' pour cet utilisateur
        $existingData[$foundInstanceIndex]['players'][$userIndex]['readyForNextCategory'] = 'yes';
    } else {
        // Si l'utilisateur n'existe pas, ajouter le joueur à cette instance
        $playerData = [
            'userId' => $userId,
            'userName' => $userName,
            'readyForNextCategory' => $readyForNextCategory
        ];
        $existingData[$foundInstanceIndex]['players'][] = $playerData;
    }
} else {
    // Si l'instance n'existe pas, créer une nouvelle instance avec le joueur
    $newData = [
        'gameInstanceId' => $gameInstanceId,
        'categoryId' => $categoryId,
        'players' => [
            [
                'userId' => $userId,
                'userName' => $userName,
                'readyForNextCategory' => $readyForNextCategory
            ]
        ]
    ];
    $existingData[] = $newData;
}

// Écrire les données dans le fichier JSON avec une indentation
file_put_contents($filePath, json_encode($existingData, JSON_PRETTY_PRINT));

$response = ['message' => 'Données enregistrées avec succès', 'success' => true];

echo json_encode($response);
?>
