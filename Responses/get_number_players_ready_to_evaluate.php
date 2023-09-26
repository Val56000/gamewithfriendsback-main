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

if ($data) {
    // Récupérer le gameInstanceId
    $gameInstanceId = $data[0]['gameInstanceId'];
    // Récupérer le categoryId
    $categoryId = $data[0]['categoryId'];

    // Chemin vers le fichier JSON
    $filePath = '../Data/ready_to_evaluate.json';

    // Lire les données existantes (s'il y en a)
    $existingData = [];
    if (file_exists($filePath)) {
        $existingData = json_decode(file_get_contents($filePath), true);
    }

    // Rechercher si une entrée avec le même "gameInstanceId" existe déjà
    $foundInstanceIndex = -1;
    foreach ($existingData as $index => $item) {
        if ($item['gameInstanceId'] === $gameInstanceId) {
            $foundInstanceIndex = $index;
            break;
        }
    }

    if ($foundInstanceIndex !== -1) {
        // Rechercher si une entrée avec le même "categoryId" existe déjà dans la catégorie correspondante
        $foundCategoryIndex = -1;
        foreach ($existingData[$foundInstanceIndex]['categories'] as $index => $category) {
            if ($category['categoryId'] === $categoryId) {
                $foundCategoryIndex = $index;
                break;
            }
        }

        if ($foundCategoryIndex !== -1) {
            $nbrePlayersReady = $existingData[$foundInstanceIndex]['categories'][$foundCategoryIndex]['nbrePlayersReady'];
            echo json_encode(['nbrePlayersReady' => $nbrePlayersReady]);
        } else {
            echo json_encode(['error' => 'Catégorie non trouvée']);
        }
    } else {
        echo json_encode(['error' => 'Instance de jeu non trouvée']);
    }
} else {
    echo json_encode(['error' => 'Données non valides']);
}
?>
