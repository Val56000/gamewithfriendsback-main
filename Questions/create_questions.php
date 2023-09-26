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
            $questionText = $item['questionText']; // Récupérer la questionText de la requête
            $userId = $item['userId'];
            $userName = $item['userName'];
            $numberPlayers = $item['numberPlayers'];

            // Rechercher l'instance de jeu correspondante
            foreach ($existingData as &$instance) {
                if ($instance['gameInstanceId'] === $instanceId) {
                    // Rechercher la catégorie correspondante dans cette instance
                    foreach ($instance['categories'] as &$category) {
                        if ($category['categoryId'] === $categoryId) {
                            // Vérifier si 'questions' est déjà un tableau
                            if (!is_array($category['questions'])) {
                                $category['questions'] = [];
                            }
                            $existingQuestions = $category['questions'];

                            // Obtenir le prochain index de la question
                            $nextQuestionIndex = count($existingQuestions) + 1;

                            // Créer un nouvel objet de question avec un ID égal à 1 pour la première question
                            $newQuestion = [
                                'questionId' => ($nextQuestionIndex === 1) ? 1 : $nextQuestionIndex,
                                'userId' => $userId, 
                                'userName' => $userName, 
                                'questionText' => $questionText
                            ];

                            // Ajouter le nouvel objet de question
                            $category['questions'][] = $newQuestion;
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
