<?php
// controller admin_dash.php

// Création / Reprise de la session existante
session_start();

// Appel du model
include '../models/admin_dash.php';

// Appel des fonctions utiles
include '../assets/functions/user_is_connected.php';
include '../assets/functions/user_have_right.php';

// Variable contenant les données de l'admin
$admin_info = '';

// Variable contenant les données des utilisateurs
$all_user_list = '';
    
// Variable contenant les données des sessions
$all_session_list = '';

// variable de contrôle
$erreur = false;


if (user_is_connected()){
    try {
        $admin_info = admin_info($pdo, $_SESSION['user']['user_id']);
        if ($admin_info['user_statut'] != 2){
            $erreur = true;
        }
    } catch (\Throwable $th) {
        echo $th;
    };
}
else {
    $erreur = true;
};


if ($erreur == true){
    // echo '$erreur == true';
    header('location: ../../');
}
else {

    // Appel des informations des utilisateurs
    try {
        $all_user_list = all_user_list($pdo);
    } 
    catch (\Throwable $th) {
        echo $th;
    }
    
    // Appel des informations des session
    try {
        $all_session_list = all_session_list($pdo);
    } 
    catch (\Throwable $th) {
        echo $th;
    }

    // ------------------------------------------------------------------------------------
    // Supprimer un utilisateur
    // ------------------------------------------------------------------------------------
    if (isset($_POST['del_user'])){
        $user_id = $_POST['del_user'];
        if (delete_user($pdo, $user_id)){
            $msg .= '<p>L\'utilisateur ID = "' . $user_id . '" a été supprimé<p>';
        }
    }

    // ------------------------------------------------------------------------------------
    // Créer un utlisateur
    // ------------------------------------------------------------------------------------
    if (isset($_POST['create_user']) && 
        isset($_POST['user_pseudo']) && 
        isset($_POST['user_password']) && 
        isset($_POST['user_mail']) &&
        isset($_POST['user_date']) &&
        isset($_POST['user_statut'])
    ) 
    {
        // Controle et assignation des variables
        $user_pseudo = trim($_POST['user_pseudo']);
        $user_pseudo = htmlspecialchars($user_pseudo, ENT_QUOTES, 'UTF-8');
        $user_password = trim($_POST['user_password']);
        $user_mail = trim($_POST['user_mail']);
        $user_date = trim($_POST['user_date']);
        $user_statut = trim($_POST['user_statut']);

        // variable de controle 
        $erreur = false;

        // Contrôle de la taille du pseudo : entre 2 et 16
        $user_length = iconv_strlen($user_pseudo);
        if ($user_length < 2 || $user_length > 16) {
            $msg .= '<p>Le pseudo doit contenir entre 2 et 16 caractères.</p>';
            $erreur = true;
        }

        // Contrôle du mail vide
        if ($user_mail == ""){
            $msg .= '<p>L\'adresse e-mail est vide.</p>';
            $erreur = true;
        }

        if (preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/ ", $user_mail) == false){
            // Contrôle des caractères autorisé dans l'adresse mail
            // Caractères autorisés : "a-z A-Z 0-9 . _ -" @ "a-z A-Z 0-9" . "a-z A-Z" limite 2-5 caractères
            $msg .= '<p>Le format de l\'email est invalides.</p>';
            $erreur = true;
        }

        // disponibilité du mail
        if (verif_mail($pdo, $user_mail)) {
            $msg .= '<p>Cette adresse mail est déjà associée à un compte.</p>';
            $erreur = true;
        };

        // le mot de passe doit avoir au moins 6 caractères
        $password_length = iconv_strlen($user_password);
        if ($password_length < 6) {
            $msg .= '<p>Le mot de passe doit contenir au moins 6 caractères.</p>';
            $erreur = true;
        };

        // Enregistrement du compte utilisateur
        if($erreur == false) {

            // hachage du mdp
            $user_password = password_hash($user_password, PASSWORD_DEFAULT);

            // Ajoute du compte utilisateur dans la bdd
            create_user($pdo, $user_pseudo, $user_mail, $user_password, $user_date, $user_statut);
            $msg .= '<p>L\'utilisateur "' . $user_pseudo . '" a été crée<p>';
        };
    };

    // ------------------------------------------------------------------------------------
    // Supprimer une session
    // ------------------------------------------------------------------------------------
    if (isset($_POST['delete_session'])){
        $session_id = $_POST['delete_session'];
        if (delete_session($pdo, $session_id)){
            $msg .= '<p>La session ID = "' . $session_id . '" a été supprimé<p>';
        }
    }

    // ------------------------------------------------------------------------------------
    // Créer une session
    // ------------------------------------------------------------------------------------
    if (isset($_POST['create_session']) && 
        isset($_POST['session_name']) && 
        isset($_POST['session_type']) && 
        isset($_POST['session_user_id']) &&
        isset($_POST['session_update'])) {

        // Controle et assignation des variables
        $session_name = trim($_POST['session_name']);
        $session_name = htmlspecialchars($session_name, ENT_QUOTES, 'UTF-8');
        $session_type = $_POST['session_type'];
        $session_user_id = $_POST['session_user_id'];
        $session_update = $_POST['session_update'];
    
        // variable de contrôle 
        $erreur = false;
    
        // Contrôle de la taille de session_name : entre 1 et 16
        $session_name_length = iconv_strlen($session_name);
        if ($session_name_length < 1 || $session_name_length > 16) {
            $msg .= '<p>Le nom de la session doit contenir entre 1 et 16 caractères.</p>';
            $erreur = true;
        }
    
        // Controle du nom de la session disponible dans la bdd
        if ( control_session_name($pdo, $session_name)){
            $msg .= '<p>Le nom de la session n\'est pas disponible</p><br>';
            $erreur = true;
        }
    
        if ($session_type == 0) {
            // session public
    
            // Ajoute de la nouvelle session dans la table 'session_list'
            if ($erreur == false){
                create_session($pdo, $session_name, $session_type, $session_user_id, NULL, $session_update);
                $msg .= '<p>La session "' . $session_name . '" a été créé.</p>';
            }
    
        } elseif ($session_type == 1) {
            // session private
    
            if (isset($_POST['session_pass'])) {
                $session_pass = trim($_POST['session_pass']);
                $session_pass = htmlspecialchars($session_pass, ENT_QUOTES, 'UTF-8');
    
                // Contrôle de la taille de session_pass : minimum 4 
                $session_pass_length = iconv_strlen($session_pass);
                if ($session_pass_length < 4) {
                    $msg .= '<p>Le mot de passe doit contenir au minimum 4 caractère.</p>';
                    $erreur = true;
                }
    
                // hashage du mot de passe de la session
                $session_pass_hash = password_hash($session_pass, PASSWORD_DEFAULT);
    
                // Ajoute de la nouvelle session dans la table 'session_list'
                if ($erreur == false){
                    create_session($pdo, $session_name, $session_type, $session_user_id, $session_pass_hash, $session_update);
                    $msg .= '<p>La session "' . $session_name . '" a été créé.</p>';
                };
            };
        }; // end elseif
    }; // end Créer une session
};

// Appel de la vue
include '../views/admin_dash.php';