<?php
session_start();

require('global.php');
require('Controller.php');


try {
    if (!isset($_GET['action'])) {
        $action = 'edit';
    }
    else if ($_GET['action'] != 'edit' && $_GET['action'] != 'add') {
        throw new Exception("Page indéfinie");//affiche page d'erreur
    }

    switch ($_GET['action']) {
        case 'edit':
            Edition();
            //Edition(verifFormEdit(verifScript('edit')));
        break;
        case 'add':
            require('View/vUpdate.php');
            //Update(verifFormAdd(verifScript('add')));
        break;
    }
}
catch(Exception $error) {//retravailler
    $errorMessage = $error->getMessage();
    $errorDetail = 'Fichier : ' . $error->getFile() . ', ligne ' . $error->getLine();
    
    require('View/vError.php');
}
