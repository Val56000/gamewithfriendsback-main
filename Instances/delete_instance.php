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



// Assurez-vous que la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Chemin vers le fichier JSON
    $file = '../Data/instances.json';

    $data = json_decode(file_get_contents("php://input"), true);

    // Vérifier si le fichier JSON existe
    if (file_exists($file)) {
        // Lire le contenu du fichier JSON
        $jsonData = json_decode(file_get_contents($file), true);

        // Récupérer la cléInstanceAdmin à supprimer depuis les données JSON envoyées
        $instanceToDelete = $data['instance'];

        // Initialiser un indicateur pour savoir si l'objet a été trouvé et supprimé
        $found = false;

        // Parcourir les données JSON pour trouver et supprimer l'objet
        foreach ($jsonData as $index => $entry) {
            if (isset($entry['instance']) && $entry['instance'] === $instanceToDelete) {
                // Supprimer l'objet s'il est trouvé
                unset($jsonData[$index]);
                $found = true;
                break; // Sortir de la boucle dès que l'objet est trouvé
            }
        }

        if ($found) {
            // Réindexer le tableau pour éviter les clés manquantes
            $jsonData = array_values($jsonData);

            // Réécrire le fichier JSON avec les données mises à jour
            if (file_put_contents($file, json_encode($jsonData, JSON_PRETTY_PRINT))) {
                $response = array('success' => true);
            } else {
                $response = array('error' => 'Échec de l\'écriture du fichier');
            }
        } else {
            $response = array('error' => 'Instance non trouvée');
        }
    } else {
        $response = array('error' => 'Le fichier instances.json n\'existe pas');
    }

    // Convertir la réponse en format JSON et l'afficher
    echo json_encode($response);
} else {
    echo json_encode(array('error' => 'Méthode non autorisée'));
}
?>