<?php

class Utilisateur {

    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT id, nom, prenom, email, role, statut FROM utilisateur");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, nom, prenom, email, role, statut FROM utilisateur WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getByEmail($email) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role, statut) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->nom,
            $data->prenom,
            $data->email,
            password_hash($data->mot_de_passe, PASSWORD_BCRYPT),
            $data->role,
            isset($data->statut) ? $data->statut : 'actif'
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE utilisateur SET nom = ?, prenom = ?, email = ?, role = ?, statut = ? WHERE id = ?");
        $stmt->execute([
            $data->nom,
            $data->prenom,
            $data->email,
            $data->role,
            $data->statut,
            $id
        ]);
    }

    public static function updatePassword($id, $mot_de_passe) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE id = ?");
        $stmt->execute([
            password_hash($mot_de_passe, PASSWORD_BCRYPT),
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM utilisateur WHERE id = ?");
        $stmt->execute([$id]);
    }

    public static function authenticate($email, $mot_de_passe) {
        $user = self::getByEmail($email);
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            return $user;
        }
        return false;
    }

    public static function getParRole($role) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, nom, prenom, email, role, statut FROM utilisateur WHERE role = ?");
    $stmt->execute([$role]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
?>
