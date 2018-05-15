<?php
require('Model.php');


function Edition() {
    if (isset($_SESSION['edit']['step'], $_POST['nextStep']) && $_POST['nextStep']) {
        if ($_SESSION['edit']['step'] < 3) {
            $_SESSION['edit']['step']++;
        }
        else {
            if (isset($_POST['reuseData']) && $_POST['reuseData']) {
                $_SESSION['edit']['step'] = 1;
            }
            else {
                unset($_SESSION['edit']['step']);
                unset($_SESSION['edit']['dataScene']);
            }
        }
    }
    if (!isset($_SESSION['edit']['step'])) {//initialise
        $_SESSION['edit']['step'] = 1;

        $_SESSION['edit']['dataFile']['name'] = 'temporaire';
        $_SESSION['edit']['dataFile']['format'] = 'BMP';
        $_SESSION['edit']['dataFile']['dimX'] = 768;
        $_SESSION['edit']['dataFile']['dimY'] = 768;
        $_SESSION['edit']['dataFile']['dimZ'] = MAX_Z_IMG;
        $_SESSION['edit']['dataFile']['video']['duration'] = MAX_DURATION;
        $_SESSION['edit']['dataFile']['video']['frequency'] = 1;

        $_SESSION['edit']['dataScene']['bright'] = 100;
        $_SESSION['edit']['dataScene']['backgroundColor'] = '#0096C8';
    }
    if (isset($_POST['confirmShape']) && $_POST['confirmShape'] == 'Confirmer' && $_POST['shape'] != 'Aucun') {//initialise
        if (!isset($_SESSION['edit']['dataScene']['shape'])) {
            $nbShape = 1;
        }
        else {
            $nbShape = count($_SESSION['edit']['dataScene']['shape']) + 1;
        }

        $_SESSION['edit']['dataScene']['shape'][$nbShape]['name'] = $_POST['shape'];
        $_SESSION['edit']['dataScene']['shape'][$nbShape]['id'] = $nbShape;
        $_SESSION['edit']['dataScene']['shape'][$nbShape]['growth'] = 0;
        $_SESSION['edit']['dataScene']['shape'][$nbShape]['color'] = '#ffffff';
        $_SESSION['edit']['dataScene']['shape'][$nbShape]['dim'] = array('xAxis' => 1, 'yAxis' => 1, 'zAxis' => 1);
        $_SESSION['edit']['dataScene']['shape'][$nbShape]['pos'] = array('xAxis' => $_SESSION['edit']['dataFile']['dimX'], 
                                                                        'yAxis' => $_SESSION['edit']['dataFile']['dimY'], 
                                                                        'zAxis' => $_SESSION['edit']['dataFile']['dimZ']);
        $_SESSION['edit']['dataScene']['shape'][$nbShape]['rot'] = array('xAxis' => 0, 'yAxis' => 0, 'zAxis' => 0);
    }

    $_POST = array();//fin d'exploitation : vide la variable


    //variables du template
    $template['pageName'] = 'Edition d\'image';
    $template['actual'] = 'edit';
    $template['script'] = false;

    //variables de la page
    $edition['legend'][1] = 'Caractérisation du fichier';
    $edition['legend'][2] = 'Composition du fichier';
    $edition['legend'][3] = 'Validation du résultat';

    //remplissage de la page
    require('View/vEdition.php');
}