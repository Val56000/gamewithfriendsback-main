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
    // Récupérer les paramètres de la requête
    $gameInstanceId = $data['gameInstanceId'];
    $categoryId = $data['categoryId'];
    $userId = $data['userId'];

    // Chemin vers le fichier JSON
    $filePath = '../Data/categories-questions.json';

    // Lire les données existantes (s'il y en a)
    $existingData = [];
    if (file_exists($filePath)) {
        $existingData = json_decode(file_get_contents($filePath), true);
    }

    // Rechercher le bon "gameInstanceId"
    foreach ($existingData as &$gameInstance) {
        if ($gameInstance['gameInstanceId'] === $gameInstanceId) {
            // Rechercher la bonne "categoryId"
            foreach ($gameInstance['categories'] as &$category) {
                if ($category['categoryId'] === $categoryId) {
                    // Rechercher la bonne "userId"
                    foreach ($category['questions'] as &$question) {
                        if ($question['userId'] === $userId) {
                            // Supprimer les doublons des réponses
                            $uniqueAnswers = [];
                            foreach ($question['answers'] as $answer) {
                                $answerUserId = $answer['answerUserId'];
                                if (!isset($uniqueAnswers[$answerUserId])) {
                                    $uniqueAnswers[$answerUserId] = $answer;
                                }
                            }

                            // Vérifier s'il y a des doublons
                            if (count($uniqueAnswers) === count($question['answers'])) {
                                echo json_encode(['message' => 'Aucun doublon']);
                            } else {
                                // Remplacer les réponses par les réponses uniques
                                $question['answers'] = array_values($uniqueAnswers);

                                // Écrire les données dans le fichier JSON avec une indentation
                                file_put_contents($filePath, json_encode($existingData, JSON_PRETTY_PRINT));

                                echo json_encode(['message' => 'Doublons supprimés avec succès']);
                            }
                            exit();
                        }
                    }
                }
            }
        }
    }

    echo json_encode(['error' => 'Instance de jeu, catégorie ou utilisateur non trouvée']);
} else {
    echo json_encode(['error' => 'Données non valides']);
}
?>
