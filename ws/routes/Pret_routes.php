<?php
require_once __DIR__ . '/../controllers/PretController.php';



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Si c'est une requête "pré-vol" (OPTIONS), on arrête là
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Routes pour les prêts
Flight::route('GET /prets', ['PretController', 'getAll']);
Flight::route('GET /prets/@id', ['PretController', 'getById']);
Flight::route('GET /prets/@id/amortissement', ['PretController', 'getAmortissement']);
Flight::route('POST /prets', ['PretController', 'create']);
Flight::route('PUT /prets/@id', ['PretController', 'update']);
Flight::route('DELETE /prets/@id', ['PretController', 'delete']);
Flight::route('PUT /prets/@id/validate', ['PretController', 'validerPret']);