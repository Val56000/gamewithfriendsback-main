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
    $file = '../Data/categories-questions.json';

    if (file_exists($file)) {
        $existingData = json_decode(file_get_contents($file), true);

        // Trouver le bon gameInstanceId
        $gameInstanceId = $data[0]['gameInstanceId'];
        $categoryId = $data[0]['categoryId'];
        $userId = $data[0]['userId'];

        foreach ($existingData as $game) {
            if ($game['gameInstanceId'] == $gameInstanceId) {
                foreach ($game['categories'] as $category) {
                    if ($category['categoryId'] == $categoryId) {
                        foreach ($category['questions'] as $question) {
                            if ($question['userId'] == $userId && isset($question['answers'])) {
                                $userAnswers = $question['answers'];
                                echo json_encode(['userAnswers' => $userAnswers]);
                                exit(); // Terminer le script après avoir trouvé les réponses
                            }
                        }
                    }
                }
            }
        }

        // Si aucune réponse n'a été trouvée, retourner une erreur
        echo json_encode(['error' => 'Réponses non trouvées']);
    } else {
        echo json_encode(['error' => 'Le fichier JSON n\'existe pas']);
    }
} else {
    echo json_encode(['error' => 'Données non valides']);
}
?>
