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

// La clé de l'instance que vous recherchez
$keyInstance = $data['gameInstanceId'];

// Récupérer le contenu du fichier 'categories-questions.json'
$jsonData = file_get_contents('../Data/categories-questions.json');

// Convertir le JSON en tableau associatif
$categoriesData = json_decode($jsonData, true);

// Initialiser un tableau pour stocker les catégories correspondantes à l'instance
$matchingCategories = [];

// Parcourir toutes les données pour trouver les catégories correspondantes
foreach ($categoriesData as $categoryData) {
    if ($categoryData['gameInstanceId'] === $keyInstance) {
        foreach ($categoryData['categories'] as $category) {
            $matchingCategories[] = $category;
        }
    }
}

// $matchingCategories contient maintenant une liste de toutes les catégories correspondantes

// Envoyer la réponse JSON
echo json_encode(['dataCategories' => $matchingCategories]);
?>
