<?php
require_once __DIR__ . '/../models/Remboursement.php';
require_once __DIR__ . '/../helpers/Utils.php';


class SimulationController {
   public static function sommeInteretsEntreDate($dateDebut) {
    $somme = Remboursement::sommeInterets($dateDebut);
    if($somme['total']==null){
        $somme['total']=0;
    }
    Flight::json($somme['total']);
    }
    public static function getDatesDuPremierEntre() {
        $dates = Remboursement::getDatesDuPremierEntre();
        Flight::json($dates);
    }
    public static function getDatesEntre($dateDebut, $dateFin) {
        $dates = Remboursement::getDatesEntre($dateDebut, $dateFin);
        Flight::json($dates);
    }
    public static function getRemboursementsParDate($date) {
        $resultats = Remboursement::getRemboursementsAvecNomParDate($date);
        Flight::json($resultats);
    }
    public static function inserertRemboursements($id) {
        Remboursement::insererAmortissementDansRemboursement($id);
    }

}
