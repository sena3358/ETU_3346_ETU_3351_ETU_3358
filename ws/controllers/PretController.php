<?php
<<<<<<< Updated upstream:ws/controllers/PretController.php
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
=======
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../helpers/Utils.php';
// require_once __DIR__ . '/../db.php';

class PretController {
    public function index() {
        $prets = Pret::all();
        echo json_encode($prets);
    }

    public function store() {
    $data = json_decode(file_get_contents('php://input'), true);

    // Connexion BDD
    $db = getDB();

    // Récupération du type de prêt via modèle
    $typePretModel = new TypePret($db);
    $typePret = $typePretModel->find($data['type_pret_id']);

    if (!$typePret) {
        http_response_code(404);
        echo json_encode(['error' => 'Type de prêt non trouvé']);
        return;
    }

    // Validation du montant selon les bornes autorisées
    if ($data['montant'] < $typePret['montant_min'] || $data['montant'] > $typePret['montant_max']) {
        http_response_code(400);
        echo json_encode(['error' => 'Montant non conforme au type de prêt']);
        return;
    }

    // Calcul du taux final
    $data['taux_final'] = $typePret['taux_base'] + ($data['duree_mois'] > 60 ? $typePret['taux_risque'] : 0);

    // Création du prêt (via modèle Pret)
    $pretModel = new Pret($db);
    $pret = $pretModel->create($data);

    http_response_code(201);
    echo json_encode($pret);
}

    
    // Autres méthodes...
}

?>
>>>>>>> Stashed changes:tp-flightphp-crud/ws/controllers/PretController.php
