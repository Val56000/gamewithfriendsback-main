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

// Chemin vers le fichier JSON 'categories-questions.json'
$filePath = '../Data/categories-questions.json';

// Lire les données existantes (s'il y en a)
$existingData = [];
if (file_exists($filePath)) {
    $existingData = json_decode(file_get_contents($filePath), true);
}

// Rechercher l'instance avec le même "gameInstanceId"
$foundInstance = null;
foreach ($existingData as $instance) {
    if ($instance['gameInstanceId'] === $gameInstanceId) {
        $foundInstance = $instance;
        break;
    }
}

if ($foundInstance !== null) {
    // Vérifier si la catégorie suivante (categoryId+1) existe dans l'instance
    $categories = $foundInstance['categories'];
    $nextCategoryId = $categoryId + 1;

    $nextCategoryExists = false;
    foreach ($categories as $category) {
        if ($category['categoryId'] === $nextCategoryId) {
            $nextCategoryExists = true;
            break;
        } else{
            $nextCategoryExists = false;
        }
    }

    $response = ['nextCategoryExists' => $nextCategoryExists];
    echo json_encode($response);
} else {
    $response = ['error' => 'Aucune instance trouvée avec le gameInstanceId fourni.'];
    echo json_encode($response);
}
?>
