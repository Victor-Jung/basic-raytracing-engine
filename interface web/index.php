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
        if ($_POST['script'] == 'fileConfig') {//bloc 1 : enregistrement des entrees
            $_SESSION['file']['name']               = isset($_POST['fileName']) ? filter_input(INPUT_POST, 'fileName',  FILTER_SANITIZE_STRING) : false ;
            $_SESSION['file']['video']['selected']  = isset($_POST['video'])    ? filter_input(INPUT_POST, 'video',     FILTER_VALIDATE_INT)    : null ;
            $_SESSION['file']['video']['duration']  = isset($_POST['duration']) ? filter_input(INPUT_POST, 'duration',  FILTER_VALIDATE_INT)    : false ;
            $_SESSION['file']['video']['frequency'] = isset($_POST['frequency'])? filter_input(INPUT_POST, 'frequency', FILTER_VALIDATE_INT)    : false ;
            $_SESSION['file']['video']['move']['x'] = isset($_POST['moveX'])    ? filter_input(INPUT_POST, 'moveX',     FILTER_VALIDATE_INT)    : false ;
            $_SESSION['file']['video']['move']['y'] = isset($_POST['moveY'])    ? filter_input(INPUT_POST, 'moveY',     FILTER_VALIDATE_INT)    : false ;
            $_SESSION['file']['video']['move']['z'] = isset($_POST['moveZ'])    ? filter_input(INPUT_POST, 'moveZ',     FILTER_VALIDATE_INT)    : false ;
            $_SESSION['file']['dim']['x']           = isset($_POST['dimXFile']) ? filter_input(INPUT_POST, 'dimXFile',  FILTER_VALIDATE_INT)    : false ;
            $_SESSION['file']['dim']['y']           = isset($_POST['dimYFile']) ? filter_input(INPUT_POST, 'dimYFile',  FILTER_VALIDATE_INT)    : false ;

            $_SESSION['file']['effects']['shadows']     = isset($_POST['shadows'])      ? filter_input(INPUT_POST, 'shadows',       FILTER_VALIDATE_INT)    : 0 ;
            $_SESSION['file']['effects']['aliasing']    = isset($_POST['aliasing'])     ? filter_input(INPUT_POST, 'aliasing',      FILTER_VALIDATE_INT)    : 0 ;

            $_SESSION['scene']['color']                 = isset($_POST['sceneColor'])   ? filter_input(INPUT_POST, 'sceneColor',    FILTER_SANITIZE_STRING) : false ;
            $_SESSION['scene']['viewer']['x']           = isset($_POST['viewerX'])      ? filter_input(INPUT_POST, 'viewerX',       FILTER_VALIDATE_INT)    : false ;
            $_SESSION['scene']['viewer']['y']           = isset($_POST['viewerY'])      ? filter_input(INPUT_POST, 'viewerY',       FILTER_VALIDATE_INT)    : false ;
            $_SESSION['scene']['viewer']['z']           = isset($_POST['viewerZ'])      ? filter_input(INPUT_POST, 'viewerZ',       FILTER_VALIDATE_INT)    : false ;


            $nbLight = (!isset($_SESSION['scene']['light']))? 0 : count($_SESSION['scene']['light']);
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


            if (!$_SESSION['file']['name'])                         $listWarning[] = 'nom de fichier';
            if (is_null($_SESSION['file']['video']['selected']))    $listWarning[] = 'format de fichier';
            if (!$_SESSION['file']['video']['duration'])            $listWarning[] = 'durée de la vidéo';
            if (!$_SESSION['file']['video']['frequency'])           $listWarning[] = 'images par secondes';
            if (!$_SESSION['file']['video']['move']['x'])           $listWarning[] = 'déplacement en x';
            if (!$_SESSION['file']['video']['move']['y'])           $listWarning[] = 'déplacement en y';
            if (!$_SESSION['file']['video']['move']['z'])           $listWarning[] = 'déplacement en z';
            if (!$_SESSION['file']['dim']['x'])                     $listWarning[] = 'dimension X du fichier';
            if (!$_SESSION['file']['dim']['y'])                     $listWarning[] = 'dimension Y du fichier';

            if (is_null($_SESSION['file']['effects']['shadows']))       $listWarning[] = 'Choix des ombres';
            if (is_null($_SESSION['file']['effects']['aliasing']))      $listWarning[] = 'Choix d\'anti-aliasing';

            if (!$_SESSION['scene']['color'] || !verifHexaColor($_SESSION['scene']['color']))   $listWarning[] = 'couleur de la scène';
            if (!$_SESSION['scene']['viewer']['x'])                                             $listWarning[] = 'Position x de l\'observateur';
            if (!$_SESSION['scene']['viewer']['y'])                                             $listWarning[] = 'Position y de l\'observateur';
            if (!$_SESSION['scene']['viewer']['z'])                                             $listWarning[] = 'Position z de l\'observateur';
        }

        if ($_POST['script'] == 'sceneConfig') {//bloc 2 : enregistrement des entrees
            $nbElli = filter_input(INPUT_POST, 'selectElli', FILTER_VALIDATE_INT);
            for ($elli = 1; $elli < $nbElli; $elli++) {
                $_SESSION['ellipsoid'][$elli]['color']      = isset($_POST['elli'.$elli.'_color'])  ? filter_input(INPUT_POST, 'elli'.$elli.'_color', FILTER_SANITIZE_STRING) : false ;
                $_SESSION['ellipsoid'][$elli]['pos']['x']   = isset($_POST['elli'.$elli.'_xPos'])   ? filter_input(INPUT_POST, 'elli'.$elli.'_xPos', FILTER_VALIDATE_INT)   : false ;
                $_SESSION['ellipsoid'][$elli]['pos']['y']   = isset($_POST['elli'.$elli.'_yPos'])   ? filter_input(INPUT_POST, 'elli'.$elli.'_yPos', FILTER_VALIDATE_INT)   : false ;
                $_SESSION['ellipsoid'][$elli]['pos']['z']   = isset($_POST['elli'.$elli.'_zPos'])   ? filter_input(INPUT_POST, 'elli'.$elli.'_zPos', FILTER_VALIDATE_INT)   : false ;
                $_SESSION['ellipsoid'][$elli]['rad']['x']   = isset($_POST['elli'.$elli.'_xRad'])   ? filter_input(INPUT_POST, 'elli'.$elli.'_xRad', FILTER_VALIDATE_INT)   : false ;
                $_SESSION['ellipsoid'][$elli]['rad']['y']   = isset($_POST['elli'.$elli.'_yRad'])   ? filter_input(INPUT_POST, 'elli'.$elli.'_yRad', FILTER_VALIDATE_INT)   : false ;
                $_SESSION['ellipsoid'][$elli]['rad']['z']   = isset($_POST['elli'.$elli.'_zRad'])   ? filter_input(INPUT_POST, 'elli'.$elli.'_zRad', FILTER_VALIDATE_INT)   : false ;

                if (!$_SESSION['ellipsoid'][$elli]['color'])    $listWarning[] = 'Couleur de l\'objet'.($elli+1);
                if (!$_SESSION['ellipsoid'][$elli]['pos']['x']) $listWarning[] = 'Position x du centre de l\'ellipsoïde '.($elli+1);
                if (!$_SESSION['ellipsoid'][$elli]['pos']['y']) $listWarning[] = 'Position y du centre de l\'ellipsoïde '.($elli+1);
                if (!$_SESSION['ellipsoid'][$elli]['pos']['z']) $listWarning[] = 'Position z du centre de l\'ellipsoïde '.($elli+1);
                if (!$_SESSION['ellipsoid'][$elli]['rad']['x']) $listWarning[] = 'Rayon x de l\'ellipsoïde '.($elli+1);
                if (!$_SESSION['ellipsoid'][$elli]['rad']['y']) $listWarning[] = 'Rayon y de l\'ellipsoïde '.($elli+1);
                if (!$_SESSION['ellipsoid'][$elli]['rad']['z']) $listWarning[] = 'Rayon z de l\'ellipsoïde '.($elli+1);
            }

            $nbPoly = filter_input(INPUT_POST, 'selectPoly', FILTER_VALIDATE_INT);
            for ($poly = 1; $poly < $nbPoly; $poly++) {
                $_SESSION['polyhedron'][$poly]['color']      = isset($_POST['poly'.$poly.'_color'])  ? filter_input(INPUT_POST, 'poly'.$poly.'_color', FILTER_SANITIZE_STRING) : false ;
                $_SESSION['polyhedron'][$poly]['pos']['x']   = isset($_POST['poly'.$poly.'_xPos'])   ? filter_input(INPUT_POST, 'poly'.$poly.'_xPos', FILTER_VALIDATE_INT)   : false ;
                $_SESSION['polyhedron'][$poly]['pos']['y']   = isset($_POST['poly'.$poly.'_yPos'])   ? filter_input(INPUT_POST, 'poly'.$poly.'_yPos', FILTER_VALIDATE_INT)   : false ;
                $_SESSION['polyhedron'][$poly]['pos']['z']   = isset($_POST['poly'.$poly.'_zPos'])   ? filter_input(INPUT_POST, 'poly'.$poly.'_zPos', FILTER_VALIDATE_INT)   : false ;
                $_SESSION['polyhedron'][$poly]['rad']['x']   = isset($_POST['poly'.$poly.'_xRad'])   ? filter_input(INPUT_POST, 'poly'.$poly.'_xRad', FILTER_VALIDATE_INT)   : false ;
                $_SESSION['polyhedron'][$poly]['rad']['y']   = isset($_POST['poly'.$poly.'_yRad'])   ? filter_input(INPUT_POST, 'poly'.$poly.'_yRad', FILTER_VALIDATE_INT)   : false ;
                $_SESSION['polyhedron'][$poly]['rad']['z']   = isset($_POST['poly'.$poly.'_zRad'])   ? filter_input(INPUT_POST, 'poly'.$poly.'_zRad', FILTER_VALIDATE_INT)   : false ;

                if (!$_SESSION['polyhedron'][$poly]['color'])    $listWarning[] = 'Couleur de la face';
                if (!$_SESSION['polyhedron'][$poly]['pos']['x']) $listWarning[] = 'Position x '.$poly;
                if (!$_SESSION['polyhedron'][$poly]['pos']['y']) $listWarning[] = 'Position y '.$poly;
                if (!$_SESSION['polyhedron'][$poly]['pos']['z']) $listWarning[] = 'Position z '.$poly;
                if (!$_SESSION['polyhedron'][$poly]['rad']['x']) $listWarning[] = 'Rayon x de '.$poly;
                if (!$_SESSION['polyhedron'][$poly]['rad']['y']) $listWarning[] = 'Rayon y de '.$poly;
                if (!$_SESSION['polyhedron'][$poly]['rad']['z']) $listWarning[] = 'Rayon z de '.$poly;
            }
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
