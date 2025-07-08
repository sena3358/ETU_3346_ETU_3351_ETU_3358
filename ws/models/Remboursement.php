<?php
require_once __DIR__ . '/../db.php';

class Remboursement {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM remboursement");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
   public static function sommeInterets($date) {
    $db = getDB();
    $stmt = $db->prepare("SELECT SUM(interet) as total FROM remboursement WHERE date_paiement=?");
    $stmt->execute([$date]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
    }
   public static function remboursementDate($date) {
    $db = getDB();
    $stmt = $db->prepare("SELECT r. FROM remboursement r WHERE date_paiement=?");
    $stmt->execute([$date]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function getDateBegin(){
        $db = getDB();
        $stmt = $db->query("SELECT MIN(date_paiement) AS date_debut FROM remboursement");
        return $stmt->fetch(PDO::FETCH_ASSOC)['date_debut'];
    }
    public static function getDatesDuPremierEntre() {
        $dateDebut = self::getDateBegin();
        $dateFin = self::getDateEnd();
        $dates = [];

        // Convertir en objets DateTime
        $start = new DateTime($dateDebut);
        $end = new DateTime($dateFin);
        $end->modify('first day of next month'); // Inclure le mois de dateFin

        while ($start < $end) {
            $dates[] = $start->format('Y-m-d'); // Ajouter la date au format 'YYYY-MM-DD'
            $start->modify('+1 month');         // Passer au mois suivant
        }

        return $dates;
    }
    
    public static function getDatesEntre($dateDebut,$dateFin) {
       
        $dates = [];

        // Convertir en objets DateTime
        $start = new DateTime($dateDebut);
        $end = new DateTime($dateFin);
        $end->modify('first day of next month'); // Inclure le mois de dateFin

        while ($start < $end) {
            $dates[] = $start->format('Y-m-d'); // Ajouter la date au format 'YYYY-MM-DD'
            $start->modify('+1 month');         // Passer au mois suivant
        }

        return $dates;
    }

    public static function getDateEnd(){
        $db = getDB();
        $stmt = $db->query("SELECT MAX(date_paiement) AS date_fin FROM remboursement");
        return $stmt->fetch(PDO::FETCH_ASSOC)['date_fin'];
    }


public static function getTotalInterets($dateDebut, $dateFin) {
    $db = getDB();
    $stmt = $db->query("SELECT SUM(interet) AS total_interet FROM remboursement WHERE date_debut BETWEEN ? AND ?");
    $stmt->execute([$dateDebut, $dateFin]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_interet'];
}
public static function getRemboursementsAvecNomParDate($date) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT 
            remboursement.id AS id_remboursement,
            remboursement.date_paiement,
            remboursement.amortissement,
            remboursement.interet,
            utilisateur.nom,
            utilisateur.prenom
        FROM remboursement
        JOIN pret ON remboursement.id_pret = pret.id
        JOIN utilisateur ON pret.id_utilisateur = utilisateur.id
        WHERE remboursement.date_paiement = ?
    ");
    $stmt->execute([$date]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM remboursement WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO remboursement (pret_id, montant, date_remboursement) VALUES (?, ?, ?)");
        $stmt->execute([$data->pret_id, $data->montant, $data->date_remboursement]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE remboursement SET pret_id = ?, montant = ?, date_remboursement = ? WHERE id = ?");
        $stmt->execute([$data->pret_id, $data->montant, $data->date_remboursement, $id]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM remboursement WHERE id = ?");
        $stmt->execute([$id]);
    }
    public static function insererAmortissementDansRemboursement($pretId) {
    $db = getDB();

    $check = $db->prepare("SELECT COUNT(*) FROM remboursement WHERE id_pret = ?");
    $check->execute([$pretId]);
    $count = $check->fetchColumn();

    if ($count > 0) {
        Flight::halt(400, "Le remboursement pour ce prêt a déjà été généré.");
    }
    else {
    $amortissements = Pret::getAmortissement($pretId);
    if (!$amortissements) return;

    $stmt = $db->prepare("INSERT INTO remboursement (id_pret, date_paiement, amortissement, interet) VALUES (?, ?, ?, ?)");
    $montant=0;
    foreach ($amortissements as $a) {
        $stmt->execute([
            $pretId,
            $a['date_echeance'],
            $a['capital'],
            $a['interets']
        ]);
        $montant+=$a['mensualite'];
    }
        $update = $db->prepare("UPDATE fondTotal SET montantTotal = montantTotal + ? WHERE id = 1");
        $update->execute([$montant]);

    }
}

   
}
