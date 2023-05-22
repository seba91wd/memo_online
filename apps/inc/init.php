<?php
// Connexion à la BDD
$host = 'mysql:host=localhost;dbname=memo_online';
$login = 'admin_memo_online';
$password = '***************';
$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
);
$pdo = new PDO($host, $login, $password, $options);

// Variable contenant les messages pour l'utilisateur
$msg = '';