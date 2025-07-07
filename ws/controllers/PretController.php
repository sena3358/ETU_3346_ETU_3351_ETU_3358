<?php
require_once __DIR__ . '/../models/Pret.php';

class PretController {
    
    // Récupérer tous les prêts (avec nom client + type)
    public static function getAll() {
        $prets = Pret::getAll();
        Flight::json($prets);
    }

    // Récupérer un prêt par son ID
    public static function getById($id) {
        $pret = Pret::getById($id);
        Flight::json($pret);
    }

    // Créer un nouveau prêt
    public static function create() {
            $data = Flight::request()->data;
            $id = Pret::create($data);
            Flight::json(['id' => $id]);
    }

    // Modifier un prêt existant
    public static function update($id) {
        $data = Flight::request()->data;
        Pret::update($id, $data);
        Flight::json(['id' => $id]);
    }

    // Supprimer un prêt
    public static function delete($id) {
        Pret::delete($id);
        Flight::json(['id' => $id]);
    }
}
?>
