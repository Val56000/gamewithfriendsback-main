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

if (isset($data['gameInstanceId']) && isset($data['categoryId'])) {
    $gameInstanceId = $data['gameInstanceId'];
    $categoryId = $data['categoryId'];
    $file = '../Data/categories-questions.json';

    if (file_exists($file)) {
        $existingData = json_decode(file_get_contents($file), true);

        // Rechercher l'instance de jeu correspondante
        foreach ($existingData as $instance) {
            if ($instance['gameInstanceId'] === $gameInstanceId) {
                // Rechercher la catégorie correspondant au "categoryId"
                foreach ($instance['categories'] as $category) {
                    if ($category['categoryId'] === $categoryId) {
                        echo json_encode(['success' => true, 'categoryInstance' => $category]);
                        return; // Terminer le script après avoir renvoyé les données
                    }
                }
            }
        }
    } else {
        echo json_encode(["message" => "Paramètres 'gameInstanceId' ou 'categoryId' manquants dans la requête"]);
    }
}
?>
