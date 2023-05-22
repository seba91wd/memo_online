<?php
// Ce fichier reçoit les requêtes asynchrones envoyées depuis le controler de session.php

include '../models/function_async.php';

// ------------------------------------------------------------------------------------
// Filtre d'affichage des mémo
// ------------------------------------------------------------------------------------

if (isset($_POST['user_id']) && isset($_POST['session_id'])){
    
    if (isset($_POST['memo_simple']) && isset($_POST['memo_valide']) && isset($_POST['memo_archive'])){
        $option_data = array(
            'user_id' => $_POST['user_id'],
            'session_id' => $_POST['session_id'],
            'memo_simple' => $_POST['memo_simple'],
            'memo_valide' => $_POST['memo_valide'],
            'memo_archive' => $_POST['memo_archive']
        );
        
        if (filter_change($pdo, $option_data)){
            return true;
        }
        else {
            return false;
        };
    }
    if (isset($_POST['display_cat'])){
        $option_data = array(
            'user_id' => $_POST['user_id'],
            'session_id' => $_POST['session_id'],
            'display_cat' => $_POST['display_cat']
        );

        cat_change($pdo, $option_data);
    }
};