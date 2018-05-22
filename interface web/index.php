<?php
session_start();

require('global.php');
require('Controller.php');

//$_SESSION = array();
try {
    //presetEdition : recuperation des champs posts
    $listWarning = false;

    //enregistrement du contenu des blocs form
    if (isset($_POST['script'])) {
        //bloc 1
        if ($_POST['script'] == 'fileConfig') {
            //enregistrement des entrees
            if (!isset($_POST['fileName'], $_POST['typeFile'], $_POST['duration'], $_POST['frequency']) ||
            !isset($_POST['dimXFile'], $_POST['dimYFile'], $_POST['dimZFile'])) {
                $listWarning[] = 'données formulaire';
                $_SESSION['pageBlock'] = 1;
                //throw new Exception("Connxion : Données formulaire incomplètes");
            }

            $_SESSION['name'] = filter_input(INPUT_POST, 'fileName', FILTER_SANITIZE_STRING);
            $_SESSION['format'] = filter_input(INPUT_POST, 'typeFile', FILTER_CALLBACK, 
                    ['options' => function ($data) {return in_array($data, ['picture', 'video']) ? $data : false;}]);

            $_SESSION['video']['duration'] = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);
            $_SESSION['video']['frequency'] = filter_input(INPUT_POST, 'frequency', FILTER_VALIDATE_INT);

            $_SESSION['dimX'] = filter_input(INPUT_POST, 'dimXFile', FILTER_VALIDATE_INT);
            $_SESSION['dimY'] = filter_input(INPUT_POST, 'dimYFile', FILTER_VALIDATE_INT);
            $_SESSION['dimZ'] = filter_input(INPUT_POST, 'dimZFile', FILTER_VALIDATE_INT);


            if (!$_SESSION['name'])   $listWarning[] = 'nom de fichier';
            if (!$_SESSION['format']) $listWarning[] = 'format de fichier';
            if (!$_SESSION['video']['duration'])  $listWarning[] = 'durée de la vidéo';
            if (!$_SESSION['video']['frequency']) $listWarning[] = 'images par secondes';
            if (!$_SESSION['dimX']) $listWarning[] = 'dimension X du fichier';
            if (!$_SESSION['dimY']) $listWarning[] = 'dimension Y du fichier';
            if (!$_SESSION['dimZ']) $listWarning[] = 'dimension Z du fichier';
        }

        //bloc 2
        if ($_POST['script'] == 'sceneConfig') {
            //comptage des objets
            if (!isset($_SESSION['shape'])) {
                $nbObjects = 0;
            }
            else {
                $nbObjects = count($_SESSION['shape']);
            }

            //enregistrement des entrees
            {
                if (!isset($_POST['bright'], $_POST['backgroundColor'])) {
                    $listWarning[] = 'données formulaire';
                    $_SESSION['pageBlock'] = 2;
                    //throw new Exception("Connexion : Données formulaire incomplètes");
                }

                $_SESSION['brightScene'] = filter_input(INPUT_POST, 'bright', FILTER_VALIDATE_INT);
                $_SESSION['backgroundColor'] = filter_input(INPUT_POST, 'backgroundColor', FILTER_SANITIZE_STRING);

                if (!$_SESSION['brightScene']) $listWarning[] = 'luminosité de la scène';
                if (!$_SESSION['backgroundColor'] || 
                !verifHexaColor($_SESSION['backgroundColor'])) $listWarning[] = 'couleur de fond de la scène';

                $i = 0;
                while ($i < $nbObjects) {
                    $i++;

                    if (!isset($_POST['color'.$i]) || ($_POST['name'.$i] == 'Sphère' && !isset($_POST['radius'.$i])) ||
                    !isset($_POST['posX'.$i], $_POST['posY'.$i], $_POST['posZ'.$i]) ||
                    ($_POST['name'.$i] != 'Sphère' && isset($_POST['rotX'.$i], $_POST['rotY'.$i], $_POST['rotZ'.$i])) &&
                    (!isset($_POST['dimX'.$i], $_POST['dimY'.$i]) && ($_POST['name'.$i] != 'Surface' && !isset($_POST['dimZ'.$i])))) {
                        $listWarning[] = 'données formulaire objets';
                        $_SESSION['pageBlock'] = 2;
                        //throw new Exception("Connexion : Données formulaire incomplètes");
                    }

                    $_SESSION['shape'][$i]['color'] = filter_input(INPUT_POST, 'color'.$i, FILTER_SANITIZE_STRING);

                    if ($_POST['name'.$i] == 'Sphère') {
                        $_SESSION['shape'][$i]['radius'] = filter_input(INPUT_POST, 'radius'.$i, FILTER_VALIDATE_INT);
                    }
                    else {
                        $_SESSION['shape'][$i]['dim']['xAxis'] = filter_input(INPUT_POST, 'dimX'.$i, FILTER_VALIDATE_INT);
                        $_SESSION['shape'][$i]['dim']['yAxis'] = filter_input(INPUT_POST, 'dimY'.$i, FILTER_VALIDATE_INT);
                        if ($_POST['name'.$i] != 'Surface') {
                            $_SESSION['shape'][$i]['dim']['zAxis'] = filter_input(INPUT_POST, 'dimZ'.$i, FILTER_VALIDATE_INT);
                        }
                    }

                    $_SESSION['shape'][$i]['pos']['xAxis'] = filter_input(INPUT_POST, 'posX'.$i, FILTER_VALIDATE_INT);
                    $_SESSION['shape'][$i]['pos']['yAxis'] = filter_input(INPUT_POST, 'posY'.$i, FILTER_VALIDATE_INT);
                    $_SESSION['shape'][$i]['pos']['zAxis'] = filter_input(INPUT_POST, 'posZ'.$i, FILTER_VALIDATE_INT);
                    
                    $_SESSION['shape'][$i]['rot']['xAxis'] = filter_input(INPUT_POST, 'rotX'.$i, FILTER_VALIDATE_INT);
                    $_SESSION['shape'][$i]['rot']['yAxis'] = filter_input(INPUT_POST, 'rotY'.$i, FILTER_VALIDATE_INT);
                    $_SESSION['shape'][$i]['rot']['zAxis'] = filter_input(INPUT_POST, 'rotZ'.$i, FILTER_VALIDATE_INT);


                    if (!$_SESSION['shape'][$i]['color'] || 
                    !verifHexaColor($_SESSION['shape'][$i]['color'])) $listWarning[] = 'objet '.$i.' : couleur';
                    
                    if ($_POST['name'.$i] == 'Sphère') {
                        if (!$_SESSION['shape'][$i]['radius']) $listWarning[] = 'objet '.$i.' : rayon';
                    }
                    else {
                        if (!$_SESSION['shape'][$i]['dim']['xAxis']) $listWarning[] = 'objet '.$i.' : dimension X';
                        if (!$_SESSION['shape'][$i]['dim']['yAxis']) $listWarning[] = 'objet '.$i.' : dimension Y';
                        if ($_POST['name'.$i] != 'Surface') {
                            if (!$_SESSION['shape'][$i]['dim']['zAxis']) $listWarning[] = 'objet '.$i.' : dimension Z';
                        }
                    }
                    
                    if (!$_SESSION['shape'][$i]['pos']['xAxis']) $listWarning[] = 'objet '.$i.' : position X';
                    if (!$_SESSION['shape'][$i]['pos']['yAxis']) $listWarning[] = 'objet '.$i.' : position Y';
                    if (!$_SESSION['shape'][$i]['pos']['zAxis']) $listWarning[] = 'objet '.$i.' : position Z';

                    if ($_SESSION['shape'][$i]['rot']['xAxis'] === false) $listWarning[] = 'objet '.$i.' : rotation X';
                    if ($_SESSION['shape'][$i]['rot']['yAxis'] === false) $listWarning[] = 'objet '.$i.' : rotation Y';
                    if ($_SESSION['shape'][$i]['rot']['zAxis'] === false) $listWarning[] = 'objet '.$i.' : rotation Z';
                }
            }

            //initialisation d objet
            if (isset($_POST['confirmShape']) && $_POST['confirmShape'] == 'Confirmer' && $_POST['shape'] != 'Aucun') {
                $_SESSION['shape'][$nbObjects+1]['name'] = $_POST['shape'];
                $_SESSION['shape'][$nbObjects+1]['id'] = $nbObjects+1;
                $_SESSION['shape'][$nbObjects+1]['color'] = '#ffffff';

                if ($_SESSION['shape'][$nbObjects+1]['name'] == 'Sphère') {
                    $_SESSION['shape'][$nbObjects+1]['radius'] = 1;
                }
                else {
                    $_SESSION['shape'][$nbObjects+1]['dim'] = array('xAxis' => 1, 'yAxis' => 1, 'zAxis' => 1);
                }
                $_SESSION['shape'][$nbObjects+1]['pos'] = array('xAxis' => $_SESSION['dimX']-1, 
                                                                                    'yAxis' => $_SESSION['dimY']-1, 
                                                                                    'zAxis' => $_SESSION['dimZ']);
                $_SESSION['shape'][$nbObjects+1]['rot'] = array('xAxis' => 0, 'yAxis' => 0, 'zAxis' => 0);
                
                //ajouter aux formulaires
                for ($i = 0; $i < 6; $i++) {//6 faces pour le pave
                    $_SESSION['shape'][$nbObjects+1]['faces'][$i]['color'] = '#ffffff';
                }
            }

            //suppression d objet
            if (isset($_SESSION['shape'])) {
                for ($i = 1; $i <= $nbObjects; $i++) {
                    if (isset($_POST['delete_'.$i])) {
                        while ($i < $nbObjects) {
                            $i++;
                            $_SESSION['shape'][$i]['id']--;
                            $_SESSION['shape'][$i-1] = $_SESSION['shape'][$i];
                        }
                        unset($_SESSION['shape'][$i]);
                    }
                }
            }
        } 

        //bloc 3
        if ($_POST['script'] == 'patchConfig') {
            //enregistrement des entrees
            /*
                rien pour l instant
            */
        }

        //fin d'exploitation : invalide le formulaire
        $_POST['script'] = false;
    }

    //chargement de la page
    Edition($listWarning);
}
catch(Exception $error) {//retravailler
    $errorMessage = $error->getMessage();
    $errorDetail = 'Fichier : ' . $error->getFile() . ', ligne ' . $error->getLine();
    
    require('View/vError.php');
}
