<?php
//define('MAX_X_IMG', 4096);//4K
//define('MAX_Y_IMG', 4096);//4K

//Page d edition
define('RESOLUTION_LEVELS', 8);
define('LENGTH_NAME', 20);
define('MAX_DURATION', 5*60);
define('MAX_Z_IMG', 100);
define('MAX_GROWTH', 10);
define('STEP_GROWTH', 0.1);
define('STEP_AXIS', 1);

//Page d ajout
//...


//fonctions
function hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);
 
    if(strlen($hex) == 3) {
       $r = hexdec(substr($hex,0,1).substr($hex,0,1));
       $g = hexdec(substr($hex,1,1).substr($hex,1,1));
       $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
       $r = hexdec(substr($hex,0,2));
       $g = hexdec(substr($hex,2,2));
       $b = hexdec(substr($hex,4,2));
    }
    
    return array('R' => $r, 'G' => $g, 'B' => $b);
}


function verifHexaColor($hex) {
    $rgb = hex2rgb($hex);

    if (!isset($rgb['R'], $rgb['G'], $rgb['B']) ||
    $rgb['R'] < 0 || $rgb['R'] > 255 ||
    $rgb['G'] < 0 || $rgb['G'] > 255 ||
    $rgb['B'] < 0 || $rgb['B'] > 255) {
        return false;
    }
    
    return true;
}


function presetEdition() {
    $listWarning = false;

    //gestion des blocs form
    {
        //changement de bloc
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
        //initialisation de la page (premier bloc)
        if (!isset($_SESSION['edit']['step'])) {
            $_SESSION['edit']['step'] = 1;

            $_SESSION['edit']['dataFile']['name'] = 'temporaire';
            $_SESSION['edit']['dataFile']['format'] = 'BMP';
            $_SESSION['edit']['dataFile']['dimX'] = 768;
            $_SESSION['edit']['dataFile']['dimY'] = 768;
            $_SESSION['edit']['dataFile']['dimZ'] = MAX_Z_IMG;
            $_SESSION['edit']['dataFile']['video']['duration'] = MAX_DURATION;
            $_SESSION['edit']['dataFile']['video']['frequency'] = 1;

            $_SESSION['edit']['dataScene']['bright'] = 100;
            $_SESSION['edit']['dataScene']['backgroundColor'] = '#80D4FF';
        }
    }

    //enregistrement du contenu des blocs form
    if (isset($_POST['script'])) {
        //bloc 1
        if ($_POST['script'] == 'fileConfig') {
            //enregistrement des entrees
            if (!isset($_POST['fileName'], $_POST['typeFile'], $_POST['duration'], $_POST['frequency']) ||
            !isset($_POST['dimXFile'], $_POST['dimYFile'], $_POST['dimZFile'])) {
                $listWarning[] = 'données formulaire';
                $_SESSION['edit']['step'] = 1;
                //throw new Exception("Connxion : Données formulaire incomplètes");
            }

            $_SESSION['edit']['dataFile']['name'] = filter_input(INPUT_POST, 'fileName', FILTER_SANITIZE_STRING);
            $_SESSION['edit']['dataFile']['format'] = filter_input(INPUT_POST, 'typeFile', FILTER_CALLBACK, 
                    ['options' => function ($data) {return in_array($data, ['picture', 'video']) ? $data : false;}]);

            $_SESSION['edit']['dataFile']['video']['duration'] = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);
            $_SESSION['edit']['dataFile']['video']['frequency'] = filter_input(INPUT_POST, 'frequency', FILTER_VALIDATE_INT);

            $_SESSION['edit']['dataFile']['dimX'] = filter_input(INPUT_POST, 'dimXFile', FILTER_VALIDATE_INT);
            $_SESSION['edit']['dataFile']['dimY'] = filter_input(INPUT_POST, 'dimYFile', FILTER_VALIDATE_INT);
            $_SESSION['edit']['dataFile']['dimZ'] = filter_input(INPUT_POST, 'dimZFile', FILTER_VALIDATE_INT);


            if (!$_SESSION['edit']['dataFile']['name'])   $listWarning[] = 'nom de fichier';
            if (!$_SESSION['edit']['dataFile']['format']) $listWarning[] = 'format de fichier';
            if (!$_SESSION['edit']['dataFile']['video']['duration'])  $listWarning[] = 'durée de la vidéo';
            if (!$_SESSION['edit']['dataFile']['video']['frequency']) $listWarning[] = 'images par secondes';
            if (!$_SESSION['edit']['dataFile']['dimX']) $listWarning[] = 'dimension X du fichier';
            if (!$_SESSION['edit']['dataFile']['dimY']) $listWarning[] = 'dimension Y du fichier';
            if (!$_SESSION['edit']['dataFile']['dimZ']) $listWarning[] = 'dimension Z du fichier';
        } 

        //bloc 2
        if ($_POST['script'] == 'sceneConfig') {
            //comptage des objets
            if (!isset($_SESSION['edit']['dataScene']['shape'])) {
                $nbObjects = 0;
            }
            else {
                $nbObjects = count($_SESSION['edit']['dataScene']['shape']);
            }

            //enregistrement des entrees
            {
                if (!isset($_POST['bright'], $_POST['backgroundColor'])) {
                    $listWarning[] = 'données formulaire';
                    $_SESSION['edit']['step'] = 2;
                    //throw new Exception("Connexion : Données formulaire incomplètes");
                }

                $_SESSION['edit']['dataScene']['bright'] = filter_input(INPUT_POST, 'bright', FILTER_VALIDATE_INT);
                $_SESSION['edit']['dataScene']['backgroundColor'] = filter_input(INPUT_POST, 'backgroundColor', FILTER_SANITIZE_STRING);

                if (!$_SESSION['edit']['dataScene']['bright']) $listWarning[] = 'luminosité de la scène';
                if (!$_SESSION['edit']['dataScene']['backgroundColor'] || 
                !verifHexaColor($_SESSION['edit']['dataScene']['backgroundColor'])) $listWarning[] = 'couleur de fond de la scène';

                $i = 0;
                while ($i < $nbObjects) {
                    $i++;

                    if (!isset($_POST['color'.$i]) || ($_POST['name'.$i] == 'Sphère' && !isset($_POST['radius'.$i])) ||
                    !isset($_POST['posX'.$i], $_POST['posY'.$i], $_POST['posZ'.$i]) ||
                    ($_POST['name'.$i] != 'Sphère' && isset($_POST['rotX'.$i], $_POST['rotY'.$i], $_POST['rotZ'.$i])) &&
                    (!isset($_POST['dimX'.$i], $_POST['dimY'.$i]) && ($_POST['name'.$i] != 'Surface' && !isset($_POST['dimZ'.$i])))) {
                        $listWarning[] = 'données formulaire objets';
                        $_SESSION['edit']['step'] = 2;
                        //throw new Exception("Connexion : Données formulaire incomplètes");
                    }

                    $_SESSION['edit']['dataScene']['shape'][$i]['color'] = filter_input(INPUT_POST, 'color'.$i, FILTER_SANITIZE_STRING);

                    if ($_POST['name'.$i] == 'Sphère') {
                        $_SESSION['edit']['dataScene']['shape'][$i]['radius'] = filter_input(INPUT_POST, 'radius'.$i, FILTER_VALIDATE_INT);
                    }
                    else {
                        $_SESSION['edit']['dataScene']['shape'][$i]['dim']['xAxis'] = filter_input(INPUT_POST, 'dimX'.$i, FILTER_VALIDATE_INT);
                        $_SESSION['edit']['dataScene']['shape'][$i]['dim']['yAxis'] = filter_input(INPUT_POST, 'dimY'.$i, FILTER_VALIDATE_INT);
                        if ($_POST['name'.$i] != 'Surface') {
                            $_SESSION['edit']['dataScene']['shape'][$i]['dim']['zAxis'] = filter_input(INPUT_POST, 'dimZ'.$i, FILTER_VALIDATE_INT);
                        }
                    }

                    $_SESSION['edit']['dataScene']['shape'][$i]['pos']['xAxis'] = filter_input(INPUT_POST, 'posX'.$i, FILTER_VALIDATE_INT);
                    $_SESSION['edit']['dataScene']['shape'][$i]['pos']['yAxis'] = filter_input(INPUT_POST, 'posY'.$i, FILTER_VALIDATE_INT);
                    $_SESSION['edit']['dataScene']['shape'][$i]['pos']['zAxis'] = filter_input(INPUT_POST, 'posZ'.$i, FILTER_VALIDATE_INT);
                    
                    $_SESSION['edit']['dataScene']['shape'][$i]['rot']['xAxis'] = filter_input(INPUT_POST, 'rotX'.$i, FILTER_VALIDATE_INT);
                    $_SESSION['edit']['dataScene']['shape'][$i]['rot']['yAxis'] = filter_input(INPUT_POST, 'rotY'.$i, FILTER_VALIDATE_INT);
                    $_SESSION['edit']['dataScene']['shape'][$i]['rot']['zAxis'] = filter_input(INPUT_POST, 'rotZ'.$i, FILTER_VALIDATE_INT);


                    if (!$_SESSION['edit']['dataScene']['shape'][$i]['color'] || 
                    !verifHexaColor($_SESSION['edit']['dataScene']['shape'][$i]['color'])) $listWarning[] = 'objet '.$i.' : couleur';
                    
                    if ($_POST['name'.$i] == 'Sphère') {
                        if (!$_SESSION['edit']['dataScene']['shape'][$i]['radius']) $listWarning[] = 'objet '.$i.' : rayon';
                    }
                    else {
                        if (!$_SESSION['edit']['dataScene']['shape'][$i]['dim']['xAxis']) $listWarning[] = 'objet '.$i.' : dimension X';
                        if (!$_SESSION['edit']['dataScene']['shape'][$i]['dim']['yAxis']) $listWarning[] = 'objet '.$i.' : dimension Y';
                        if ($_POST['name'.$i] != 'Surface') {
                            if (!$_SESSION['edit']['dataScene']['shape'][$i]['dim']['zAxis']) $listWarning[] = 'objet '.$i.' : dimension Z';
                        }
                    }
                    
                    if (!$_SESSION['edit']['dataScene']['shape'][$i]['pos']['xAxis']) $listWarning[] = 'objet '.$i.' : position X';
                    if (!$_SESSION['edit']['dataScene']['shape'][$i]['pos']['yAxis']) $listWarning[] = 'objet '.$i.' : position Y';
                    if (!$_SESSION['edit']['dataScene']['shape'][$i]['pos']['zAxis']) $listWarning[] = 'objet '.$i.' : position Z';

                    if ($_SESSION['edit']['dataScene']['shape'][$i]['rot']['xAxis'] === false) $listWarning[] = 'objet '.$i.' : rotation X';
                    if ($_SESSION['edit']['dataScene']['shape'][$i]['rot']['yAxis'] === false) $listWarning[] = 'objet '.$i.' : rotation Y';
                    if ($_SESSION['edit']['dataScene']['shape'][$i]['rot']['zAxis'] === false) $listWarning[] = 'objet '.$i.' : rotation Z';
                }
            }

            //initialisation d objet
            if (isset($_POST['confirmShape']) && $_POST['confirmShape'] == 'Confirmer' && $_POST['shape'] != 'Aucun') {
                $_SESSION['edit']['dataScene']['shape'][$nbObjects+1]['name'] = $_POST['shape'];
                $_SESSION['edit']['dataScene']['shape'][$nbObjects+1]['id'] = $nbObjects+1;
                $_SESSION['edit']['dataScene']['shape'][$nbObjects+1]['color'] = '#ffffff';

                if ($_SESSION['edit']['dataScene']['shape'][$nbObjects+1]['name'] == 'Sphère') {
                    $_SESSION['edit']['dataScene']['shape'][$nbObjects+1]['radius'] = 1;
                }
                else {
                    $_SESSION['edit']['dataScene']['shape'][$nbObjects+1]['dim'] = array('xAxis' => 1, 'yAxis' => 1, 'zAxis' => 1);
                }
                $_SESSION['edit']['dataScene']['shape'][$nbObjects+1]['pos'] = array('xAxis' => $_SESSION['edit']['dataFile']['dimX']-1, 
                                                                                    'yAxis' => $_SESSION['edit']['dataFile']['dimY']-1, 
                                                                                    'zAxis' => $_SESSION['edit']['dataFile']['dimZ']);
                $_SESSION['edit']['dataScene']['shape'][$nbObjects+1]['rot'] = array('xAxis' => 0, 'yAxis' => 0, 'zAxis' => 0);
            }

            //suppression d objet
            if (isset($_SESSION['edit']['dataScene']['shape'])) {
                for ($i = 1; $i <= $nbObjects; $i++) {
                    if (isset($_POST['delete_'.$i])) {
                        while ($i < $nbObjects) {
                            $i++;
                            $_SESSION['edit']['dataScene']['shape'][$i]['id']--;
                            $_SESSION['edit']['dataScene']['shape'][$i-1] = $_SESSION['edit']['dataScene']['shape'][$i];
                        }
                        unset($_SESSION['edit']['dataScene']['shape'][$i]);
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


function presetUpdate() {//modifier tout
    $listWarning = false;

    if (isset($_POST['structure'])) {
        $received['script'] = 'formula';

        if (isset($_POST['fName'], $_POST['formula'])) {
            $received['fName'] = filter_input(INPUT_POST, 'fName', FILTER_SANITIZE_STRING);
            $received['formula'] = filter_input(INPUT_POST, 'formula', FILTER_SANITIZE_STRING);

            if (!$received['fName']) {
                $received['echec'][] = 'nom de la formule';
            }
            if (!$received['formula']) {
                $received['echec'][] = 'formule';
            }
        }
        else {
            throw new Exception("Enregistrement : Données formulaire incomplètes");
        }
    }

    if (isset($_POST['texture'])) {
        $received['script'] = 'texture';
        //determiner besoins
        //couleur, ?
    }

    $listWarning = false;

    //chargement de la page
    Update($listWarning);
}