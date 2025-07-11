<?php

require_once __DIR__ . '/../models/Type_Pret.php';
require_once __DIR__ . '/../models/TypePret.php';


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

class TypePretController {
    public static function getAll() {
        $types = Type_Pret::getAll();
        Flight::json($types);
    }

    public static function getById($id) {
        $type = Type_Pret::getById($id);
        Flight::json($type);
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = Type_Pret::create($data);
        Flight::json(['id' => $id]);
    }

    public static function update($id, $data) {
        Type_Pret::update($id, $data);
        Flight::json(['id' => $id]);
    }

    public static function delete($id) {
        Type_Pret::delete($id);
        Flight::json(['id' => $id]);
    }
}









