<?php
// Model admin_dash.php

include '../inc/init.php';

function admin_info($pdo, $user_id){
    $admin_info = $pdo->prepare("SELECT * FROM user_list WHERE user_id = :user_id");
    $admin_info->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $admin_info->execute();

    if($admin_info->rowCount() > 0){
        return $admin_info->fetch(PDO::FETCH_ASSOC);
    }
    else{
        return false;
    };
}



function all_user_list($pdo){
    $all_user_list = $pdo->query("SELECT * FROM user_list");

    if($all_user_list->rowCount() > 0){
        return $all_user_list->fetchAll(PDO::FETCH_ASSOC);
    }
    else{
        return false;
    };
}

function all_session_list($pdo){
    $all_session_list = $pdo->query("SELECT * FROM session_list");

    if($all_session_list->rowCount() > 0){
        return $all_session_list->fetchAll(PDO::FETCH_ASSOC);
    }
    else{
        return false;
    };
}

function delete_user($pdo, $user_id){
    $delete_user = $pdo->prepare("DELETE FROM user_list WHERE user_id = :user_id");
    $delete_user->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $delete_user->execute();

    if($delete_user->rowCount() > 0) {
        return true;
    } 
    else {
        return false;
    };
};

function verif_mail($pdo, $user_mail){
    $verif_mail = $pdo->prepare("SELECT * FROM user_list WHERE user_mail = :user_mail");
    $verif_mail->bindParam(':user_mail', $user_mail, PDO::PARAM_STR);
    $verif_mail->execute();

    if($verif_mail->rowCount() > 0){
        return true;
    }
    else{
        return false;
    };
};

function create_user($pdo, $user_pseudo, $user_mail, $user_password, $user_date, $user_statut){
    $enregistrement = $pdo->prepare(
        "INSERT INTO user_list (user_pseudo, user_mail, user_password, user_date, user_statut) 
        VALUES (:user_pseudo, :user_mail, :user_password, :user_date, :user_statut)" // user_statut 0 = attente de validation mail
    );
    $enregistrement->bindParam(':user_pseudo', $user_pseudo, PDO::PARAM_STR);
    $enregistrement->bindParam(':user_mail', $user_mail, PDO::PARAM_STR);
    $enregistrement->bindParam(':user_password', $user_password, PDO::PARAM_STR);
    $enregistrement->bindParam(':user_date', $user_date, PDO::PARAM_STR);
    $enregistrement->bindParam(':user_statut', $user_statut, PDO::PARAM_STR);
    $enregistrement->execute();

    if ($enregistrement->rowCount() > 0){
        return true;
    }
    else {
        return false;
    };
};

function delete_session($pdo, $session_id){
    $delete_session = $pdo->prepare("DELETE FROM session_list WHERE session_id = :session_id");
    $delete_session->bindParam(':session_id', $session_id, PDO::PARAM_STR);
    $delete_session->execute();

    if($delete_session->rowCount() > 0) {
        return true;
    } 
    else {
        return false;
    };
}

function control_session_name($pdo, $session_name){

    $control_session_name = $pdo->prepare("SELECT session_name FROM session_list WHERE session_name = :session_name");
    $control_session_name->bindParam(':session_name', $session_name, PDO::PARAM_STR);
    $control_session_name->execute();

    if($control_session_name->rowCount() > 0){
        return true;
    }
    else{
        return false;
    }
}

function create_session($pdo, $session_name, $session_type, $session_user_id, $session_pass, $session_update){
    
    // Ajoute de la nouvelle session dans la table 'session_list'
    $create_session = $pdo->prepare(
        'INSERT INTO session_list
        (session_name, session_type, session_user_id, session_pass, session_update)
        VALUES 
        (:session_name, :session_type, :session_user_id, :session_pass, :session_update )'
    );

    $create_session->bindParam(':session_name', $session_name, PDO::PARAM_STR);
    $create_session->bindParam(':session_type', $session_type, PDO::PARAM_STR);
    $create_session->bindParam(':session_user_id', $session_user_id, PDO::PARAM_STR);
    $create_session->bindParam(':session_pass', $session_pass, PDO::PARAM_STR);
    $create_session->bindParam(':session_update', $session_update, PDO::PARAM_STR);
    $create_session->execute();

    if($create_session->rowCount() > 0){
        return true;
    }
    else{
        return false;
    }
};