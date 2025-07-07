<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/routes/prets.php';
require_once __DIR__ . '/db.php'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



// ------------------- SECURITE & CORS -------------------
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// // Intercepter requêtes OPTIONS
// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//     http_response_code(200);
//     exit();
// }


// ------------------- CONFIGURATION INITIALE -------------------
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0); // Désactiver en production
error_reporting(E_ALL);


// ------------------- AUTHENTIFICATION -------------------
Flight::map('authenticate', function() {
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        Flight::halt(401, json_encode(['error' => 'Authentification requise']));
    }
    // Ici vous pourriez vérifier les credentials contre la DB
});

// ------------------- FONCTIONS METIERS -------------------
Flight::map('validateFinancialData', function($data) {
    $required = ['montant', 'taux', 'duree_mois', 'client_id'];
    foreach ($required as $field) {
        if (!isset($data->$field)) {
            Flight::halt(400, json_encode(['error' => "Champ $field manquant"]));
        }
    }
    
    if ($data->montant <= 0 || $data->taux <= 0 || $data->duree_mois <= 0) {
        Flight::halt(400, json_encode(['error' => 'Les valeurs doivent être positives']));
    }
});

function calculerMensualite($montant, $tauxAnnuel, $dureeMois) {
    $tauxMensuel = $tauxAnnuel / 100 / 12;
    return $montant * $tauxMensuel / (1 - pow(1 + $tauxMensuel, -$dureeMois));
}

function calculerScoreClient($clientId) {
    $db = getDB();
    // Logique simplifiée de calcul de score
    $stmt = $db->prepare("SELECT COUNT(*) as nb_prets, SUM(montant) as total_prets 
                         FROM prets 
                         WHERE client_id = ? AND statut = 'approuvé'");
    $stmt->execute([$clientId]);
    $historique = $stmt->fetch();
    
    // Score basé sur l'historique (à adapter)
    return $historique ? min(100, 70 + ($historique['nb_prets'] * 5) - ($historique['total_prets'] / 10000)) : 70;
}

// ------------------- ROUTES CLIENTS -------------------
Flight::route('GET /clients', function() {
    Flight::authenticate();
    
    $db = getDB();
    $search = Flight::request()->query['search'] ?? '';
    
    $stmt = $db->prepare("SELECT * FROM clients WHERE nom LIKE ? OR prenom LIKE ?");
    $stmt->execute(["%$search%", "%$search%"]);
    
    Flight::json($stmt->fetchAll());
});

Flight::route('POST /clients', function() {
    Flight::authenticate();
    
    $data = Flight::request()->data;
    $db = getDB();
    
    // Validation
    if (empty($data->nom) || empty($data->prenom) || empty($data->email)) {
        Flight::halt(400, json_encode(['error' => 'Nom, prénom et email requis']));
    }
    
    try {
        $db->beginTransaction();
        
        $stmt = $db->prepare("INSERT INTO clients (nom, prenom, email, solde) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data->nom, 
            $data->prenom, 
            $data->email,
            $data->solde ?? 0.00
        ]);
        
        $clientId = $db->lastInsertId();
        $db->commit();
        
        Flight::json([
            'id' => $clientId,
            'message' => 'Client créé avec succès'
        ], 201);
        
    } catch (PDOException $e) {
        $db->rollBack();
        if ($e->getCode() == 23000) { // Violation de contrainte unique
            Flight::halt(400, json_encode(['error' => 'Email déjà utilisé']));
        } else {
            Flight::halt(500, json_encode(['error' => 'Erreur création client']));
        }
    }
});

// ------------------- ROUTES TYPES DE PRÊT -------------------
Flight::route('GET /types-pret', function() {
    Flight::authenticate();
    
    $db = getDB();
    $stmt = $db->query("SELECT * FROM types_pret");
    Flight::json($stmt->fetchAll());
});

Flight::route('POST /types-pret', function() {
    Flight::authenticate();
    
    $data = Flight::request()->data;
    $db = getDB();
    
    // Validation
    $required = ['nom', 'taux_base', 'taux_risque', 'duree_max', 'montant_min', 'montant_max'];
    foreach ($required as $field) {
        if (empty($data->$field)) {
            Flight::halt(400, json_encode(['error' => "Le champ $field est requis"]));
        }
    }
    
    try {
        $stmt = $db->prepare("INSERT INTO types_pret 
                            (nom, taux_base, taux_risque, duree_max, montant_min, montant_max) 
                            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->nom,
            $data->taux_base,
            $data->taux_risque,
            $data->duree_max,
            $data->montant_min,
            $data->montant_max
        ]);
        
        Flight::json([
            'id' => $db->lastInsertId(),
            'message' => 'Type de prêt créé avec succès'
        ], 201);
        
    } catch (PDOException $e) {
        Flight::halt(500, json_encode(['error' => 'Erreur création type de prêt']));
    }
});

// ------------------- ROUTES DEMANDES DE PRÊT -------------------
Flight::route('POST /demandes-pret', function() {
    Flight::authenticate();
    
    $data = Flight::request()->data;
    $db = getDB();
    
    // Validation
    $required = ['client_id', 'type_pret_id', 'montant', 'duree_mois'];
    foreach ($required as $field) {
        if (empty($data->$field)) {
            Flight::halt(400, json_encode(['error' => "Le champ $field est requis"]));
        }
    }
    
    try {
        // Vérifier le type de prêt
        $stmt = $db->prepare("SELECT * FROM types_pret WHERE id = ?");
        $stmt->execute([$data->type_pret_id]);
        $typePret = $stmt->fetch();
        
        if (!$typePret) {
            Flight::halt(404, json_encode(['error' => 'Type de prêt non trouvé']));
        }
        
        // Vérifier les limites
        if ($data->montant < $typePret['montant_min'] || $data->montant > $typePret['montant_max']) {
            Flight::halt(400, json_encode(['error' => 'Montant hors limites']));
        }
        
        if ($data->duree_mois > $typePret['duree_max']) {
            Flight::halt(400, json_encode(['error' => 'Durée trop longue']));
        }
        
        // Calculer le taux final basé sur le score du client
        $score = calculerScoreClient($data->client_id);
        $tauxFinal = $typePret['taux_base'] + ($typePret['taux_risque'] * (1 - $score/100));
        
        // Créer la demande
        $stmt = $db->prepare("INSERT INTO demandes_pret 
                            (client_id, type_pret_id, montant, duree_mois, taux_final) 
                            VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->client_id,
            $data->type_pret_id,
            $data->montant,
            $data->duree_mois,
            $tauxFinal
        ]);
        
        Flight::json([
            'id' => $db->lastInsertId(),
            'taux_applique' => $tauxFinal,
            'message' => 'Demande de prêt enregistrée'
        ], 201);
        
    } catch (PDOException $e) {
        Flight::halt(500, json_encode(['error' => 'Erreur création demande']));
    }
});

Flight::route('GET /demandes-pret', function() {
    Flight::authenticate();
    
    $db = getDB();
    $status = Flight::request()->query['status'] ?? null;
    
    $sql = "SELECT d.*, c.nom, c.prenom, t.nom as type_pret 
           FROM demandes_pret d
           JOIN clients c ON d.client_id = c.id
           JOIN types_pret t ON d.type_pret_id = t.id";
           
    if ($status) {
        $sql .= " WHERE d.statut = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$status]);
    } else {
        $stmt = $db->query($sql);
    }
    
    Flight::json($stmt->fetchAll());
});

// ------------------- ROUTES PRÊTS -------------------
Flight::route('GET /prets', function() {
    Flight::authenticate();
    
    $db = getDB();
    $status = Flight::request()->query['status'] ?? null;
    
    $sql = "SELECT p.*, c.nom, c.prenom FROM prets p JOIN clients c ON p.client_id = c.id";
    if ($status) {
        $sql .= " WHERE p.statut = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$status]);
    } else {
        $stmt = $db->query($sql);
    }
    
    Flight::json($stmt->fetchAll());
});

Flight::route('POST /prets', function() {
    Flight::authenticate();
    $data = Flight::request()->data;
    
    // Validation
    Flight::validateFinancialData($data);
    
    $db = getDB();
    try {
        $db->beginTransaction();
        
        // Vérifier si le client existe
        $stmt = $db->prepare("SELECT id FROM clients WHERE id = ?");
        $stmt->execute([$data->client_id]);
        if (!$stmt->fetch()) {
            Flight::halt(404, json_encode(['error' => 'Client non trouvé']));
        }
        
        // Calcul de la mensualité
        $mensualite = calculerMensualite($data->montant, $data->taux, $data->duree_mois);
        
        // Création du prêt
        $stmt = $db->prepare("INSERT INTO prets 
            (client_id, montant, taux, duree_mois, date_debut, statut) 
            VALUES (?, ?, ?, ?, CURDATE(), 'en_attente')");
        
        $stmt->execute([
            $data->client_id,
            $data->montant,
            $data->taux,
            $data->duree_mois
        ]);
        
        $pretId = $db->lastInsertId();
        $db->commit();
        
        Flight::json([
            'pret_id' => $pretId,
            'mensualite' => $mensualite,
            'message' => 'Prêt enregistré'
        ], 201);
        
    } catch (PDOException $e) {
        $db->rollBack();
        Flight::halt(500, json_encode(['error' => 'Erreur création prêt']));
    }
});

Flight::route('PUT /prets/@id/statut', function($id) {
    Flight::authenticate();
    
    $data = Flight::request()->data;
    $db = getDB();
    
    if (!in_array($data->statut, ['approuvé', 'rejeté'])) {
        Flight::halt(400, json_encode(['error' => 'Statut invalide']));
    }
    
    try {
        $db->beginTransaction();
        
        // Mise à jour du statut
        $stmt = $db->prepare("UPDATE prets SET statut = ? WHERE id = ?");
        $stmt->execute([$data->statut, $id]);
        
        // Si approuvé, créditer le client
        if ($data->statut === 'approuvé') {
            $stmt = $db->prepare("UPDATE clients c 
                JOIN prets p ON c.id = p.client_id 
                SET c.solde = c.solde + p.montant 
                WHERE p.id = ?");
            $stmt->execute([$id]);
        }
        
        $db->commit();
        Flight::json(['message' => 'Statut mis à jour']);
        
    } catch (PDOException $e) {
        $db->rollBack();
        Flight::halt(500, json_encode(['error' => 'Erreur mise à jour statut']));
    }
});

// ------------------- ROUTES COMPLEMENTAIRES -------------------
Flight::route('GET /clients/@id/prets', function($id) {
    Flight::authenticate();
    
    $db = getDB();
    $stmt = $db->prepare("SELECT p.*, t.nom as type_pret 
                         FROM prets p
                         LEFT JOIN demandes_pret d ON p.id = d.id
                         LEFT JOIN types_pret t ON d.type_pret_id = t.id
                         WHERE p.client_id = ?");
    $stmt->execute([$id]);
    
    Flight::json($stmt->fetchAll());
});

Flight::route('GET /prets/@id/amortissement', function($id) {
    Flight::authenticate();
    
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM prets WHERE id = ?");
    $stmt->execute([$id]);
    $pret = $stmt->fetch();
    
    if (!$pret) {
        Flight::halt(404, json_encode(['error' => 'Prêt non trouvé']));
    }
    
    $amortissement = [];
    $capitalRestant = $pret['montant'];
    $tauxMensuel = $pret['taux'] / 100 / 12;
    $mensualite = calculerMensualite($pret['montant'], $pret['taux'], $pret['duree_mois']);
    
    for ($i = 1; $i <= $pret['duree_mois']; $i++) {
        $interets = $capitalRestant * $tauxMensuel;
        $capital = $mensualite - $interets;
        
        $amortissement[] = [
            'mois' => $i,
            'capital' => round($capital, 2),
            'interets' => round($interets, 2),
            'mensualite' => round($mensualite, 2),
            'capital_restant' => round($capitalRestant, 2)
        ];
        
        $capitalRestant -= $capital;
    }
    
    Flight::json($amortissement);
});

// ------------------- GESTION DES ERREURS -------------------
Flight::map('notFound', function() {
    Flight::halt(404, json_encode(['error' => 'Endpoint non trouvé']));
});

Flight::map('error', function(Exception $ex) {
    error_log($ex->getMessage());
    Flight::halt(500, json_encode(['error' => 'Erreur interne du serveur']));
});

Flight::map('error', function(Exception $ex) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => $ex->getMessage(),
        'trace' => $ex->getTrace()
    ]);
});


// ------------------- LANCEMENT DE L'APPLICATION -------------------
Flight::start();
