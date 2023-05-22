<?php
// controller session.php

// on démarre la superglobale $_SESSION
session_start();

// Appel du model
include '../models/session.php';

// Appel des fonctions utils
include '../assets/functions/user_is_connected.php';
include '../assets/functions/user_have_acces.php';
include '../assets/functions/user_have_right.php';

// Variable de contrôle des erreurs de la session
$erreur = false;

// Variable des informations de la session
$session_info = '';

// Variable des informations de chacun des "membres" de la session
$member_info = '';

// Variable des informations de utilisateur
$user_info = '';

// Variable des droits de utilisateur
$user_right = '';

// Variable de contrôle des erreurs du formulaire
$form_error = false;

// ------------------------------------------------------------------------------------
// Contrôle des paramètres de le session et de l'utilisateur
// ------------------------------------------------------------------------------------

// Réception de la superglobale $_GET
if ( ! empty(isset($_GET['name']))){
    $session_name = $_GET['name'];

    // Appel des informations de la session
    if ($session_info = request_session_info($pdo, $session_name)){
        // $session_info contient les info de la session
        // echo '<pre>';
        // echo 'session_info<br>';
        // print_r($session_info);
        // echo '</pre>';
    }
    else {
        $msg .= '<p>Le nom de la session est invalide</p>';
        $erreur = true;
    };

    // Appel des informations des membres de la session 
    if ($member_info = member_info($pdo, $session_info['session_id'])){
        // $member_info contient le pseudo et les droits de chacun des membres de la session 
        // echo '<pre>';
        // echo 'member_info<br>';
        // print_r($member_info);
        // echo '</pre>';
    }
    else {
        $member_info = '<p>Aucun membre n\'a rejoint cette session</p>';
    };
}
else {
    $msg .= '<p>Le nom de la session est vide</p>';
    $erreur = true;
};

if ( ! user_is_connected()){
    // L'utilisateur n'est pas connecté

    // Redirection vers l'index
    header('location: ../../');
}
else {
    // Appel des informations de l'utilisateur
    $user_info = user_info($pdo, $_SESSION['user']['user_id'], $session_info['session_id']);
    // debug
    // echo '<pre>';
    // print_r($user_info);
    // echo '</pre>';
};

// !!!!!!!!!!!   PROBLEME    !!!!!!!!!!!
// Si le nom de la session n'existe pas la fonction user_have_acces() redirige l'user AVANT d'afficher le msg "La session n'existe pas"
// Cela se traduit par un msg d'erreur "Kiké par l'admin".
// La solution est de remonter ce code dans la 1er condition (si la session existe {si l'user a l'acces{ etc ... }})
// !!!!!!!!!!!   PROBLEME    !!!!!!!!!!!

if ($user_acces = user_have_acces($session_info, $user_info)){
    // L'utilisateur n'a pas accès
    // Redirection vers l'index

    // echo $user_acces;
    if ($user_acces === "password_false"){
        header('location: ../../index.php?acces=mdp');
    }
    if ($user_acces === "acces_false"){
        header('location: ../../index.php?acces=false');
    }
};

// ------------------------------------------------------------------------------------
// Création d'un nouveau Mémo
// ------------------------------------------------------------------------------------

if (isset($_POST['create_post'])){

    $post_title = trim($_POST['post_title']);
    $post_title = htmlspecialchars($post_title, ENT_QUOTES, 'UTF-8');

    $post_mesage = trim($_POST['post_mesage']);
    $post_mesage = htmlspecialchars($post_mesage, ENT_QUOTES, 'UTF-8');

    // Insère un retour à la ligne HTML à chaque nouvelle ligne
    $post_mesage = nl2br($post_mesage);

    $length_post_title = iconv_strlen($post_title);
    $length_post_mesage = iconv_strlen($post_mesage);

    $post_color = $_POST['post_color'];

    // Controle de la taille du titre
    if ( $length_post_title < 1 || $length_post_title > 16){
        $form_error = true;
        $msg .= '<p>Le titre doit comporter entre 1 et 16 caractères</p>';
    };

    // Controle de la taille du message
    if ( $length_post_mesage < 1 || $length_post_mesage > 250){
        $form_error = true;
        $msg .= '<p>Le text doit comporter entre 1 et 250 caractères</p>';
    };

    if ($form_error == false){

        $new_post = array(
            'session_id' => $session_info['session_id'],
            'user_id' => $user_info['user_id'],
            'post_title' => $post_title,
            'post_mesage' => $post_mesage,
            'post_color' => $post_color
        );

        if (add_post($pdo, $new_post)){
            $msg .= '<p>Nouveau Mémo "'. $post_title . '" enregistré</p>';
        };
    };
};

// ------------------------------------------------------------------------------------
// Supprimer un Mémo
// ------------------------------------------------------------------------------------

if (isset($_POST['delete_post'])){
    $post_id = $_POST['delete_post'];

    if (delete_post($pdo, $post_id)){
        $msg .= 'Le Mémo a été suprimé';
    };
}

// ------------------------------------------------------------------------------------
// Supprimer tous les Mémo
// ------------------------------------------------------------------------------------

if (isset($_POST['delete_all_post'])){
    $post_id = $_POST['delete_all_post'];

    if (delete_all_post($pdo, $session_info['session_id'])){
        $msg .= 'Tous les Mémo ont été supprimés';
    };
}

// ------------------------------------------------------------------------------------
// Editer un Mémo
// ------------------------------------------------------------------------------------

if (isset($_POST['edit_post'])){
    $post_id = $_POST['edit_post'];
    $post_title = trim($_POST['post_title']);
    $post_title = htmlspecialchars($post_title);

    $post_mesage = trim($_POST['post_mesage']);
    $post_mesage = htmlspecialchars($post_mesage);
    $post_mesage = nl2br($post_mesage);

    $length_post_title = iconv_strlen($post_title);
    $length_post_mesage = iconv_strlen($post_mesage);

    $post_color = $_POST['post_color'];

    // Controle de la taille du titre
    if ( $length_post_title < 1 || $length_post_title > 16){
        $form_error = true;
        $msg .= '<p>Le titre doit comporter entre 1 et 16 caractères</p>';
    };

    // Controle de la taille du message
    if ( $length_post_mesage < 1 || $length_post_mesage > 250){
        $form_error = true;
        $msg .= '<p>Le text doit comporter entre 1 et 250 caractères</p>';
    };
    
    if ($form_error == false){
    
        $edit_post = array(
            'post_id' => $post_id,
            'post_title' => $post_title,
            'post_mesage' => $post_mesage,
            'post_color' => $post_color
        );
    
        if (update_post($pdo, $edit_post)){
            $msg .= '<p>Mémo "' . $post_title . '" modifié</p>';
        };
    };
};

// ------------------------------------------------------------------------------------
// Gestion des droits des membres
// ------------------------------------------------------------------------------------

if (isset($_POST['update_user_right'])) {

    // Variable de déclanchement de l'actualisation de la page
    $reload = false; 

    // On initialise les tableaux de droits pour chaque utilisateur
    $create_right = array();
    $edit_right = array();
    $delete_right = array();
    $valide_right = array();

    // Pour chaque utilisateur, on ajoute un tableau de droits pour les cases cochées
    foreach ($member_info as $row) {

        $user_id = $row['user_id'];
        // Si la super-globale $_POST['...'] existe la checkbox est coché (1), sinon elle est décoché (0)
        if (isset($_POST['create_right'][$user_id])){ $create_right[$user_id] = 1; } else { $create_right[$user_id] = 0; }
        if (isset($_POST['edit_right'][$user_id])){ $edit_right[$user_id] = 1; } else { $edit_right[$user_id] = 0; }
        if (isset($_POST['delete_right'][$user_id])){ $delete_right[$user_id] = 1; } else { $delete_right[$user_id] = 0; }
        if (isset($_POST['valide_right'][$user_id])){ $valide_right[$user_id] = 1; } else { $valide_right[$user_id] = 0; }
    }

    for ($i = 0; $i < count($member_info); $i++){

        // Comparaison des valeurs 'create_post' du formulaire et de la base de donnée
        if ($member_info[$i]['create_post'] !== $create_right[$member_info[$i]['user_id']]){
            // Si les valeurs sont differentes on modifie les données de la base

            // echo $i.' - create_post => user_id => '. $member_info[$i]['user_id'] .' => right_value => '. $create_right[$member_info[$i]['user_id']] .'<br>';
            $right_update_info = array(
                'session_id' => $session_info['session_id'],
                'user_id' => $member_info[$i]['user_id'], 
                'right_post' => 'create_post', 
                'right_value' => $create_right[$member_info[$i]['user_id']]
            );
            if (user_right_update($pdo, $right_update_info)){
                $reload = true;
                $msg .= '<p>Droit des utilisateur mise a jour</p>';
            };
        };

        // Comparaison des valeurs 'edit_post' du formulaire et de la base de donnée
        if ($member_info[$i]['edit_post'] !== $edit_right[$member_info[$i]['user_id']]){
            // Si les valeurs sont differentes on modifie les données de la base

            // echo $i.' - edit_post => user_id => '. $member_info[$i]['user_id'] .' => right_value => '. $create_right[$member_info[$i]['user_id']];
            $right_update_info = array(
                'session_id' => $session_info['session_id'],
                'user_id' => $member_info[$i]['user_id'], 
                'right_post' => 'edit_post', 
                'right_value' => $edit_right[$member_info[$i]['user_id']]
            );
            if (user_right_update($pdo, $right_update_info)){
                $reload = true;
                $msg .= '<p>Droit des utilisateur mise a jour</p>';
            };
        };

        // Comparaison des valeurs 'delete_post' du formulaire et de la base de donnée
        if ($member_info[$i]['delete_post'] !== $delete_right[$member_info[$i]['user_id']]){
            // Si les valeurs sont differentes on modifie les données de la base

            $right_update_info = array(
                'session_id' => $session_info['session_id'],
                'user_id' => $member_info[$i]['user_id'], 
                'right_post' => 'delete_post', 
                'right_value' => $delete_right[$member_info[$i]['user_id']]
            );
            if (user_right_update($pdo, $right_update_info)){
                $reload = true;
                $msg .= '<p>Droit des utilisateur mise a jour</p>';
            };
        };

        // Comparaison des valeurs 'valide_post' du formulaire et de la base de donnée
        if ($member_info[$i]['valide_post'] !== $valide_right[$member_info[$i]['user_id']]){
            // Si les valeurs sont differentes on modifie les données de la base

            $right_update_info = array(
                'session_id' => $session_info['session_id'],
                'user_id' => $member_info[$i]['user_id'], 
                'right_post' => 'valide_post', 
                'right_value' => $valide_right[$member_info[$i]['user_id']]
            );
            if (user_right_update($pdo, $right_update_info)){
                $reload = true;
                $msg .= '<p>Droit des utilisateur mise a jour</p>';
            };
        };
    };

    if ($reload == true){
        // Actualisation de la page pour le rafraichissement des données
        // Prevoir une fonction asynchrone (v1.5)
        header('location: ./session.php?name='. $session_info['session_name'] .'');
    };
};

// ------------------------------------------------------------------------------------
// Changement du mot de passe de la session
// ------------------------------------------------------------------------------------

if (isset($_POST['new_session_pass'])){
    
    // Contrôle
    $new_session_pass = trim($_POST['new_session_pass']);
    $new_session_pass = htmlspecialchars($new_session_pass, ENT_QUOTES, 'UTF-8');

    // Contrôle de la taille de session_pass : minimum 4 
    $new_session_pass_length = iconv_strlen($new_session_pass);
    if ($new_session_pass_length < 4) {
        $msg .= '<p>Le mot de passe doit contenir au minimum 4 caractère.</p>';
    }
    else {
        // hashage du mot de passe de la session
        $new_session_pass_hash = password_hash($new_session_pass, PASSWORD_DEFAULT);
        // Mise a jour du mot de passe dans la base de donnée
        if (update_session_pass($pdo, $session_info['session_id'], $new_session_pass_hash)){
            $msg .= '<p>Le mot de passe a été modifié avec succès.</p>';
        };
    };
};

// ------------------------------------------------------------------------------------
// Chargement des données des "filtres d'affichage" et du "chat"
// ------------------------------------------------------------------------------------

// Appel des informations des options de l'utilisateur
if ($user_option = control_user_option($pdo, $user_info['user_id'], $session_info['session_id'])){

    // Les cases sont "checked" si leurs valeur est "1" dans la BDD
    if ($user_option['memo_simple'] == 1){
        $checked_filter_simple = "checked";
    }
    else {
        $checked_filter_simple = "";
    }
    if ($user_option['memo_valide'] == 1){
        $checked_filter_valide = "checked";
    }
    else {
        $checked_filter_valide = "";
    }
    if ($user_option['memo_archive'] == 1){
        $checked_filter_archive = "checked";
    }
    else {
        $checked_filter_archive = "";
    }

    // Le chat est affiché si sa valeur est "1" dans la BDD
    if ($user_option['display_cat'] == 1){
        $btn_cat_display = "Sortir le chat";
    }
    else {
        $btn_cat_display = "Rentrer le chat";
    }
}
// else {
//     // L'utilisateur effectue sa 1er connexion à la session
//     option_default($pdo, $user_info['user_id'], $session_info['session_id']);

// }



// ------------------------------------------------------------------------------------
// Vérification de la presence du backgroung de l'utilisateur
// ------------------------------------------------------------------------------------

// Contient le chemin le nom et l'extension du background
$user_bg = '';

// Extension d'image acceptée
$ext_array = ['.jpg', '.jpeg', '.gif', '.png', '.webp', '.bmp'];

// Chemin de l'image
$bg_src = "../assets/img/bg/" . $user_info['user_id'];

// Test de l'existence du fichier avec chaque extension
for ($i = 0; $i < count($ext_array); $i++){
    if (file_exists($bg_src . $ext_array[$i])){
        // l'image a été trouvée
        $user_bg = $bg_src . $ext_array[$i];

        // la variable contenant le chemin de l'image est envoyé dans le JS
        ?>
        <script type="text/javascript">
            let user_bg ='<?php echo $user_bg ;?>';
        </script>
        <?php
    }
}

// ------------------------------------------------------------------------------------
// Stokage du Background-image de l'utilisateur
// ------------------------------------------------------------------------------------

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

    // Si un fichier est déjà dans le dossier il est supprimé
    foreach ($ext_array as $ext){
        $file = '../assets/img/bg/'. $user_info['user_id'] . $ext . '';
        // Vérification de l'existence d'un précédent fichier
        if (file_exists($file)) {
            // Suppression du fichier
            unlink($file);
        };
    }

    // Vérification de l'extension du fichier
    // $allowed_extensions = array('jpg', 'jpeg', 'gif', 'png', 'webp', 'bmp');
    
    $file_extension = '.' . strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $ext_array)) {
        $msg .= "<p>Erreur : le fichier n'est pas une image.</p>";
    } 
    else {
        // Déplacement du fichier vers le dossier de destination
        $destination_folder = '../assets/img/bg/';
        $destination_file = $destination_folder . $user_info['user_id'] . $file_extension;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination_file)) {
            $msg .= "<p>L'image a été enregistrée avec succès.</p>";
        } 
        else {
            $msg .= "<p>Erreur lors de l'enregistrement de l'image.</p>";
        };
    };
};

// ------------------------------------------------------------------------------------
// Valider / Achiver un Mémo
// ------------------------------------------------------------------------------------

if (isset($_POST['btn_valid_post'])){
    $post_id = $_POST['btn_valid_post'];
    $statut_info = array(
        'post_id' => $_POST['btn_valid_post'],
        'post_statut' => 'valid'
    );
    if (statut_post($pdo, $statut_info)){
        $msg .= '<p>Mémo validé</p>';
    };
}

if (isset($_POST['btn_archive_post'])){
    $statut_info = array(
        'post_id' => $_POST['btn_archive_post'],
        'post_statut' => 'archive'
    );
    $post_id = $_POST['btn_archive_post'];
    if (statut_post($pdo, $statut_info)){
        $msg .= '<p>Mémo archivé</p>';
    };
}

// ------------------------------------------------------------------------------------
// Envoie du user_id et du session_id dans le JS
// ------------------------------------------------------------------------------------
?>
<script type="text/javascript">
let user_id ='<?php echo $_SESSION['user']['user_id'];?>';
let session_id ='<?php echo $session_info['session_id'];?>';
</script>
<?php

// ------------------------------------------------------------------------------------
// Appel des informations des Mémo
// ------------------------------------------------------------------------------------

if ($list_post = load_post($pdo, $session_info)){
    // Appel des informations pour l'affichage des Mémo de la session
    // echo '<pre>';
    // print_r($list_post);
    // echo '</pre>';
}


// Affichage de la vue
if ($erreur == false){
    include '../views/session.php';
} 
else {
    echo $msg;
}