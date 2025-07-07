<?php
require_once __DIR__ . '/../models/Fond.php';

class FondController {
    public static function getAll() {
        $fonds = Fond::getAll();
        Flight::json($fonds);
    }

    public static function getById($id) {
        $fond = Fond::getById($id);
        Flight::json($fond);
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = Fond::create($data);
        Flight::json(['message' => 'Fonds ajouté', 'id' => $id]);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Fond::update($id, $data);
        Flight::json(['message' => 'Fonds modifié']);
    }

    public static function delete($id) {
        Fond::delete($id);
        Flight::json(['message' => 'Fonds supprimé']);
    }

    public static function getTotalFond() {
        $total = Fond::getTotalFond();
        Flight::json(['montantTotal' => $total]);
    }
}
