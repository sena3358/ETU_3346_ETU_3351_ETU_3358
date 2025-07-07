<?php
<<<<<<< Updated upstream:ws/controllers/TypePretController.php
require_once __DIR__ . '/../models/Type_Pret.php';

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
?>
=======
require_once __DIR__ . '/../models/TypePret.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


class TypePretController {
    public static function index() {
        $db = getDB();
        $typePretModel = new TypePret($db);
        $types = $typePretModel->getAll();
        
        header('Content-Type: application/json');
        echo json_encode($types);
    }

    public static function store() {
        $db = getDB();
        $data = json_decode(file_get_contents('php://input'), true);

        // Validation simple
        $required = ['nom', 'taux_base', 'taux_risque', 'duree_max', 'montant_min', 'montant_max'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                http_response_code(400);
                echo json_encode(['error' => "Champ $field manquant"]);
                return;
            }
        }

        $typePretModel = new TypePret($db);
        $id = $typePretModel->create($data);

        http_response_code(201);
        echo json_encode(['message' => 'Type de prêt ajouté', 'id' => $id]);
    }

    public static function destroy($params) {
        $id = $params['id'];
        $db = getDB();
        $typePretModel = new TypePret($db);

        $success = $typePretModel->delete($id);
        echo json_encode(['success' => $success]);
    }
}
>>>>>>> Stashed changes:tp-flightphp-crud/ws/controllers/TypePretController.php
