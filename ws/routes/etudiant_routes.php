<?php
require_once __DIR__ . '/../controllers/EtudiantController.php';
require_once __DIR__ . '/../controllers/UtilisateurController.php';



header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Si c'est une requête "pré-vol" (OPTIONS), on arrête là
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}


// Flight::route('GET /etudiants', ['EtudiantController', 'getAll']);
// Flight::route('GET /etudiants/@id', ['EtudiantController', 'getById']);
// Flight::route('POST /etudiants', ['EtudiantController', 'create']);
// Flight::route('PUT /etudiants/@id', ['EtudiantController', 'update']);
// Flight::route('DELETE /etudiants/@id', ['EtudiantController', 'delete']);


require_once __DIR__ . '/../helpers/Utils.php';
require_once __DIR__ . '/../controllers/FondController.php';
require_once __DIR__ . '/../controllers/TypePretController.php';
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('GET /fonds', ['FondController', 'getAll']);
Flight::route('GET /fonds/total', ['FondController', 'getTotalFond']);
Flight::route('GET /fonds/@id', ['FondController', 'getById']);
Flight::route('POST /fonds', ['FondController', 'create']);

Flight::route('GET /types_pret', ['TypePretController', 'getAll']);
Flight::route('GET /types_pret/@id', ['TypePretController', 'getById']);
Flight::route('POST /types_pret', ['TypePretController', 'create']);
Flight::route('PUT /types_pret/@id', ['TypePretController', 'update']);
Flight::route('DELETE /types_pret/@id', ['TypePretController', 'delete']);

Flight::route('GET /pret', ['PretController', 'getAll']);
Flight::route('GET /pret/@id', ['PretController', 'getById']);
Flight::route('POST /pret', ['PretController', 'create']);
Flight::route('PUT /pret/@id', ['PretController', 'update']);
Flight::route('DELETE /pret/@id', ['PretController', 'delete']);

Flight::route('GET /utilisateur', ['UtilisateurController', 'getAll']);
Flight::route('GET /utilisateur/@id', ['UtilisateurController', 'getById']);
Flight::route('GET /utilisateur/role/@role', ['UtilisateurController', 'getParRole']);
Flight::route('POST /utilisateur', ['UtilisateurController', 'create']);
Flight::route('PUT /utilisateur/@id', ['UtilisateurController', 'update']);
Flight::route('DELETE /utilisateur/@id', ['UtilisateurController', 'delete']);
Flight::route('POST /login', ['UtilisateurController', 'login']);
Flight::route('PUT /utilisateur/@id/password', ['UtilisateurController', 'updatePassword']);
