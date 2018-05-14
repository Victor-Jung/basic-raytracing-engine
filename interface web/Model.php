<?php

function dbConnect() {
    $errMsg = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
    $dbName = 'eventCalendar';
    $dbUser = 'root';
    $dbPass = '1a9z9e8r';
    
    $dataBase = new PDO('mysql:host=localhost;dbname='.$dbName.';charset=utf8', $dbUser, $dbPass, $errMsg);
    if (!$dataBase) throw new Exception("Base De Données : Echec de connexion");

    return $dataBase;
}
