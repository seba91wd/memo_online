<?php
// model session.php

include '../inc/init.php';

function request_session_info($pdo, $session_name){
    // Appel des informations de la session
    $request = $pdo->prepare(
        "SELECT session_id, session_name, session_type, session_user_id, session_update, session_pass
        FROM session_list
        WHERE session_name = :session_name"
    );
    $request->bindParam(':session_name', $session_name, PDO::PARAM_STR);
    $request->execute();

    // Controle de la présence du nom de la session dans la bdd
    if($request->rowCount() > 0){
        return $request->fetch(PDO::FETCH_ASSOC);
    }
    else{
        return false;
    }
};

function user_info($pdo, $user_id, $session_id) {
    $request = $pdo->prepare(
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
    $request->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $request->bindParam(':session_id', $session_id, PDO::PARAM_INT);
    $request->execute();
    
    if ($request->rowCount() > 0) {
        return $request->fetch(PDO::FETCH_ASSOC);
    } else {
        return false;
    };
};

function member_info($pdo, $session_id){
    $member_info = $pdo->prepare(
        "SELECT 
        user_list.user_id, 
        user_list.user_pseudo, 
        user_right.user_acces, 
        user_right.create_post, 
        user_right.edit_post, 
        user_right.delete_post, 
        user_right.valide_post
        FROM user_list
        LEFT JOIN user_right
        ON user_list.user_id = user_right.user_id
        AND user_right.session_id = :session_id
        WHERE user_right.user_rule != 1
    ");
    $member_info->bindParam(':session_id', $session_id, PDO::PARAM_STR);
    $member_info->execute();

    if($member_info->rowCount() > 0) {
        return $member_info->fetchAll(PDO::FETCH_ASSOC);
    } 
    else {
        return false;
    };
};

function add_post($pdo, $new_post){
    $add_post = $pdo->prepare(
        'INSERT INTO post_list (post_id, session_id, user_id, post_title, post_mesage, post_color, post_date)
        VALUES (NULL, :session_id, :user_id, :post_title, :post_mesage, :post_color, NOW() )'
    );
    $add_post->bindParam(':session_id', $new_post['session_id'], PDO::PARAM_STR);
    $add_post->bindParam(':user_id', $new_post['user_id'], PDO::PARAM_STR);
    $add_post->bindParam(':post_title', $new_post['post_title'], PDO::PARAM_STR);
    $add_post->bindParam(':post_mesage', $new_post['post_mesage'], PDO::PARAM_STR);
    $add_post->bindParam(':post_color', $new_post['post_color'], PDO::PARAM_STR);
    $add_post->execute();

    if($add_post->rowCount() > 0) {
        return true;
    } 
    else {
        return false;
    };
};

function delete_post($pdo, $post_id){
    $delete_post = $pdo->prepare('DELETE FROM post_list WHERE post_id = :post_id');
    $delete_post->bindParam(':post_id', $post_id, PDO::PARAM_STR);
    $delete_post->execute();

    if($delete_post->rowCount() > 0) {
        return true;
    } 
    else {
        return false;
    };
};

function delete_all_post($pdo, $session_id){
    $delete_all_post = $pdo->prepare('DELETE FROM `post_list` WHERE session_id = :session_id');
    $delete_all_post->bindParam(':session_id', $session_id, PDO::PARAM_STR);
    $delete_all_post->execute();

    if($delete_all_post->rowCount() > 0) {
        return true;
    } 
    else {
        return false;
    };
};

function update_post($pdo, $edit_post){
    $update_post = $pdo->prepare(
        'UPDATE post_list 
        SET post_title = :post_title, 
        post_mesage = :post_mesage, 
        post_color = :post_color, 
        post_update = NOW()
        WHERE post_id = :post_id
    ');
    $update_post->bindParam(':post_title', $edit_post['post_title'], PDO::PARAM_STR);
    $update_post->bindParam(':post_mesage', $edit_post['post_mesage'], PDO::PARAM_STR);
    $update_post->bindParam(':post_color', $edit_post['post_color'], PDO::PARAM_STR);
    $update_post->bindParam(':post_id', $edit_post['post_id'], PDO::PARAM_STR);
    $update_post->execute();

    if($update_post->rowCount() > 0) {
        return true;
    }
    else {
        return false;
    };
};


function load_post($pdo, $session_info){
    // Sélèction des données pour affichage sur les Mémo

    $load_post = $pdo->prepare(
        'SELECT 
        post_list.post_id, 
        post_list.session_id, 
        post_list.user_id, 
        user_list.user_pseudo,
        user_list.user_mail, 
        post_list.post_title, 
        post_list.post_mesage, 
        post_list.post_statut, 
        post_list.post_color, 
        DATE_FORMAT(post_list.post_date, "%d/%m/%Y %H:%i:%s") AS post_date_fr, 
        DATE_FORMAT(post_list.post_update, "%d/%m/%Y %H:%i:%s") AS post_date_update_fr
        FROM post_list 
        JOIN user_list ON post_list.user_id = user_list.user_id
        WHERE post_list.session_id = :session_id
        ORDER BY post_list.post_date ASC'
    );
    $load_post->bindParam(':session_id', $session_info['session_id'], PDO::PARAM_STR);
    $load_post->execute();

    if($load_post->rowCount() > 0) {
        return $load_post->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        return false;
    };
};


function user_right_update($pdo, $right_update_info){
    $right_post = $right_update_info['right_post'];
    $right_value = $right_update_info['right_value'];
    $user_id = $right_update_info['user_id'];
    $session_id = $right_update_info['session_id'];

    $user_right_update = "";

    if ($right_post == "create_post"){
        $user_right_update = $pdo->prepare(
            'UPDATE user_right
            SET create_post = :right_value
            WHERE user_id = :user_id
            AND session_id = :session_id'
        );
    }

    elseif ($right_post == "edit_post"){
        $user_right_update = $pdo->prepare(
            'UPDATE user_right
            SET edit_post = :right_value
            WHERE user_id = :user_id
            AND session_id = :session_id'
        );
    }

    elseif ($right_post == "delete_post"){
        $user_right_update = $pdo->prepare(
            'UPDATE user_right
            SET delete_post = :right_value
            WHERE user_id = :user_id
            AND session_id = :session_id'
        );
    }

    elseif ($right_post == "valide_post"){
        $user_right_update = $pdo->prepare(
            'UPDATE user_right
            SET valide_post = :right_value
            WHERE user_id = :user_id
            AND session_id = :session_id'
        );
    }

    $user_right_update->bindParam(':right_value', $right_value, PDO::PARAM_STR);
    $user_right_update->bindParam(':user_id', $user_id, PDO::PARAM_STR);
    $user_right_update->bindParam(':session_id', $session_id, PDO::PARAM_STR);
    $user_right_update->execute();

    if($user_right_update->rowCount() > 0) {
        return true;
    }
    else {
        return false;
    };
};

function update_session_pass($pdo, $session_id, $session_pass){
    $update_session_pass = $pdo->prepare(
        "UPDATE session_list 
        SET session_pass = :session_pass
        WHERE session_id = :session_id"
    );
    $update_session_pass->bindParam(':session_pass', $session_pass, PDO::PARAM_STR);
    $update_session_pass->bindParam(':session_id', $session_id, PDO::PARAM_STR);
    $update_session_pass->execute();

    if($update_session_pass->rowCount() > 0){
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

function statut_post($pdo, $statut_info){
    $post_statut_requet = $pdo->prepare('SELECT post_statut FROM post_list WHERE post_id = :post_id');
    $post_statut_requet->bindParam(':post_id', $statut_info['post_id'], PDO::PARAM_STR);
    $post_statut_requet->execute();
    $post_statut = $post_statut_requet->fetch(PDO::FETCH_ASSOC);

    if ($statut_info['post_statut'] == 'valid'){
        if ($post_statut['post_statut'] == 1){
            $valid_post = $pdo->prepare('UPDATE post_list SET post_statut = "0" WHERE post_id = :post_id');
            $valid_post->bindParam(':post_id', $statut_info['post_id'], PDO::PARAM_STR);
            $valid_post->execute();
    
            if($valid_post->rowCount() > 0) {
                return true;
            }
            else {
                return false;
            };
    
        }
        else {
            $valid_post = $pdo->prepare('UPDATE post_list SET post_statut = "1" WHERE post_id = :post_id');
            $valid_post->bindParam(':post_id', $statut_info['post_id'], PDO::PARAM_STR);
            $valid_post->execute();
    
            if($valid_post->rowCount() > 0) {
                return true;
            }
            else {
                return false;
            };
        };
    }

    elseif ($statut_info['post_statut'] == 'archive'){
        if ($post_statut['post_statut'] == 2){
            $valid_post = $pdo->prepare('UPDATE post_list SET post_statut = "0" WHERE post_id = :post_id');
            $valid_post->bindParam(':post_id', $statut_info['post_id'], PDO::PARAM_STR);
            $valid_post->execute();
    
            if($valid_post->rowCount() > 0) {
                return true;
            }
            else {
                return false;
            };
        }
        else {
            $valid_post = $pdo->prepare('UPDATE post_list SET post_statut = "2" WHERE post_id = :post_id');
            $valid_post->bindParam(':post_id', $statut_info['post_id'], PDO::PARAM_STR);
            $valid_post->execute();
    
            if($valid_post->rowCount() > 0) {
                return true;
            }
            else {
                return false;
            };
        };
    };
};




