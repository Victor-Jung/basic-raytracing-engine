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
            $_SESSION['file']['name']               = isset($_POST['fileName']) ? filter_input(INPUT_POST, 'fileName',  FILTER_SANITIZE_STRING) : false ;
            $_SESSION['file']['video']['selected']  = isset($_POST['video'])    ? filter_input(INPUT_POST, 'video',     FILTER_VALIDATE_INT)    : null ;
            $_SESSION['file']['video']['duration']  = isset($_POST['duration']) ? filter_input(INPUT_POST, 'duration',  FILTER_VALIDATE_INT)    : false ;
            $_SESSION['file']['video']['frequency'] = isset($_POST['frequency'])? filter_input(INPUT_POST, 'frequency', FILTER_VALIDATE_INT)    : false ;
            $_SESSION['file']['dim']['x']           = isset($_POST['dimXFile']) ? filter_input(INPUT_POST, 'dimXFile',  FILTER_VALIDATE_INT)    : false ;
            $_SESSION['file']['dim']['y']           = isset($_POST['dimYFile']) ? filter_input(INPUT_POST, 'dimYFile',  FILTER_VALIDATE_INT)    : false ;


            if (!$_SESSION['file']['name'])                         $listWarning[] = 'nom de fichier';
            if (is_null($_SESSION['file']['video']['selected']))    $listWarning[] = 'format de fichier';
            if (!$_SESSION['file']['video']['duration'])            $listWarning[] = 'durée de la vidéo';
            if (!$_SESSION['file']['video']['frequency'])           $listWarning[] = 'images par secondes';
            if (!$_SESSION['file']['dim']['x'])                     $listWarning[] = 'dimension X du fichier';
            if (!$_SESSION['file']['dim']['y'])                     $listWarning[] = 'dimension Y du fichier';
        }

        //bloc 2
        if ($_POST['script'] == 'sceneConfig') {
            //comptage des objets variables
            $nbLight = (!isset($_SESSION['scene']['light']))? 0 : count($_SESSION['scene']['light']);
            $nbEllipsoid = (!isset($_SESSION['ellipsoid']))? 0 : count($_SESSION['ellipsoid']);
            $nbPolyhedron = (!isset($_SESSION['polyhedron']))? 0 : count($_SESSION['polyhedron']);

            //enregistrement des entrees
            {
                //entrees fixes
                {
                    $_SESSION['scene']['color']                 = isset($_POST['sceneColor'])   ? filter_input(INPUT_POST, 'sceneColor',    FILTER_SANITIZE_STRING) : false ;
                    $_SESSION['scene']['viewer']['x']           = isset($_POST['viewerX'])      ? filter_input(INPUT_POST, 'viewerX',       FILTER_VALIDATE_INT)    : false ;
                    $_SESSION['scene']['viewer']['y']           = isset($_POST['viewerY'])      ? filter_input(INPUT_POST, 'viewerY',       FILTER_VALIDATE_INT)    : false ;
                    $_SESSION['scene']['viewer']['z']           = isset($_POST['viewerZ'])      ? filter_input(INPUT_POST, 'viewerZ',       FILTER_VALIDATE_INT)    : false ;

                    $_SESSION['file']['effects']['shadows']     = isset($_POST['shadows'])      ? filter_input(INPUT_POST, 'shadows',       FILTER_VALIDATE_INT)    : 0 ;
                    $_SESSION['file']['effects']['reflection']  = isset($_POST['reflection'])   ? filter_input(INPUT_POST, 'reflection',    FILTER_VALIDATE_INT)    : 0 ;
                    $_SESSION['file']['effects']['refraction']  = isset($_POST['refraction'])   ? filter_input(INPUT_POST, 'refraction',    FILTER_VALIDATE_INT)    : 0 ;


                    if (!$_SESSION['scene']['color'] || !verifHexaColor($_SESSION['scene']['color']))   $listWarning[] = 'couleur de la scène';
                    if (!$_SESSION['scene']['viewer']['x'])                                             $listWarning[] = 'Position x de l\'observateur';
                    if (!$_SESSION['scene']['viewer']['y'])                                             $listWarning[] = 'Position y de l\'observateur';
                    if (!$_SESSION['scene']['viewer']['z'])                                             $listWarning[] = 'Position z de l\'observateur';

                    if (is_null($_SESSION['file']['effects']['shadows']))       $listWarning[] = 'Choix des ombres';
                    if (is_null($_SESSION['file']['effects']['reflection']))    $listWarning[] = 'Choix de la réflexion';
                    if (is_null($_SESSION['file']['effects']['refraction']))    $listWarning[] = 'Choix de la réfraction';
                }

                //entrees variables
                {
                    for ($light = 0; $light < $nbLight; $light++) {
                        $_SESSION['scene']['light'][$light]['bright']   = isset($_POST['bright'])   ? filter_input(INPUT_POST, 'bright', FILTER_VALIDATE_FLOAT) : false ;
                        $_SESSION['scene']['light'][$light]['pos']['x'] = isset($_POST['lightX'])   ? filter_input(INPUT_POST, 'lightX', FILTER_VALIDATE_INT)   : false ;
                        $_SESSION['scene']['light'][$light]['pos']['y'] = isset($_POST['lightY'])   ? filter_input(INPUT_POST, 'lightY', FILTER_VALIDATE_INT)   : false ;
                        $_SESSION['scene']['light'][$light]['pos']['z'] = isset($_POST['lightZ'])   ? filter_input(INPUT_POST, 'lightZ', FILTER_VALIDATE_INT)   : false ;

                        if (!$_SESSION['scene']['light'][$light]['bright'])     $listWarning[] = 'luminosité de la scène';
                        if (!$_SESSION['scene']['light'][$light]['pos']['x'])   $listWarning[] = 'Position x de la lumière';
                        if (!$_SESSION['scene']['light'][$light]['pos']['y'])   $listWarning[] = 'Position y de la lumière';
                        if (!$_SESSION['scene']['light'][$light]['pos']['z'])   $listWarning[] = 'Position z de la lumière';
                    }


                /*
                    $i = 0;
                    while ($i < $nbEllipsoid) {
                        $i++;
                        /*
                        if (!isset($_POST['color'.$i]) || 
                        !isset($_POST['posX'.$i], $_POST['posY'.$i], $_POST['posZ'.$i]) ||
                        !isset($_POST['radX'.$i], $_POST['radY'.$i], $_POST['radZ'.$i]) ||
                        !isset($_POST['rotX'.$i], $_POST['rotY'.$i], $_POST['rotZ'.$i])) {
                            $listWarning[] = 'données formulaire objets';
                            $_SESSION['pageBlock'] = 2;
                            //throw new Exception("Connexion : Données formulaire incomplètes");
                        }

                        $_SESSION['shape'][$i]['color'] = filter_input(INPUT_POST, 'color'.$i, FILTER_SANITIZE_STRING);

                        if ($_POST['name'.$i] == 'Sphère') {
                            $_SESSION['shape'][$i]['radius'] = filter_input(INPUT_POST, 'radius'.$i, FILTER_VALIDATE_INT);
                        }
                        else {
                            $_SESSION['shape'][$i]['dim']['x'] = filter_input(INPUT_POST, 'dimX'.$i, FILTER_VALIDATE_INT);
                            $_SESSION['shape'][$i]['dim']['y'] = filter_input(INPUT_POST, 'dimY'.$i, FILTER_VALIDATE_INT);
                            if ($_POST['name'.$i] != 'Surface') {
                                $_SESSION['shape'][$i]['dim']['z'] = filter_input(INPUT_POST, 'dimZ'.$i, FILTER_VALIDATE_INT);
                            }
                        }

                        $_SESSION['shape'][$i]['pos']['x'] = filter_input(INPUT_POST, 'posX'.$i, FILTER_VALIDATE_INT);
                        $_SESSION['shape'][$i]['pos']['y'] = filter_input(INPUT_POST, 'posY'.$i, FILTER_VALIDATE_INT);
                        $_SESSION['shape'][$i]['pos']['z'] = filter_input(INPUT_POST, 'posZ'.$i, FILTER_VALIDATE_INT);
                        
                        $_SESSION['shape'][$i]['rot']['x'] = filter_input(INPUT_POST, 'rotX'.$i, FILTER_VALIDATE_INT);
                        $_SESSION['shape'][$i]['rot']['y'] = filter_input(INPUT_POST, 'rotY'.$i, FILTER_VALIDATE_INT);
                        $_SESSION['shape'][$i]['rot']['z'] = filter_input(INPUT_POST, 'rotZ'.$i, FILTER_VALIDATE_INT);


                        if (!$_SESSION['shape'][$i]['color'] || 
                        !verifHexaColor($_SESSION['shape'][$i]['color'])) $listWarning[] = 'objet '.$i.' : couleur';
                        
                        if ($_POST['name'.$i] == 'Sphère') {
                            if (!$_SESSION['shape'][$i]['radius']) $listWarning[] = 'objet '.$i.' : rayon';
                        }
                        else {
                            if (!$_SESSION['shape'][$i]['dim']['x']) $listWarning[] = 'objet '.$i.' : dimension X';
                            if (!$_SESSION['shape'][$i]['dim']['y']) $listWarning[] = 'objet '.$i.' : dimension Y';
                            if ($_POST['name'.$i] != 'Surface') {
                                if (!$_SESSION['shape'][$i]['dim']['z']) $listWarning[] = 'objet '.$i.' : dimension Z';
                            }
                        }
                        
                        if (!$_SESSION['shape'][$i]['pos']['x']) $listWarning[] = 'objet '.$i.' : position X';
                        if (!$_SESSION['shape'][$i]['pos']['y']) $listWarning[] = 'objet '.$i.' : position Y';
                        if (!$_SESSION['shape'][$i]['pos']['z']) $listWarning[] = 'objet '.$i.' : position Z';

                        if ($_SESSION['shape'][$i]['rot']['x'] === false) $listWarning[] = 'objet '.$i.' : rotation X';
                        if ($_SESSION['shape'][$i]['rot']['y'] === false) $listWarning[] = 'objet '.$i.' : rotation Y';
                        if ($_SESSION['shape'][$i]['rot']['z'] === false) $listWarning[] = 'objet '.$i.' : rotation Z';
                        *//*
                    }

                    $i = 0;
                    while ($i < $nbPolyhedron) {
                        $i++;
                        /*
                        if (!isset($_POST['color'.$i]) || 
                        !isset($_POST['posX'.$i], $_POST['posY'.$i], $_POST['posZ'.$i]) ||
                        !isset($_POST['radX'.$i], $_POST['radY'.$i], $_POST['radZ'.$i]) ||
                        !isset($_POST['rotX'.$i], $_POST['rotY'.$i], $_POST['rotZ'.$i])) {
                            $listWarning[] = 'données formulaire objets';
                            $_SESSION['pageBlock'] = 2;
                            //throw new Exception("Connexion : Données formulaire incomplètes");
                        }

                        $_SESSION['shape'][$i]['color'] = filter_input(INPUT_POST, 'color'.$i, FILTER_SANITIZE_STRING);

                        if ($_POST['name'.$i] == 'Sphère') {
                            $_SESSION['shape'][$i]['radius'] = filter_input(INPUT_POST, 'radius'.$i, FILTER_VALIDATE_INT);
                        }
                        else {
                            $_SESSION['shape'][$i]['dim']['x'] = filter_input(INPUT_POST, 'dimX'.$i, FILTER_VALIDATE_INT);
                            $_SESSION['shape'][$i]['dim']['y'] = filter_input(INPUT_POST, 'dimY'.$i, FILTER_VALIDATE_INT);
                            if ($_POST['name'.$i] != 'Surface') {
                                $_SESSION['shape'][$i]['dim']['z'] = filter_input(INPUT_POST, 'dimZ'.$i, FILTER_VALIDATE_INT);
                            }
                        }

                        $_SESSION['shape'][$i]['pos']['x'] = filter_input(INPUT_POST, 'posX'.$i, FILTER_VALIDATE_INT);
                        $_SESSION['shape'][$i]['pos']['y'] = filter_input(INPUT_POST, 'posY'.$i, FILTER_VALIDATE_INT);
                        $_SESSION['shape'][$i]['pos']['z'] = filter_input(INPUT_POST, 'posZ'.$i, FILTER_VALIDATE_INT);
                        
                        $_SESSION['shape'][$i]['rot']['x'] = filter_input(INPUT_POST, 'rotX'.$i, FILTER_VALIDATE_INT);
                        $_SESSION['shape'][$i]['rot']['y'] = filter_input(INPUT_POST, 'rotY'.$i, FILTER_VALIDATE_INT);
                        $_SESSION['shape'][$i]['rot']['z'] = filter_input(INPUT_POST, 'rotZ'.$i, FILTER_VALIDATE_INT);


                        if (!$_SESSION['shape'][$i]['color'] || 
                        !verifHexaColor($_SESSION['shape'][$i]['color'])) $listWarning[] = 'objet '.$i.' : couleur';
                        
                        if ($_POST['name'.$i] == 'Sphère') {
                            if (!$_SESSION['shape'][$i]['radius']) $listWarning[] = 'objet '.$i.' : rayon';
                        }
                        else {
                            if (!$_SESSION['shape'][$i]['dim']['x']) $listWarning[] = 'objet '.$i.' : dimension X';
                            if (!$_SESSION['shape'][$i]['dim']['y']) $listWarning[] = 'objet '.$i.' : dimension Y';
                            if ($_POST['name'.$i] != 'Surface') {
                                if (!$_SESSION['shape'][$i]['dim']['z']) $listWarning[] = 'objet '.$i.' : dimension Z';
                            }
                        }
                        
                        if (!$_SESSION['shape'][$i]['pos']['x']) $listWarning[] = 'objet '.$i.' : position X';
                        if (!$_SESSION['shape'][$i]['pos']['y']) $listWarning[] = 'objet '.$i.' : position Y';
                        if (!$_SESSION['shape'][$i]['pos']['z']) $listWarning[] = 'objet '.$i.' : position Z';

                        if ($_SESSION['shape'][$i]['rot']['x'] === false) $listWarning[] = 'objet '.$i.' : rotation X';
                        if ($_SESSION['shape'][$i]['rot']['y'] === false) $listWarning[] = 'objet '.$i.' : rotation Y';
                        if ($_SESSION['shape'][$i]['rot']['z'] === false) $listWarning[] = 'objet '.$i.' : rotation Z';
                        *//*
                    }
                */
                }
            }

            //initialisation d objet
            if (isset($_POST['confirmShape']) && $_POST['confirmShape'] == 'Confirmer') {
                if ($_POST['shape'] == 'Ellipsoïde') {
                    $_SESSION['ellipsoid'][$nbEllipsoid+1]['name'] = $_POST['shape'];
                    $_SESSION['ellipsoid'][$nbEllipsoid+1]['color'] = '#ffffff';

                    $_SESSION['ellipsoid'][$nbEllipsoid+1]['pos'] = array('x' => 1, 'y' => 1, 'z' => 1);
                    $_SESSION['ellipsoid'][$nbEllipsoid+1]['rad'] = array('x' => 1, 'y' => 1, 'z' => 1);
                    $_SESSION['ellipsoid'][$nbEllipsoid+1]['rot'] = array('x' => 1, 'y' => 1, 'z' => 1);
                }
                else if ($_POST['shape'] == 'Polyèdre') {
                    $_SESSION['polyhedron'][$nbPolyhedron+1]['name'] = $_POST['shape'];

                    for ($i = 0; $i < $var; $i++) {
                        $_SESSION['polyhedron'][$nbPolyhedron+1]['face'][$i]['color'] = '#ffffff';
                        $_SESSION['polyhedron'][$nbPolyhedron+1]['face'][$i]['rot'] = array('x' => 1, 'y' => 1, 'z' => 1);
                        for ($j = 0; $j < $var2; $j++) {
                            $_SESSION['polyhedron'][$nbPolyhedron+1]['face'][$i]['peak'][$j] = array('x' => 1, 'y' => 1, 'z' => 1);
                        }
                    }
                }
            }
/*
            //suppression d objet
            if (isset($_POST['deleteEllipsoid']) && isset($_SESSION['ellipsoid'])) {
                for ($i = 1; $i <= $nbEllipsoid; $i++) {
                    if (isset($_POST['delete_'.$i])) {
                        while ($i < $nbEllipsoid) {
                            $i++;
                            $_SESSION['ellipsoid'][$i-1] = $_SESSION['ellipsoid'][$i];
                        }
                        unset($_SESSION['ellipsoid'][$i]);
                    }
                }
            }
            else if (isset($_POST['deletePolyedre']) && isset($_SESSION['polyhedron'])) {
                for ($i = 1; $i <= $nbPolyhedron; $i++) {
                    if (isset($_POST['delete_'.$i])) {
                        while ($i < $nbPolyhedron) {
                            $i++;
                            $_SESSION['polyhedron'][$i-1] = $_SESSION['polyhedron'][$i];
                        }
                        unset($_SESSION['polyhedron'][$i]);
                    }
                }
            }
*/
        } 
        

        //bloc 3
        if ($_POST['script'] == 'patchConfig') {
            //enregistrement des entrees : rien pour l instant
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
