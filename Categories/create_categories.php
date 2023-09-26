<?php
// Autoriser l'accès depuis n'importe quelle origine
header("Access-Control-Allow-Origin: *");

// Autoriser les en-têtes de demande spécifiques
header("Access-Control-Allow-Headers: Content-Type");

// Autoriser les méthodes HTTP spécifiques
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Autoriser l'envoi de cookies (si nécessaire)
header("Access-Control-Allow-Credentials: true");

// Répondre à une requête OPTIONS avec un code de statut 200 (OK) pour les pré-vérifications CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

header('Content-Type: application/json');

// Récupérer et décoder les données JSON dans le corps de la requête 
$data = json_decode(file_get_contents("php://input"), true);

if ($data && isset($data['gameInstanceId'])) {
    $file = '../Data/categories-questions.json';
    $keyInstance = $data['gameInstanceId'];

    $json = file_get_contents($file);
    $instances = json_decode($json, true);

    $found = false;
    foreach ($instances as &$instance) {
        if ($instance['gameInstanceId'] === $keyInstance) {
            // Comptez combien de catégories existent déjà
            $existingCategoriesCount = count($instance['categories']);

            // Incrémentez ce nombre pour obtenir l'ID de la nouvelle catégorie
            $newCategoryId = $existingCategoriesCount + 1; // L'ID est maintenant un entier

            $categoryData = array(
                "categoryId" => $newCategoryId,
                "categoryName" => $data['categoryName'],
                "userId" => $data['userId'],
                "questions" => $data['questions']
            );
            $instance['categories'][] = $categoryData;
            $found = true;
            break;
        }
    }

    // Si 'gameInstanceId' n'a pas été trouvé, créez un nouvel objet avec ces données
    if (!$found) {
        $newObject = array(
            "gameInstanceId" => $keyInstance,
            "categories" => array(
                array(
                    "categoryId" => 1, // L'ID de la première catégorie est 1
                    "categoryName" => $data['categoryName'],
                    "userId" => $data['userId'],
                    "questions" => $data['questions']
                )
            )
        );

        // Ajoutez ce nouvel objet à la liste des instances
        $instances[] = $newObject;
    }

    // Encodez la liste mise à jour en JSON
    $updatedJson = json_encode($instances, JSON_PRETTY_PRINT);

    // Écrivez les données mises à jour dans le fichier
    if (file_put_contents($file, $updatedJson)) {
        echo json_encode(['success' => 'Données enregistrées avec succès']);
    } else {
        echo json_encode(['error' => 'Échec de l\'enregistrement des données']);
    }
} else {
    echo json_encode(['error' => 'Données non valides']);
}
?>
