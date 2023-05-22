<?php
global_debug();
function global_debug(){
    echo '<pre style="
        margin: 0 auto;
        width: 600px;
        background-color: #ff0000b0;
        color: #b4ff00;
        border-radius: 10px;
        padding: 10px;
        text-align: left;
    ">';
    echo ' *************************************<br>';
    echo ' ************* DEBUG *************<br>';
    echo '<br>';
    echo '$_SESSION =><br>';
    if(isset($_SESSION)){
        print_r($_SESSION);
    }else{
        echo 'none<br>';
    }
    echo '<br>';
    echo ' *************************************<br>';
    echo '<br>';
    echo '$_POST =><br>';
    print_r($_POST);
    echo ' *************************************<br>';
    echo '$_GET =><br>';
    print_r($_GET);
    echo '<br>';
    echo ' ************* DEBUG *************<br>';
    echo ' *************************************<br>';
    echo '</pre>';
}