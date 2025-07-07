<?php

class Pret {
<<<<<<< Updated upstream:ws/models/Pret.php

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
        $stmt = $db->prepare("SELECT * FROM pret WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();

        // Vérification fonds disponibles
        $fonds = $db->query("SELECT SUM(montantTotal) AS total FROM fondTotal")->fetch()['total'];
        
        if ($data->montant > $fonds) {
            throw new Exception("Fonds insuffisants pour accorder ce prêt. Disponible : " . number_format($fonds, 2) . " Ar.");
        }

        $stmt = $db->prepare("INSERT INTO pret (id_utilisateur, id_type_pret, montant, taux, duree, date_debut, statut) 
                              VALUES (?, ?, ?, ?, ?, ?, en cours)");
        $stmt->execute([
            $data->id_utilisateur,
            $data->id_type_pret,
            $data->montant,
            $data->taux,
            $data->duree,
            $data->date_debut,
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
=======
    public static function all() {
        // Jointure avec clients et types_pret
        $query = "SELECT d.*, c.nom, c.prenom, t.nom as type_pret 
                  FROM demandes_pret d
                  JOIN clients c ON d.client_id = c.id
                  JOIN types_pret t ON d.type_pret_id = t.id";
        // Exécution et retour des résultats
    }

    public static function create($data) {
        $query = "INSERT INTO demandes_pret 
                 (client_id, type_pret_id, montant, duree_mois, taux_final) 
                 VALUES (?, ?, ?, ?, ?)";
        // Exécution avec les paramètres
    }
    
    // Autres méthodes...
}


?>
>>>>>>> Stashed changes:tp-flightphp-crud/ws/models/Pret.php
