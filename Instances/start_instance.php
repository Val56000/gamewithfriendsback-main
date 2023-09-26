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

//Récupérer et décoder les données JSON dans le corps de la requête 
$data = json_decode(file_get_contents("php://input"), true);



if ($data && isset($data['instance']) && isset($data['action'])) {
    //Si rôle = 'admin'
    if ($data['action'] == 'admin') {
    
        $file = '../Data/instances.json';

        $keyInstance = $data['instance'];
        
        // Lisez le fichier JSON
        $json = file_get_contents($file);
        $instances = json_decode($json, true);

        // Recherchez l'instance correspondante
        $found = false;
        foreach ($instances as &$instance) {
            if ($instance['instance'] === $keyInstance) {
                $instance['startInstance'] = 'yes';
                $found = true;
                break;
            }
        }

        // Si l'instance correspondante est trouvée et mise à jour, enregistrez les modifications dans le fichier JSON
        if ($found) {
            file_put_contents($file, json_encode($instances, JSON_PRETTY_PRINT));
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Instance non trouvée']);
        }
    }
    //Si rôle = 'player'
    elseif ($data['action'] == 'player') {
        $file = '../Data/instances.json';
        $keyInstance = $data['instance'];
    
        // Lisez le fichier JSON
        $json = file_get_contents($file);
        $instances = json_decode($json, true);
    
        // Recherchez l'instance correspondante
        $found = false;
        foreach ($instances as $instance) {
            if ($instance['instance'] === $keyInstance) {
                if ($instance['startInstance'] === 'yes') {
                    // L'instance a démarré, vous pouvez ajouter le code nécessaire ici
                    echo json_encode(['status' => 'Instance has started', 'success' => true]);
                } else {
                    // L'instance n'a pas encore démarré
                    echo json_encode(['status' => 'Instance has not started']);
                }
                $found = true;
                break;
            }
        }
        // Si l'instance correspondante n'est pas trouvée
        if (!$found) {
            echo json_encode(['error' => 'Instance non trouvée']);
        }
    }
} 
else {
    echo json_encode(['error' => 'Données non valides']);
}

?>