<?php

class Fond {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM fonds_etablissement");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM fonds_etablissement WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO fonds_etablissement (montant, source, date_ajout) VALUES (?, ?, ?)");
        $stmt->execute([$data->montant, $data->source, $data->date_ajout]);
        $update = $db->prepare("UPDATE fondTotal SET montantTotal = montantTotal + ? WHERE id = 1");
        $update->execute([$data->montant]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE fonds_etablissement SET montant = ?, source = ?, date_ajout = ? WHERE id = ?");
        $stmt->execute([$data->montant, $data->source, $data->date_ajout, $id]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM fonds_etablissement WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function getTotalFond() {
        $db = getDB();
        $stmt = $db->query("SELECT montantTotal FROM fondTotal WHERE id = 1");
        return $stmt->fetchColumn();
    }
}

?>