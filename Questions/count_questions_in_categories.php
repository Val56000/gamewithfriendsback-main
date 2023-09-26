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

if ($data && isset($data['gameInstanceId'])) {
    $file = '../Data/categories-questions.json';

    if (file_exists($file)) {
        $existingData = json_decode(file_get_contents($file), true);

        if ($existingData) { // Vérifiez si les données sont valides
            $gameInstanceId = $data['gameInstanceId'];
            $categoriesSize = [];

            // Recherchez le bon "gameInstanceId" dans le fichier JSON
            foreach ($existingData as $instance) {
                if ($instance['gameInstanceId'] === $gameInstanceId && isset($instance['categories'])) {
                    // Parcourir les catégories de cette instance
                    foreach ($instance['categories'] as $category) {
                        if (isset($category['questions']) && is_array($category['questions'])) {
                            // Ajoutez la taille du tableau de "questions" de cette catégorie au tableau associatif
                            $categoriesSize[$category['categoryName']] = count($category['questions']);
                        }
                    }
                }
            }

            // Renvoyez la taille du tableau de "questions" pour chaque catégorie dans un tableau
            $response = [
                'success' => true,
                'categoriesSize' => $categoriesSize
            ];

            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'Les données JSON ne sont pas valides']);
        }
    } else {
        echo json_encode(['error' => 'Le fichier JSON n\'existe pas']);
    }
} else {
    echo json_encode(['error' => 'Données non valides']);
}
?>
