<?php
// Model connexion_user.php

function request_user_info($pdo, $user_mail){

    $request = $pdo->prepare("SELECT * FROM `user_list` WHERE `user_mail` = :user_mail");
    $request->bindParam(':user_mail', $user_mail, PDO::PARAM_STR);
    $request->execute();
    
    if($request->rowCount() > 0){
        return $request->fetch(PDO::FETCH_ASSOC);
    }
    else{
        return false;
    };
}

function user_session_admin($pdo, $user_id){

    $user_session_admin = $pdo->prepare("SELECT session_name FROM session_list WHERE session_user_id = :user_id");
    $user_session_admin->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $user_session_admin->execute();

    if($user_session_admin->rowCount() > 0){
        return $user_session_admin->fetchAll(PDO::FETCH_ASSOC);
    }
    else{
        return false;
    };
}

function user_session_member($pdo, $user_id){
    $user_session_member = $pdo->prepare(
        "SELECT session_list.session_name
        FROM session_list
        JOIN user_right
        ON session_list.session_id = user_right.session_id
        JOIN user_list
        ON user_list.user_id = user_right.user_id
        WHERE user_list.user_id = :user_id
        AND user_right.user_acces = 1
        AND user_right.user_rule != 1"
    );
    $user_session_member->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $user_session_member->execute();

    if($user_session_member->rowCount() > 0){
        return $user_session_member->fetchAll(PDO::FETCH_ASSOC);
    }
    else{
        return false;
    };
};

// Model create_session.php

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

function create_session($pdo, $session_name, $session_type, $session_user_id, $session_pass){
    
    // Ajoute de la nouvelle session dans la table 'session_list'
    $add_session_list = $pdo->prepare(
        'INSERT INTO session_list
        (session_name, session_type, session_user_id, session_pass, session_update)
        VALUES 
        (:session_name, :session_type, :session_user_id, :session_pass, NOW() )'
    );

    $add_session_list->bindParam(':session_name', $session_name, PDO::PARAM_STR);
    $add_session_list->bindParam(':session_type', $session_type, PDO::PARAM_STR);
    $add_session_list->bindParam(':session_user_id', $session_user_id, PDO::PARAM_STR);
    $add_session_list->bindParam(':session_pass', $session_pass, PDO::PARAM_STR);
    $add_session_list->execute();
};

// Model create_user.php

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

function save_user($pdo, $user_pseudo, $user_mail, $user_password, $user_token){
    $enregistrement = $pdo->prepare(
        "INSERT INTO user_list (user_pseudo, user_mail, user_password, user_date, user_statut, user_token) 
        VALUES (:user_pseudo, :user_mail, :user_password, NOW(), 0, :user_token)" // user_statut 0 = attente de validation mail
    );
    $enregistrement->bindParam(':user_pseudo', $user_pseudo, PDO::PARAM_STR);
    $enregistrement->bindParam(':user_mail', $user_mail, PDO::PARAM_STR);
    $enregistrement->bindParam(':user_password', $user_password, PDO::PARAM_STR);
    $enregistrement->bindParam(':user_token', $user_token, PDO::PARAM_STR);
    $enregistrement->execute();


    // Récuperation du "user_id"
    $request_user_id = $pdo->prepare("SELECT user_id FROM user_list WHERE user_mail = :user_mail");
    $request_user_id->bindParam(':user_mail', $user_mail, PDO::PARAM_STR);
    $request_user_id->execute();
    $user_id = $request_user_id->fetch(PDO::FETCH_ASSOC);

    if ($enregistrement->rowCount() > 0){
        return $user_id['user_id'];
    }
    else {
        return false;
    };
};

function user_mail_validation($pdo, $user_id, $mail_token){

    $bdd_token_request = $pdo->prepare("SELECT user_token FROM user_list WHERE user_id = :user_id");
    $bdd_token_request->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $bdd_token_request->execute();
    $bdd_token = $bdd_token_request->fetch(PDO::FETCH_ASSOC);

    if (hash_equals($bdd_token['user_token'], $mail_token)) {
        $user_mail_validation = $pdo->prepare("UPDATE user_list SET user_statut = 1, user_token = NULL WHERE user_id = :user_id");
        $user_mail_validation->bindParam(':user_id', $user_id, PDO::PARAM_STR);
        $user_mail_validation->execute();

        if ($user_mail_validation->rowCount() > 0){
            return true;
        }
        else {
            return false;
        };
    }
    else {
        echo '<p>Token non valide</p>';
        return false;
    };
};

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

// model load_session.php

function session_info($pdo, $session_name){
    // Appel des informations de la session
    $session_info = $pdo->prepare(
        "SELECT session_id, session_name, session_type, session_user_id, session_update, session_pass
        FROM `session_list`
        WHERE `session_name` = :session_name");
    $session_info->bindParam(':session_name', $session_name, PDO::PARAM_STR);
    $session_info->execute();

    if($session_info->rowCount() > 0){
        return $session_info->fetch(PDO::FETCH_ASSOC);
    }
    else{
        return false;
    }
};

function user_info($pdo, $user_id, $session_id) {
    $user_info = $pdo->prepare(
        "SELECT 
        user_list.user_id, 
        user_list.user_pseudo, 
        user_list.user_mail, 
        user_list.user_password, 
        user_list.user_statut, 
        user_right.right_id, 
        user_right.session_pass, 
        user_right.user_rule, 
        user_right.user_acces, 
        user_right.create_post, 
        user_right.edit_post, 
        user_right.delete_post, 
        user_right.valide_post
        FROM user_list
        LEFT JOIN user_right
        ON user_list.user_id = user_right.user_id
        AND user_right.session_id = :session_id
        WHERE user_list.user_id = :user_id"
    );
    $user_info->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $user_info->bindParam(':session_id', $session_id, PDO::PARAM_INT);
    $user_info->execute();
    
    if ($user_info->rowCount() > 0) {
        return $user_info->fetch(PDO::FETCH_ASSOC);
    } else {
        return false;
    };
};

function add_right($pdo, $user_right){

    $user_info = user_info($pdo, $user_right['user_id'], $user_right['session_id']);

    // echo '<pre>';
    // echo '$user_right<br>';
    // print_r($user_right);
    // echo '</pre>';

    if( ! $user_info['right_id'] ) {
        // Si l'utilisateur n'a pas deja recu des droits pour cette session

        // user_rule
        if ( isset($user_right['user_rule']) ){
            $add_right = $pdo->prepare(
                'INSERT INTO user_right 
                (user_id, session_id, user_rule)
                VALUES (:user_id, :session_id, :user_rule)
                ' 
            );
            $add_right->bindParam(':user_rule', $user_right['user_rule'], PDO::PARAM_STR);
            $add_right->bindParam(':user_id', $user_right['user_id'], PDO::PARAM_STR);
            $add_right->bindParam(':session_id', $user_right['session_id'], PDO::PARAM_STR);
            $add_right->execute();
        }
    }

    // session_pass
    if ( isset($user_right['session_pass']) ){
        $add_right = $pdo->prepare(
            'UPDATE user_right 
            SET session_pass = :session_pass 
            WHERE user_id = :user_id 
            AND session_id = :session_id' 
        );
        $add_right->bindParam(':session_pass', $user_right['session_pass'], PDO::PARAM_STR);
        $add_right->bindParam(':user_id', $user_right['user_id'], PDO::PARAM_STR);
        $add_right->bindParam(':session_id', $user_right['session_id'], PDO::PARAM_STR);
        $add_right->execute();
    }

    // user_rule
    if ( isset($user_right['user_rule']) ){
        $add_right = $pdo->prepare(
            'UPDATE user_right 
            SET user_rule = :user_rule 
            WHERE user_id = :user_id 
            AND session_id = :session_id' 
        );
        $add_right->bindParam(':user_rule', $user_right['user_rule'], PDO::PARAM_STR);
        $add_right->bindParam(':user_id', $user_right['user_id'], PDO::PARAM_STR);
        $add_right->bindParam(':session_id', $user_right['session_id'], PDO::PARAM_STR);
        $add_right->execute();
    }
    
    // user_acces
    if ( isset($user_right['user_acces']) ){
        $add_right = $pdo->prepare(
            'UPDATE user_right 
            SET user_acces = :user_acces 
            WHERE user_id = :user_id 
            AND session_id = :session_id' 
        );
        $add_right->bindParam(':user_acces', $user_right['user_acces'], PDO::PARAM_STR);
        $add_right->bindParam(':user_id', $user_right['user_id'], PDO::PARAM_STR);
        $add_right->bindParam(':session_id', $user_right['session_id'], PDO::PARAM_STR);
        $add_right->execute();
    }

    // create_post
    if ( isset($user_right['create_post']) ){
        $add_right = $pdo->prepare(
            'UPDATE user_right 
            SET create_post = :create_post 
            WHERE user_id = :user_id 
            AND session_id = :session_id' 
        );
        $add_right->bindParam(':create_post', $user_right['create_post'], PDO::PARAM_STR);
        $add_right->bindParam(':user_id', $user_right['user_id'], PDO::PARAM_STR);
        $add_right->bindParam(':session_id', $user_right['session_id'], PDO::PARAM_STR);
        $add_right->execute();
    }

    // edit_post
    if ( isset($user_right['edit_post']) ){
        $add_right = $pdo->prepare(
            'UPDATE user_right 
            SET edit_post = :edit_post 
            WHERE user_id = :user_id 
            AND session_id = :session_id' 
        );
        $add_right->bindParam(':edit_post', $user_right['edit_post'], PDO::PARAM_STR);
        $add_right->bindParam(':user_id', $user_right['user_id'], PDO::PARAM_STR);
        $add_right->bindParam(':session_id', $user_right['session_id'], PDO::PARAM_STR);
        $add_right->execute();
    }

    // delete_post
    if ( isset($user_right['delete_post']) ){
        $add_right = $pdo->prepare(
            'UPDATE user_right 
            SET delete_post = :delete_post 
            WHERE user_id = :user_id 
            AND session_id = :session_id' 
        );
        $add_right->bindParam(':delete_post', $user_right['delete_post'], PDO::PARAM_STR);
        $add_right->bindParam(':user_id', $user_right['user_id'], PDO::PARAM_STR);
        $add_right->bindParam(':session_id', $user_right['session_id'], PDO::PARAM_STR);
        $add_right->execute();
    }

    // valide_post
    if ( isset($user_right['valide_post']) ){
        $add_right = $pdo->prepare(
            'UPDATE user_right 
            SET valide_post = :valide_post 
            WHERE user_id = :user_id 
            AND session_id = :session_id' 
        );
        $add_right->bindParam(':valide_post', $user_right['valide_post'], PDO::PARAM_STR);
        $add_right->bindParam(':user_id', $user_right['user_id'], PDO::PARAM_STR);
        $add_right->bindParam(':session_id', $user_right['session_id'], PDO::PARAM_STR);
        $add_right->execute();
    }

    if($add_right->rowCount() > 0){
        return true;
    }
    else{
        return false;
    };

};

function control_user_option($pdo, $user_id, $session_id){
    $control_user_option = $pdo->prepare(
        "SELECT memo_simple, memo_valide, memo_archive, display_cat 
        FROM user_option
        WHERE user_id = :user_id
        AND session_id = :session_id"
    );
    $control_user_option->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $control_user_option->bindParam(':session_id', $session_id, PDO::PARAM_STR);
    $control_user_option->execute();
    $user_option = $control_user_option->fetch(PDO::FETCH_ASSOC);

    if($control_user_option->rowCount() > 0){
        return $user_option;
    }
    else{
        return false;
    };
};

function change_user_option($pdo, $user_option_data){
    $change_user_option = $pdo->prepare(
        "INSERT INTO user_option (option_id, user_id, session_id, memo_simple, memo_valide, memo_archive, display_cat)
        VALUE (NULL, :user_id, :session_id, :memo_simple, :memo_valide, :memo_archive, :display_cat)"
    );
    $change_user_option->bindParam(':user_id', $user_option_data['user_id'], PDO::PARAM_STR);
    $change_user_option->bindParam(':session_id', $user_option_data['session_id'], PDO::PARAM_STR);
    $change_user_option->bindParam(':memo_simple', $user_option_data['memo_simple'], PDO::PARAM_STR);
    $change_user_option->bindParam(':memo_valide', $user_option_data['memo_valide'], PDO::PARAM_STR);
    $change_user_option->bindParam(':memo_archive', $user_option_data['memo_archive'], PDO::PARAM_STR);
    $change_user_option->bindParam(':display_cat', $user_option_data['display_cat'], PDO::PARAM_STR);
    $change_user_option->execute();
}

// model password_lost.php

function verif_user($pdo, $user_mail, $user_pseudo){

    $verif_user = $pdo->prepare("SELECT user_id FROM user_list WHERE user_mail = :user_mail AND user_pseudo = :user_pseudo");
    $verif_user->bindParam(':user_mail', $user_mail, PDO::PARAM_STR);
    $verif_user->bindParam(':user_pseudo', $user_pseudo, PDO::PARAM_STR);
    $verif_user->execute();
    $user_id = $verif_user->fetch(PDO::FETCH_ASSOC);

    if($verif_user->rowCount() > 0){
        return $user_id['user_id'];
    }
    else{
        return false;
    };
};

function add_token($pdo, $user_id, $user_token){
    $add_token = $pdo->prepare("UPDATE user_list SET user_token = :user_token WHERE user_id = :user_id");
    $add_token->bindParam(':user_token', $user_token, PDO::PARAM_STR);
    $add_token->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $add_token->execute();

    if($add_token->rowCount() > 0){
        return true;
    }
    else{
        return false;
    };
}

function token_is_valid($pdo, $user_id, $mail_token){
    $bdd_token_request = $pdo->prepare("SELECT user_token FROM user_list WHERE user_id = :user_id");
    $bdd_token_request->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $bdd_token_request->execute();
    $bdd_token = $bdd_token_request->fetch(PDO::FETCH_ASSOC);

    if (hash_equals($bdd_token['user_token'], $mail_token)) {
        return true;
    }
    else {
        return false;
    };
};

function change_user_pass($pdo, $user_id, $user_password){

    $change_user_pass = $pdo->prepare("UPDATE user_list SET user_password = :user_password, user_token = NULL WHERE user_id = :user_id");
    $change_user_pass->bindParam(':user_password', $user_password, PDO::PARAM_STR);
    $change_user_pass->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $change_user_pass->execute();

    if($change_user_pass->rowCount() > 0){
        return true;
    }
    else{
        return false;
    };
}

function rename_session($pdo, $new_session_name, $session_name){

    $rename_session = $pdo->prepare("UPDATE session_list SET session_name = :new_session_name WHERE session_name = :session_name");
    $rename_session->bindParam(':new_session_name', $new_session_name, PDO::PARAM_STR);
    $rename_session->bindParam(':session_name', $session_name, PDO::PARAM_STR);
    $rename_session->execute();

    if($rename_session->rowCount() > 0){
        return true;
    }
    else{
        return false;
    };
};

function change_session_pass($pdo, $new_session_pass, $session_name, $session_type){

    $change_session_pass = $pdo->prepare("UPDATE session_list SET session_type = :session_type, session_pass = :new_session_pass WHERE session_name = :session_name");
    $change_session_pass->bindParam(':new_session_pass', $new_session_pass, PDO::PARAM_STR);
    $change_session_pass->bindParam(':session_type', $session_type, PDO::PARAM_STR);
    $change_session_pass->bindParam(':session_name', $session_name, PDO::PARAM_STR);
    $change_session_pass->execute();

    if($change_session_pass->rowCount() > 0){
        return true;
    }
    else{
        return false;
    };

};

function delete_session($pdo, $session_name){
    $delete_session = $pdo->prepare("DELETE FROM session_list WHERE session_name = :session_name");
    $delete_session->bindParam(':session_name', $session_name, PDO::PARAM_STR);
    $delete_session->execute();

    if($delete_session->rowCount() > 0){
        return true;
    }
    else{
        return false;
    };

};

function remove_session($pdo, $user_id, $session_id){
    $remove_session = $pdo->prepare("DELETE FROM user_right WHERE user_id = :user_id AND session_id = :session_id");
    $remove_session->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $remove_session->bindParam(':session_id', $session_id, PDO::PARAM_STR);
    $remove_session->execute();

    if($remove_session->rowCount() > 0){
        return true;
    }
    else{
        return false;
    };
}