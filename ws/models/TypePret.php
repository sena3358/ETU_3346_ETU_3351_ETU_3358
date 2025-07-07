<?php

class TypePret {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Récupère tous les types de prêt
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM types_pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crée un nouveau type de prêt
    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO types_pret 
            (nom, taux_base, taux_risque, duree_max, montant_min, montant_max)
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['nom'],
            $data['taux_base'],
            $data['taux_risque'],
            $data['duree_max'],
            $data['montant_min'],
            $data['montant_max']
        ]);
        return $this->db->lastInsertId();
    }

    // Récupère un type de prêt par ID
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM types_pret WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Supprime un type de prêt par ID
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM types_pret WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
