<?php
require_once __DIR__ . '/../models/Utilisateur.php';

class UtilisateurController {

    // Récupérer tous les utilisateurs
    public static function getAll() {
        $users = Utilisateur::getAll();
        Flight::json($users);
    }

    // Récupérer un utilisateur par ID
    public static function getById($id) {
        $user = Utilisateur::getById($id);
        Flight::json($user);
    }

    // Récupérer les utilisateurs selon leur rôle (ADMIN ou CLIENT)
    public static function getParRole($role) {
        $users = Utilisateur::getParRole($role);
        Flight::json($users);
    }

    // Créer un nouvel utilisateur
    public static function create() {
        $data = Flight::request()->data;
        $id = Utilisateur::create($data);
        Flight::json(['id' => $id]);
    }

    // Modifier un utilisateur
    public static function update($id) {
        $data = Flight::request()->data;
        Utilisateur::update($id, $data);
        Flight::json(['id' => $id]);
    }

    // Supprimer un utilisateur
    public static function delete($id) {
        Utilisateur::delete($id);
        Flight::json(['id' => $id]);
    }

    // Authentifier (login) utilisateur
    public static function login() {
        $data = Flight::request()->data;
        $user = Utilisateur::authenticate($data['email'], $data['mot_de_passe']);
        if ($user) {
            // Tu peux ici stocker dans une session si besoin
            Flight::json($user);
        } else {
            Flight::halt(401, "Email ou mot de passe incorrect.");
        }
    }

    // Modifier le mot de passe
    public static function updatePassword($id) {
        $data = Flight::request()->data;
        Utilisateur::updatePassword($id, $data['mot_de_passe']);
        Flight::json(['message' => 'Mot de passe mis à jour.']);
    }
}
?>
