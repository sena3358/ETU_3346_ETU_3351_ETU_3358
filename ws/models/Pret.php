<?php

class Pret {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("
            SELECT p.*, u.nom AS nom_client, u.prenom AS prenom_client, t.nom AS type_pret
            FROM pret p
            JOIN utilisateur u ON p.id_utilisateur = u.id
            JOIN type_pret t ON p.id_type_pret = t.id
            ORDER BY p.date_debut DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT p.*, u.nom AS nom_client, u.prenom AS prenom_client, t.nom AS type_pret, t.taux_interet AS taux_type
            FROM pret p
            JOIN utilisateur u ON p.id_utilisateur = u.id
            JOIN type_pret t ON p.id_type_pret = t.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function getAmortissement($pretId) {
        $db = getDB();
        
        // D'abord récupérer les infos du prêt
        $pret = self::getById($pretId);
        if (!$pret) return null;
        
        // Calculer le tableau d'amortissement (simplifié)
        $montant = $pret['montant'];
        $valeur=$pret['assurance']+$pret['taux'];
        $tauxMensuel = $valeur / 100 / 12;
        $duree = $pret['duree'];
        $mensualite = $montant * $tauxMensuel * pow(1 + $tauxMensuel, $duree) / (pow(1 + $tauxMensuel, $duree) - 1);
        
        $amortissement = [];
        $capitalRestant = $montant;
        $dateDebut = new DateTime($pret['date_debut']);
        
        for ($mois = 1; $mois <= $duree; $mois++) {
            $interets = $capitalRestant * $tauxMensuel;
            $capital = $mensualite - $interets;
            $capitalRestant -= $capital;
            
            if ($mois === $duree) {
                // Ajustement pour le dernier mois (arrondis)
                $capitalRestant = 0;
            }
            
            // Vérifier si ce mois a été payé
            $paye = false;
            $datePaiement = null;
            
            $amortissement[] = [
                'mois' => $mois,
                'date_echeance' => $dateDebut->format('Y-m-d'),
                'capital' => round($capital, 2),
                'interets' => round($interets, 2),
                'mensualite' => round($mensualite, 2),
                'capital_restant' => max(0, round($capitalRestant, 2)),
                'statut' => $paye ? 'payé' : 'en attente'
            ];
            
            $dateDebut->add(new DateInterval('P1M'));
        }
        
        return $amortissement;
    }


    public static function create($data) {
        $db = getDB();

        $stmt = $db->prepare("
            INSERT INTO pret (
                id_utilisateur, id_type_pret, montant, taux, duree,
                assurance, delai_grace, date_debut, statut
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'en attente')
        ");

        $stmt->execute([
            $data->id_utilisateur,
            $data->id_type_pret,
            $data->montant,
            $data->taux,
            $data->duree,
            $data->assurance ?? 0,
            $data->delai_grace ?? 0,
            $data->date_debut
        ]);

        return $db->lastInsertId();
    }


    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE pret 
                              SET id_utilisateur = ?, id_type_pret = ?, montant = ?, taux = ?, duree = ?, date_debut = ?, statut = ? 
                              WHERE id = ?");
        $stmt->execute([
            $data->id_utilisateur,
            $data->id_type_pret,
            $data->montant,
            $data->taux,
            $data->duree,
            $data->date_debut,
            $data->statut,
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM pret WHERE id = ?");
        $stmt->execute([$id]);
    }

}

?>

