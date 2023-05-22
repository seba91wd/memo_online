<?php
function user_have_right($user_info, $right){
    if ( $user_info['' . $right . ''] == 1 ){
        return true;
    }
    else {
        return false;
    };
};