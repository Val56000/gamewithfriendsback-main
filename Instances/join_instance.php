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

if ($data && isset($data['instance'])) {
    $file = '../Data/instances.json';

    // Charger le fichier JSON existant
    $existingData = json_decode(file_get_contents($file), true);

    // Rechercher l'instance correspondante
    $instanceKey = $data['instance'];
    $found = false;

    foreach ($existingData as &$instance) {
        if ($instance['instance'] === $instanceKey) {
            // Ajouter le joueur à cette instance
            $instance['players'][] = [
                'userId' => $data['players']['userId'],
                'userName' => $data['players']['userName'],
                'role' => 'player'
            ];
            $found = true;
            break;
        }
    }

    if ($found) {
        // Écrire les données mises à jour dans le fichier JSON
        file_put_contents($file, json_encode($existingData, JSON_PRETTY_PRINT));
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Instance non trouvée']);
    }
} else {
    echo json_encode(['error' => 'Données non valides']);
}
?>
