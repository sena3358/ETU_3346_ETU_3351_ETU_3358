<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../helpers/Utils.php';


class PretController {
    
    // Récupérer tous les prêts (avec nom client + type)
    public static function getAll() {
        $prets = Pret::getAll();
        Flight::json($prets);
    }

    // Récupérer un prêt par son ID
    public static function getById($id) {
        $pret = Pret::getById($id);
        if (!$pret) {
            Flight::halt(404, 'Prêt non trouvé');
        }
        Flight::json($pret);
    }

    public static function getAmortissement($id) {
        $amortissement = Pret::getAmortissement($id);
        if (!$amortissement) {
            Flight::halt(404, 'Prêt non trouvé');
        }
        Flight::json($amortissement);
    }

    // Créer un nouveau prêt
    public static function create() {
        $data = Flight::request()->data;
        $id = Pret::create($data);
        Flight::json(['id' => $id]);
    }

    // Modifier un prêt existant
    public static function update($id) {
        $data = Flight::request()->query->getData();
        // $data = (object)$_POST;

        try {
            Pret::update($id, (object)$data);
            Flight::json(['id' => $id]);
        } catch (Exception $e) {
            Flight::halt(400, $e->getMessage());
        }
    }

    // Supprimer un prêt
    public static function delete($id) {
        Pret::delete($id);
        Flight::json(['id' => $id]);
    }
}
