<?php
// controller accueil.php

// Création || Reprise de la session existante
session_start();

// Appel du model
include './apps/models/accueil.php';

// Appel des fonctions utiles
include './apps/assets/functions/user_is_connected.php';

// ------------------------------------------------------------------------------------
// Création d'un utilisateur
// ------------------------------------------------------------------------------------

// Réception de la super-globale $_POST
if (isset($_POST['new_user_pseudo']) && 
    isset($_POST['new_user_mail']) && 
    isset($_POST['new_user_password']) &&
    isset($_POST['new_user_password_conf']) &&
    isset($_POST['question_captcha'])
) 
{
    // Controle et assignation des variables
    $user_pseudo = trim($_POST['new_user_pseudo']);
    $user_pseudo = htmlspecialchars($user_pseudo, ENT_QUOTES, 'UTF-8');
    $user_mail = trim($_POST['new_user_mail']);
    $user_password = trim($_POST['new_user_password']);
    $user_password_conf = trim($_POST['new_user_password_conf']);
    $question_captcha = trim($_POST['question_captcha']);

    // Variable de controle 
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

    // Contrôle des caractères autorisé dans l'adresse mail
    // Caractères autorisés : "a-z A-Z 0-9 . _ -" @ "a-z A-Z 0-9" . "a-z A-Z" limite 2-5 caractères
    if (preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/ ", $user_mail) == false){
        $msg .= '<p>Le format de l\'email est invalides.</p>';
        $erreur = true;
    }

    // Disponibilité du mail
    if (verif_mail($pdo, $user_mail)) {
        $msg .= '<p>Cette adresse mail est déjà associée à un compte.</p>';
        $erreur = true;
    };

    // Contrôle de la taille du mot de passe : aux moins 6 caractères
    $password_length = iconv_strlen($user_password);
    if ($password_length < 6) {
        $msg .= '<p>Le mot de passe doit contenir au moins 6 caractères.</p>';
        $erreur = true;
    };

    // Confirmation du mot de passe
    if ($user_password_conf !== $user_password){
        $msg .= '<p>La confirmation du mot de passe est invalide.</p>';
        $erreur = true;
    };

    // Vérification du captcha
    // On convertie la reponse avec strtolower afin d'eviter la "casse" (Blanc || BLANC || blanc) = ok
    if (strtolower($question_captcha) !== 'blanc') {
        $msg .= '<p>Mauvaise réponse du captcha anti-robot🤖 </p>';
        $erreur = true;
    }

    // Enregistrement du compte utilisateur
    if($erreur == false) {

        // hachage du mdp
        $user_password = password_hash($user_password, PASSWORD_DEFAULT);

        // Création du token unique de l'utilisateur
        $user_token = bin2hex(random_bytes(12));

        // Ajoute du compte utilisateur dans la bdd (en attente de validation du mail)
        // Dans le même temps on récupère le user_id
        $user_id = save_user($pdo, $user_pseudo, $user_mail, $user_password, $user_token);

        // date heure du serveur
        $now = date("Y-m-d H:i:s");

        $mail_info = array(
            'user_mail' => $user_mail,
            'mail_subject' => 'Validation e-mail',
            'mail_body' => 'Cliquez sur le lien pour confirmer votre e-mail.<br>
            <a href="http://localhost/projet/post_it_local/v1_5/index.php?action=validation&date='. $now . '&user_id=' . $user_id . '&token=' . $user_token . '">Valider</a>'
        );
        
        // Mail de validation
        if (send_mail($mail_info)){
            $msg .= '<p>Un e-mail de validation a été envoyé.</p>';
        };
    };
};

// ------------------------------------------------------------------------------------
// Réception de la validation du mail utilisateur
// ------------------------------------------------------------------------------------

if (isset($_GET['action']) && isset($_GET['date']) && isset($_GET['user_id']) && isset($_GET['token'])){

    $action = $_GET['action'];

    if ($action == 'validation'){
        $token_date = $_GET['date'];
        $user_id = $_GET['user_id'];
        $mail_token = $_GET['token'];
    
        // Date du serveur
        $now = date("Y-m-d H:i:s");
    
        // Date contenu dans le mail de validation + 10 minutes
        $expiry_time = date("Y-m-d H:i:s", strtotime("+10 minutes", strtotime($token_date)));
    
        // Si "expiry_time" est inferieur a "now"
        if ($expiry_time > $now){
            // La vérification du token est effectuée dans la fonction user_mail_validation()
            if (user_mail_validation($pdo, $user_id, $mail_token)){
                $msg .= '<p>Adresse e-mail validé.<br>Vous pouvez fermer cette fenêtre</p>';
            }
        }
        else{
            // Insuffisant car nécessite une action de l'utilisateur
            $msg .= '<p>Ce lien est expiré.<br>Veuillez recréer un compte.</p>';
            delete_user($pdo, $user_id);
        };
    };
};

// ------------------------------------------------------------------------------------
// Connexion utilisateur
// ------------------------------------------------------------------------------------

if (isset($_POST['user_mail']) && isset($_POST['user_password'])) {
    $user_mail = trim($_POST['user_mail']);
    $user_password = trim($_POST['user_password']);

    if (preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $user_mail)) {
        // Adresse email valide

        // recherche des info de la session dans la BDD
        if ($user_info = request_user_info($pdo, $user_mail)){
            // La session a été trouvé les info sont disponible dans $session_info
    
            // Controle du statut de l'utilisateur (si statut = 0, l'utilisateur n'a pas confirmé son adresse mail)
            if ($user_info['user_statut'] == 0){
                $msg .= '<p>Adresse e-mail non confirmé.</p>';
            }
            // elseif ($user_info['user_statut'] == 1) {
            elseif ($user_info['user_statut'] == 1 || $user_info['user_statut'] == 2) {
                // Controle du mot de passe
                if (password_verify($user_password, $user_info['user_password'])) {
        
                    // on enregistre les informations utilisateurs dans la session
                    $_SESSION['user'] = array();
                    $_SESSION['user']['user_id'] = $user_info['user_id'];
                    $_SESSION['user']['user_pseudo'] = $user_info['user_pseudo'];
                    $_SESSION['user']['user_mail'] = $user_info['user_mail'];
                    $_SESSION['user']['user_statut'] = $user_info['user_statut'];
        
                    // $msg .= '<p>Identification réussit.</p>';
        
                } else {
                    // erreur sur le mdp
                    $msg .= '<p>Erreur sur l\'email et/ou le mot de passe.</p>';
                };
            }
        }
        else{
            // erreur sur l'e-mail
            $msg .= '<p>Erreur sur l\'email et/ou le mot de passe.</p>';
        };
    }
    else {
        $msg .= '<p>Le format du mail n\'est pas valide</p>';
    };
};

// ------------------------------------------------------------------------------------
// Déconnexion utilisateur
// ------------------------------------------------------------------------------------

if( isset($_GET['action']) && $_GET['action'] == 'deconnexion') {
    session_destroy();
    header('location: ./');
};

// ------------------------------------------------------------------------------------
// Modification de mot de passe utilisateur
// ------------------------------------------------------------------------------------

// intérupteur des vue (identification / modification)
$change_pass = false;

if (isset($_POST['mail_lost_pass']) && isset($_POST['pass_lost_pseudo'])){
    $user_mail = trim($_POST['mail_lost_pass']);
    $user_pseudo = trim($_POST['pass_lost_pseudo']);
    $user_pseudo = htmlspecialchars($user_pseudo, ENT_QUOTES, 'UTF-8');

    // variable de controle 
    $erreur = false;

    // Contrôle du mail vide
    if ($user_mail == ""){
        $msg .= '<p>L\'adresse e-mail est vide.</p>';
        $erreur = true;
    }

    if (preg_match(" /^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/ ", $user_mail) == false){
        // Contrôle des caractères autorisé dans l'adresse mail
        // Caractères autorisés : "a-z A-Z 0-9 . _ -" @ "a-z A-Z 0-9" . "a-z A-Z" limite 2-5 caractères
        $msg .= '<p>Le format de l\'email est invalides.</p>';
        $erreur = true;
    }


    if ($erreur == false){
        // controle des données dans la base
        if ($user_id = verif_user($pdo, $user_mail, $user_pseudo)) {

            // Création du token unique de l'utilisateur
            $user_token = bin2hex(random_bytes(12));

            // Ajoute du token dans la bdd
            add_token($pdo, $user_id, $user_token);

            // date heure du serveur
            $now = date("Y-m-d H:i:s");

            $mail_info = array(
                'user_mail' => $user_mail,
                'mail_subject' => 'Mot de passe oublié',
                'mail_body' => 'Cliquez sur le lien pour modifier votre mot de passe.<br>
                <a href="http://localhost/projet/post_it_local/v1_5/index.php?action=pass_lost&date='. $now . '&user_id=' . $user_id . '&token=' . $user_token . '">Valider</a>'
            );
            // Mail de validation
            if (send_mail($mail_info)){
                $msg .= '<p>Un e-mail a été envoyé a cette adresse.</p>';
            };
        }
        else{
            $msg .= '<p>Adresse e-mail ou pseudo non valide.</p>';
        };
    };
};

// Réception du mail modification de mot de passe utilisateur
if (isset($_GET['action']) && isset($_GET['date']) && isset($_GET['user_id']) && isset($_GET['token'])){

    $action = $_GET['action'];

    if ($action == 'pass_lost'){
        $token_date = $_GET['date'];
        $user_id = $_GET['user_id'];
        $mail_token = $_GET['token'];
    
        // Date du serveur
        $now = date("Y-m-d H:i:s");
    
        // Date contenu dans le mail de validation + 10 minutes
        $expiry_time = date("Y-m-d H:i:s", strtotime("+10 minutes", strtotime($token_date)));
    
        // Si "expiry_time" est inferieur a "now"
        if ($expiry_time > $now){
            if (token_is_valid($pdo, $user_id, $mail_token)){
                $change_pass = true;
            };
        }
        else{
            $msg .= '<p>Ce lien est expiré.</p>';
        };
    }
};

if (isset($_POST['change_user_password']) && isset($_POST['change_user_password_conf'])){

    $user_password = trim($_POST['change_user_password']);
    $user_password_conf = trim($_POST['change_user_password_conf']);
    $user_id = $_POST['user_id'];

    $erreur = false;

    // le mot de passe doit avoir au moins 6 caractères
    $password_length = iconv_strlen($user_password);
    if ($password_length < 6) {
        $msg .= '<p>Le mot de passe doit contenir au moins 6 caractères.</p>';
        $erreur = true;
    };

    if ($user_password_conf !== $user_password){
        $msg .= '<p>La confirmation du mot de passe est invalide.</p>';
        $erreur = true;
    };

    if($erreur == false) {
        // hachage du mdp
        $user_password = password_hash($user_password, PASSWORD_DEFAULT);
        if (change_user_pass($pdo, $user_id, $user_password)){
            $msg .= '<p>Votre mot de passe a été modifié avec succès.<br>Vous pouvez fermer cette fenêtre</p>';
        };
    };
};

// ------------------------------------------------------------------------------------
// Création d'une session
// ------------------------------------------------------------------------------------

// Reception de la superglobal $_POST
if (isset($_POST['session_name']) && isset($_POST['session_type'])) {

    // Controle et assignation des variables
    $session_name = trim($_POST['session_name']);
    $session_name = htmlspecialchars($session_name, ENT_QUOTES, 'UTF-8');
    $session_type = $_POST['session_type'];

    $session_user_id = $_SESSION['user']['user_id'];

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
        $msg .= '<p>Le nom de la session n\'est pas disponible</p>';
        $erreur = true;
    }

    if ($session_type == 0) {
        // session public

        // Ajoute de la nouvelle session dans la table 'session_list'
        if ($erreur == false){
            create_session($pdo, $session_name, $session_type, $session_user_id, NULL);

            // ATTENTION $_POST DE REDIRECTION AUTOMATIQUE
            // Cette page envoie une $_POST qui sera 
            // réceptioné par load_session.php
            // load_session.php donnera ces droit a l'utilisateur 
            // Puis load_session.php redirige vers session.php
            $_POST['load_session_name'] = $session_name;
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
                create_session($pdo, $session_name, $session_type, $session_user_id, $session_pass_hash);
                
                // ATTENTION $_POST DE REDIRECTION AUTOMATIQUE
                // Cette page envoie une $_POST qui sera 
                // réceptionné par load_session.php
                // load_session.php donnera ces droits à l'utilisateur 
                // Puis load_session.php redirige vers session.php
                $_POST['load_session_name'] = $session_name;
                $_POST['load_session_pass'] = $session_pass;
            };
        };
    };
};

// ------------------------------------------------------------------------------------
// Chargement d'une session
// ------------------------------------------------------------------------------------

// Attention La super-globale $_POST['load_session_name'] peut venir de deux directions
// 1 - controllers/create_session.php
// 2 - controllers/load_session.php

$session_info = "";

// variable des informations de l'utilisateur
$user_info = "";

// controle de l'existance de la session
if (isset($_POST['load_session_name'])){
    $load_session_name = trim($_POST['load_session_name']);
    $load_session_name = htmlspecialchars($load_session_name, ENT_QUOTES, 'UTF-8');

    // recherche des informations de la session dans la BDD
    if ($session_info = session_info($pdo, $load_session_name)){
        // La session a été trouvé les info sont disponible dans $session_info

        // Appel des informations de l'utilisateur
        $user_info = user_info($pdo, $_SESSION['user']['user_id'], $session_info['session_id']);

        // Controle du role de l'utilisateur
        if ($session_info['session_user_id'] == $user_info['user_id']){
            // L'utilisateur est l'admin de la session
            // assignation du role admin (rule => 1)

            $add_right = array(
                'session_id'    => $session_info['session_id'],
                'user_id'       => $user_info['user_id'],
                'user_rule'     => 1,
                'user_acces'    => 1,
                'create_post'   => 1,
                'edit_post'     => 1,
                'delete_post'   => 1,
                'valide_post'   => 1
            );
            add_right($pdo, $add_right);

            // Vérification de la presence de valeur dans la table user_option
            if (control_user_option($pdo, $user_info['user_id'], $session_info['session_id']) == false){
                // L'utilisateur effectue sa 1er connexion, on applique les filtres des mémos par défaut
                $user_option_data = array(
                    'session_id'    => $session_info['session_id'],
                    'user_id'       => $user_info['user_id'],
                    'memo_simple' => '1', 
                    'memo_valide' => '1', 
                    'memo_archive'=> '0', 
                    'display_cat' => '1'
                );
                change_user_option($pdo, $user_option_data);
            };
            header('location: ./apps/controllers/session.php?name='. $session_info['session_name'] .'');
        }
        else{
            // L'utilisateur est un simple membre
            // assignation du role membre (rule => 0)

            $add_right = array(
                'session_id'    => $session_info['session_id'],
                'user_id'       => $_SESSION['user']['user_id'],
                'user_rule'     => 0
            );
            add_right($pdo, $add_right);
        };


        // controle de type de session
        if ($session_info['session_type'] == 0){
            // la session est public

            // assignation des accès
            $add_right = array(
                'session_id'    => $session_info['session_id'],
                'user_id'       => $_SESSION['user']['user_id'],
                'user_acces'    => 1
            );
            add_right($pdo, $add_right);
            
            // Vérification de la presence de valeur dans la table user_option
            if (control_user_option($pdo, $user_info['user_id'], $session_info['session_id']) == false){
                // L'utilisateur effectue sa 1er connexion, on applique les filtres des mémos par défaut
                $user_option_data = array(
                    'session_id'    => $session_info['session_id'],
                    'user_id'       => $user_info['user_id'],
                    'memo_simple' => '1', 
                    'memo_valide' => '1', 
                    'memo_archive'=> '0', 
                    'display_cat' => '1'
                );
                change_user_option($pdo, $user_option_data);
            };

            // redirection $_GET vers session.php
            header('location: ./apps/controllers/session.php?name='. $session_info['session_name'] .'');
        }
        else{
            // la session est privée

            if (! is_string($session_info['session_pass']) && hash_equals($session_info['session_pass'], $user_info['session_pass'])){
                // L'utilisateur a deja effectué une connexion valide
                // Le mot de passe stoqué n'a pas changé
                header('location: ./apps/controllers/session.php?name='. $session_info['session_name'] .'');
            }
            elseif (isset($_POST['load_session_pass'])){
                // on verifie le mot de passe
                $load_session_pass = trim($_POST['load_session_pass']);
                $load_session_pass = htmlspecialchars($load_session_pass, ENT_QUOTES, 'UTF-8');

                if (password_verify($load_session_pass, $session_info['session_pass'])) {
                    // L'utilisateur est un membre et a saisie un mot de passe valide

                    // assignation des accès
                    $add_right = array(
                        'session_id'    => $session_info['session_id'], 
                        'session_pass'  => $session_info['session_pass'], 
                        'user_id'       => $_SESSION['user']['user_id'], 
                        'user_acces'    => 1
                    );
                    add_right($pdo, $add_right);

                    // Ici on stock le mot de passe (hash) dans la BDD, 
                    // cela permet a l'utilisateur de saisir le mot de passe une premiere fois,
                    // et de ne pas avoir a le re-saisir si il quite la session.
                    // L'utilisateur devra re-saisir le mot de passe si l'admin de la session
                    // modifie le mot de passe

                    // Vérification de la presence de valeur dans la table user_option
                    if (control_user_option($pdo, $user_info['user_id'], $session_info['session_id']) == false){
                        // L'utilisateur effectue sa 1er connexion, on applique les filtres des mémos par défaut
                        $user_option_data = array(
                            'session_id'    => $session_info['session_id'],
                            'user_id'       => $user_info['user_id'],
                            'memo_simple' => '1', 
                            'memo_valide' => '1', 
                            'memo_archive'=> '0', 
                            'display_cat' => '1'
                        );
                        change_user_option($pdo, $user_option_data);
                    };

                    // redirection $_GET vers session.php
                    header('location: ./apps/controllers/session.php?name='. $session_info['session_name'] .'');
                }
                else {
                    // Mot de passe invalide
                    $msg .= '<p>Mot de passe invalide</p>';
                };
            }
            else {
                // Mot de passe vide
                $msg .= '<p>Mot de passe invalide</p>';
            };
        };
    }
    else{
        $msg .= '<p>La session n\'éxiste pas</p>';
    };
};



// ------------------------------------------------------------------------------------
// Modification du nom de la session
// ------------------------------------------------------------------------------------

if (isset($_POST['rename_session']) && isset($_POST['input_new_session_name'])){
    if ($_POST['input_new_session_name'] !== ""){
        $session_name = trim($_POST['rename_session']);
        $new_session_name = trim($_POST['input_new_session_name']);
        $new_session_name = htmlspecialchars($new_session_name, ENT_QUOTES, 'UTF-8');

        // variable de contrôle 
        $erreur = false;

        // Contrôle de la taille de session_name : entre 1 et 16
        $new_session_name_length = iconv_strlen($new_session_name);
        if ($new_session_name_length < 1 || $new_session_name_length > 16) {
            $msg .= '<p>Le nom de la session doit contenir entre 1 et 16 caractères.</p>';
            $erreur = true;
        };
        
        // Controle du nom de la session disponible dans la bdd
        if ( control_session_name($pdo, $new_session_name)){
            $msg .= '<p>Le nom de la session n\'est pas disponible</p>';
            $erreur = true;
        };

        if ($erreur == false){
            if (rename_session($pdo, $new_session_name, $session_name) == true){
                $msg .= '<p>Le nom de la session a été modifié</p>';
            };
        };
    };
};

// ------------------------------------------------------------------------------------
// Modification du mot de passe de la session
// ------------------------------------------------------------------------------------

if (isset($_POST['change_session_pass']) && isset($_POST['input_new_session_pass'])){

    $new_session_pass = trim($_POST['input_new_session_pass']);
    $new_session_pass = htmlspecialchars($new_session_pass, ENT_QUOTES, 'UTF-8');
    $session_name = $_POST['change_session_pass'];

    $erreur = false;
    $session_type = "";
    $session_pass_hash = "";

    if ($new_session_pass == ""){
        // Le mot de passe est vide la session devient public
        $session_type = "0";
        echo 'public';
    }
    else{
        // Un mot de passe a été saisie la session devient privé
        $session_type = "1";
        echo 'privé';

        // Contrôle de la taille de session_pass : minimum 4 
        $new_session_pass_length = iconv_strlen($new_session_pass);
        if ($new_session_pass_length < 4) {
            $msg .= '<p>Le mot de passe doit contenir au minimum 4 caractère.</p>';
            $erreur = true;
        }
        else {
            // hashage du mot de passe de la session
            $session_pass_hash = password_hash($new_session_pass, PASSWORD_DEFAULT);
        };
    };
    
    if ($erreur == false){
        change_session_pass($pdo, $session_pass_hash, $session_name, $session_type);
        $msg .= '<p>Le mot de passe de la session a été modifié</p>';
    };
};

// ------------------------------------------------------------------------------------
// Suppresion de la session (Mes sessions)
// ------------------------------------------------------------------------------------

if (isset($_POST['delete_session'])){
    if (delete_session($pdo, $_POST['delete_session']) == true){
        $msg .= '<p>La session '. $_POST['delete_session'] .' a été supprimé</p>';
    };
};

// ------------------------------------------------------------------------------------
// Quitter la session (Sessions rejoites)
// ------------------------------------------------------------------------------------

if (isset($_POST['remove_session'])){
    $session_info = session_info($pdo, $_POST['remove_session']);
    $user_info = request_user_info($pdo, $_SESSION['user']['user_mail']);

    echo $user_info['user_id'];
    echo $session_info['session_id'];

    if (remove_session($pdo, $user_info['user_id'], $session_info['session_id']) == true){
        $msg .= '<p>Vous quitter la session '. $_POST['remove_session'] .'</p>';
    };
};


// ------------------------------------------------------------------------------------
// Affichage des listes des sessions (si utilisateur connecté)
// ------------------------------------------------------------------------------------
// !!! Attention le code "Modification du nom de la session" et 
// "Modification du mot de passe de la session" DOIT ce trouver au DESSUS du code "Affichage des listes des sessions"

if (user_is_connected()){
    // On liste les sessions crées par l'utilisateur
    $user_session_admin = user_session_admin($pdo, $_SESSION['user']['user_id']);
    // On liste les sessions rejointes par l'utilisateur
    $user_session_member = user_session_member($pdo, $_SESSION['user']['user_id']);
};

// ------------------------------------------------------------------------------------
// Accès refusé
// ------------------------------------------------------------------------------------
if (isset($_GET['acces'])){
    if ($_GET['acces'] == "false"){
        $msg .= "<p>L'administrateur vous a banni de la session.</p>";
    }
    elseif ($_GET['acces'] == "mdp")
    $msg .= "<p>L'administrateur de la session a changé le mot de passe.</p>";
}

// Appel de la vue
include './apps/views/accueil.php';