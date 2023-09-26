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

// Récupérer les données du formulaire de connexion
$data = json_decode(file_get_contents("php://input"), true);
$username = $data["username"];
$iduser = $data['id'];

// Lire le contenu du fichier JSON
$fileData = file_get_contents("Data/users.json");
$users = json_decode($fileData, true);

// Recherchez l'utilisateur par nom d'utilisateur
$userFound = null;
foreach ($users as $user) {
    if ($user["username"] === $username) {
        $userFound = $user;
        break;
    }
}

if ($userFound) {
    // L'utilisateur est trouvé, vous pouvez autoriser la connexion ici
    echo json_encode(["success" => true, "message" => "Nom d'utilisateur trouvé"]);
} else {
    // Nom d'utilisateur introuvable
    echo json_encode(["success" => false, "message" => "Nom d'utilisateur introuvable"]);
}
?>
