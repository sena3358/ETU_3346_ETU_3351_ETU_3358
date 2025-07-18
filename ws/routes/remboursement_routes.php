<?php
require_once __DIR__ . '/../controllers/RemboursementController.php';

// Si c'est une requête "pré-vol" (OPTIONS), on arrête là


Flight::route('GET /remboursements/sommeInterets/@dateDebut', ['RemboursementController', 'sommeInteretsEntreDate']);
Flight::route('GET /remboursements/datesDuPremierEntre/@dateDebut/@dateFin', ['RemboursementController', 'getDatesEntre']);
Flight::route('GET /remboursements/datesDuPremierEntre', ['RemboursementController', 'getDatesDuPremierEntre']);
Flight::route('GET /remboursements/parDate/@date', ['RemboursementController', 'getRemboursementsParDate']);
Flight::route("POST /remboursements/generer/@id", function($id){
    RemboursementController::inserertRemboursements($id);
    echo json_encode(["message" => "Remboursements insérés pour prêt ID $id"]);
});

