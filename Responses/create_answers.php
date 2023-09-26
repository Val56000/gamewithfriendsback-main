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

        // Parcourir les données reçues
        foreach ($data as $item) {
            $instanceId = $item['gameInstanceId'];
            $categoryId = $item['categoryId'];
            $questionText = $item['answerText']; // Récupérer la questionText de la requête
            $userId = $item['userId'];
            $userName = $item['userName'];
            $questionId = $item['questionId'];
        
            // Rechercher l'instance de jeu correspondante
            foreach ($existingData as &$instance) {
                if ($instance['gameInstanceId'] === $instanceId) {
                    // Rechercher la catégorie correspondante dans cette instance
                    foreach ($instance['categories'] as &$category) {
                        if ($category['categoryId'] === $categoryId) {
                            // Rechercher la question correspondante dans cette catégorie
                            foreach ($category['questions'] as &$question) {
                                if ($question['questionId'] === $questionId) {
                                    // Initialiser $question['answers'] comme un tableau vide s'il n'existe pas
                                    if (!isset($question['answers'])) {
                                        $question['answers'] = [];
                                    }
        
                                    // Ajouter un nouvel objet dans 'answers'
                                    $newAnswer = [
                                        'answerUserId' => $userId,
                                        'answerUserName' => $userName,
                                        'answerText' => $questionText
                                    ];
        
                                    // Ajouter ce nouvel objet de réponse
                                    $question['answers'][] = $newAnswer;
                                }
                            }
                        }
                    }
                }
            }
        }
        

        // Mettre à jour le fichier JSON avec les nouvelles données
        file_put_contents($file, json_encode($existingData, JSON_PRETTY_PRINT));

        echo json_encode(['success' => true ]);
    } else {
        echo json_encode(['error' => 'Le fichier JSON n\'existe pas']);
    }
} else {
    echo json_encode(['error' => 'Données non valides']);
}
?>
