<?php
function user_have_acces($session_info, $user_info){

    if ($session_info['session_user_id'] == $user_info['user_id']){
        // l'utilisateur est l'admin de la session
        return true;
    }
    elseif( $user_info['user_acces'] == 1  ){
        // L'utilisateur est un simple membre
        // l'utilisateur dispose de d'accès a la session

        if ($session_info['session_type'] == 0){
            // La session est public
            return true;
        }
        else {
            // La session est privée

            // Si le dernier mot de passe (hash) saisi par l'utilisateur est valide
            if (hash_equals($user_info['session_pass'], $session_info['session_pass'])){
                return true;
            }
            else {
                // L'administrateur de la session a changé le mot de passe de la session
                // L'utlisateur doit saisir le nouveau mot de passe
                return "password_false";
            };
        };
    }
    else {
        return "acces_false";
    };
};
