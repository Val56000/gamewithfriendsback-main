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

    // Si une entrée avec le même "gameInstanceId" existe déjà
    if ($foundInstanceIndex !== -1) {
        // Rechercher si une entrée avec le même "categoryId" existe déjà dans la catégorie correspondante
        $foundCategoryIndex = -1;
        foreach ($existingData[$foundInstanceIndex]['categories'] as $index => $category) {
            if ($category['categoryId'] === $categoryId) {
                $foundCategoryIndex = $index;
                break;
            }
        }

        // Si une entrée avec le même "categoryId" existe déjà, incrémenter "nbrePlayersReady"
        if ($foundCategoryIndex !== -1) {
            $existingData[$foundInstanceIndex]['categories'][$foundCategoryIndex]['nbrePlayersReady']++;
            $nbrePlayersReady = $existingData[$foundInstanceIndex]['categories'][$foundCategoryIndex]['nbrePlayersReady'];
        } else {
            // Si la catégorie n'existe pas, créer une nouvelle entrée pour la catégorie
            $newCategory = [
                'categoryId' => $categoryId,
                'nbrePlayersReady' => 1
            ];
            $existingData[$foundInstanceIndex]['categories'][] = $newCategory;
            $nbrePlayersReady = 1;
        }
    } else {
        // Si aucune entrée avec le même "gameInstanceId" n'est trouvée, créez une nouvelle entrée
        $newEntry = [
            'gameInstanceId' => $gameInstanceId,
            'categories' => [
                [
                    'categoryId' => $categoryId,
                    'nbrePlayersReady' => 1
                ]
            ]
        ];
        $existingData[] = $newEntry;
        $nbrePlayersReady = 1;
    }

    // Écrire les données dans le fichier JSON avec une indentation
    file_put_contents($filePath, json_encode($existingData, JSON_PRETTY_PRINT));

    // Retourner le nombre de joueurs prêts dans la catégorie actuelle
    echo json_encode(['nbrePlayersReady' => $nbrePlayersReady]);
} else {
    echo json_encode(['error' => 'Données non valides']);
}
?>

