<?php
require_once __DIR__ . '/../controllers/TypePretController.php';
require_once __DIR__ . '/../controllers/PretController.php';
// require_once __DIR__ . '/../controllers/FondController.php'; // Ajoute ceci si nécessaire

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ------------------- SECURITE & CORS -------------------
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Intercepter requêtes OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ------------------- ROUTES TYPES DE PRÊT -------------------
Flight::route('GET /types-pret', ['TypePretController', 'index']);
Flight::route('POST /types-pret', ['TypePretController', 'store']);
Flight::route('DELETE /types-pret/@id', ['TypePretController', 'destroy']);

// ------------------- ROUTES FONDS -------------------
// Flight::route('GET /fonds', ['FondController', 'index']);
// Flight::route('POST /fonds', ['FondController', 'store']);

// ------------------- ROUTES PRETS COMPLEMENT -------------------
// Flight::route('GET /prets/all', ['PretController', 'all']);
