<?php
// model option_session.php

include '../inc/init.php';

function filter_change($pdo, $option_data){

    // print_r($option_data);
    
    $filtre_change = $pdo->prepare(
        "UPDATE user_option 
        SET memo_simple = :memo_simple, memo_valide = :memo_valide, memo_archive = :memo_archive
        WHERE user_id = :user_id
        AND session_id = :session_id
    "
    );
    $filtre_change->bindParam(':user_id', $option_data['user_id'], PDO::PARAM_STR);
    $filtre_change->bindParam(':session_id', $option_data['session_id'], PDO::PARAM_STR);
    $filtre_change->bindParam(':memo_simple', $option_data['memo_simple'], PDO::PARAM_STR);
    $filtre_change->bindParam(':memo_valide', $option_data['memo_valide'], PDO::PARAM_STR);
    $filtre_change->bindParam(':memo_archive', $option_data['memo_archive'], PDO::PARAM_STR);
    $filtre_change->execute();

    if ($filtre_change->rowCount() > 0) {
        return true;
    } else {
        return false;
    };
}

function cat_change($pdo, $option_data){

    // print_r($option_data);

    $cat_change = $pdo->prepare(
        "UPDATE user_option 
        SET display_cat = :display_cat
        WHERE user_id = :user_id
        AND session_id = :session_id
    "
    );
    $cat_change->bindParam(':user_id', $option_data['user_id'], PDO::PARAM_STR);
    $cat_change->bindParam(':session_id', $option_data['session_id'], PDO::PARAM_STR);
    $cat_change->bindParam(':display_cat', $option_data['display_cat'], PDO::PARAM_STR);
    $cat_change->execute();

    if ($cat_change->rowCount() > 0) {
        return true;
    } else {
        return false;
    };
}