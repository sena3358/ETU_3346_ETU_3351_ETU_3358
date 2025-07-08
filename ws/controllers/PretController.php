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

    public static function validate($id) {
        try {
            // Vérifier d'abord que le prêt est bien en attente
            $pret = Pret::getById($id);
            if (!$pret || $pret['statut'] !== 'en attente') {
                Flight::halt(400, 'Seuls les prêts en attente peuvent être validés');
            }

            // Mettre à jour le statut
            Pret::updateStatus($id, 'en cours');
            
            // Mettre à jour les fonds disponibles (si nécessaire)
            // $fonds = $db->query("SELECT montantTotal FROM fondTotal")->fetch()['montantTotal'];
            // $nouveauMontant = $fonds - $pret['montant'];
            // $db->query("UPDATE fondTotal SET montantTotal = $nouveauMontant");

            Flight::json(['success' => true]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur serveur: ' . $e->getMessage());
        }
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

    public static function validerPret($id) {
    $db = getDB();

    // 1. Récupération du prêt
    $stmt = $db->prepare("SELECT * FROM pret WHERE id = ?");
    $stmt->execute([$id]);
    $pret = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pret) {
        Flight::halt(404, "Prêt introuvable");
    }

    if ($pret['statut'] !== 'en attente') {
        Flight::halt(400, "Le prêt est déjà validé ou refusé");
    }

    // 2. Vérifier fonds disponibles
    $fond = $db->query("SELECT montantTotal FROM fondTotal LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if (!$fond || $fond['montantTotal'] < $pret['montant']) {
        Flight::halt(400, "Fonds insuffisants pour valider ce prêt");
    }

    // 3. Déduire du fondTotal
    $stmt = $db->prepare("UPDATE fondTotal SET montantTotal = montantTotal - ? WHERE id = 1");
    $stmt->execute([$pret['montant']]);

    // 4. Changer le statut
    $stmt = $db->prepare("UPDATE pret SET statut = 'en cours' WHERE id = ?");
    $stmt->execute([$id]);

    //// 5. Générer l'échéancier (annuité constante + assurance)
    //$montant = $pret['montant'];
    //$taux = $pret['taux'] / 100 / 12; // taux mensuel
    //$duree = $pret['duree'];
    //$assurance = isset($pret['assurance']) ? $pret['assurance'] / 100 : 0;
    //$date = new DateTime($pret['date_debut']);
//
    //// Formule annuité constante : A = M * t / (1 - (1 + t)^-n)
    //$annuite = $montant * $taux / (1 - pow(1 + $taux, -$duree));

    //$stmtInsert = $db->prepare("
    //    INSERT INTO remboursement (id_pret, mois, date_echeance, capital, interet, assurance, total)
    //    VALUES (?, ?, ?, ?, ?, ?, ?)
    //");
//
    //for ($i = 1; $i <= $duree; $i++) {
    //    $interet = $montant * $taux;
    //    $capital = $annuite - $interet;
    //    $montant -= $capital;
    //    $assurance_mensuelle = $pret['montant'] * $assurance / $duree;
    //    $total = $capital + $interet + $assurance_mensuelle;

    //    $stmtInsert->execute([
    //        $id,
    //        $i,
    //        $date->format('Y-m-d'),
    //        round($capital, 2),
    //        round($interet, 2),
    //        round($assurance_mensuelle, 2),
    //        round($total, 2)
    //    ]);

    //    $date->modify('+1 month');
    //}

    Flight::json(['message' => 'Prêt validé']);
}

}
