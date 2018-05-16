<?php
require('Model.php');


function Edition() {
    //partie gestion des blocs form
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

    //partie gestion du contenu des blocs form
    if (isset($_POST['script'])) {
        //bloc 1
        if ($_POST['script'] == 'fileConfig') {
            //enregistrement des entrees
            if (!isset($_POST['fileName']) || !isset($_POST['typeFile']) ||
            !isset($_POST['duration']) || !isset($_POST['frequency']) ||
            !isset($_POST['dimXFile']) || !isset($_POST['dimYFile']) || !isset($_POST['dimZFile'])) {
                $template['listWarning'][] = 'données formulaire';
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


            if (!$_SESSION['edit']['dataFile']['name'])   $template['listWarning'][] = 'nom de fichier';
            if (!$_SESSION['edit']['dataFile']['format']) $template['listWarning'][] = 'format de fichier';
            if (!$_SESSION['edit']['dataFile']['video']['duration'])  $template['listWarning'][] = 'durée de la vidéo';
            if (!$_SESSION['edit']['dataFile']['video']['frequency']) $template['listWarning'][] = 'images par secondes';
            if (!$_SESSION['edit']['dataFile']['dimX']) $template['listWarning'][] = 'dimension X du fichier';
            if (!$_SESSION['edit']['dataFile']['dimY']) $template['listWarning'][] = 'dimension Y du fichier';
            if (!$_SESSION['edit']['dataFile']['dimZ']) $template['listWarning'][] = 'dimension Z du fichier';
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
                if (!isset($_POST['bright']) || !isset($_POST['backgroundColor'])) {
                    $template['listWarning'][] = 'données formulaire';
                    $_SESSION['edit']['step'] = 2;
                    //throw new Exception("Connexion : Données formulaire incomplètes");
                }

                $_SESSION['edit']['dataScene']['bright'] = filter_input(INPUT_POST, 'bright', FILTER_VALIDATE_INT);
                $_SESSION['edit']['dataScene']['backgroundColor'] = filter_input(INPUT_POST, 'backgroundColor', FILTER_SANITIZE_STRING);

                if (!$_SESSION['edit']['dataScene']['bright']) $template['listWarning'][] = 'luminosité de la scène';
                if (!$_SESSION['edit']['dataScene']['backgroundColor']) $template['listWarning'][] = 'couleur de fond de la scène';

                $i = 0;
                while ($i < $nbObjects) {
                    $i++;

                    if (!isset($_POST['color'.$i]) || ($_POST['name'.$i] == 'Sphère' && !isset($_POST['radius'.$i])) ||
                    !isset($_POST['posX'.$i], $_POST['posY'.$i], $_POST['posZ'.$i]) ||
                    ($_POST['name'.$i] != 'Sphère' && isset($_POST['rotX'.$i], $_POST['rotY'.$i], $_POST['rotZ'.$i])) &&
                    (!isset($_POST['dimX'.$i], $_POST['dimY'.$i]) && ($_POST['name'.$i] != 'Surface' && !isset($_POST['dimZ'.$i])))) {
                        $template['listWarning'][] = 'données formulaire objets';
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


                    if (!$_SESSION['edit']['dataScene']['shape'][$i]['color']) $template['listWarning'][] = 'objet '.$i.' : couleur';
                    
                    if ($_POST['name'.$i] == 'Sphère') {
                        if (!$_SESSION['edit']['dataScene']['shape'][$i]['radius']) $template['listWarning'][] = 'objet '.$i.' : rayon';
                    }
                    else {
                        if (!$_SESSION['edit']['dataScene']['shape'][$i]['dim']['xAxis']) $template['listWarning'][] = 'objet '.$i.' : dimension X';
                        if (!$_SESSION['edit']['dataScene']['shape'][$i]['dim']['yAxis']) $template['listWarning'][] = 'objet '.$i.' : dimension Y';
                        if ($_POST['name'.$i] != 'Surface') {
                            if (!$_SESSION['edit']['dataScene']['shape'][$i]['dim']['zAxis']) $template['listWarning'][] = 'objet '.$i.' : dimension Z';
                        }
                    }
                    
                    if (!$_SESSION['edit']['dataScene']['shape'][$i]['pos']['xAxis']) $template['listWarning'][] = 'objet '.$i.' : position X';
                    if (!$_SESSION['edit']['dataScene']['shape'][$i]['pos']['yAxis']) $template['listWarning'][] = 'objet '.$i.' : position Y';
                    if (!$_SESSION['edit']['dataScene']['shape'][$i]['pos']['zAxis']) $template['listWarning'][] = 'objet '.$i.' : position Z';

                    if ($_SESSION['edit']['dataScene']['shape'][$i]['rot']['xAxis'] === false) $template['listWarning'][] = 'objet '.$i.' : rotation X';
                    if ($_SESSION['edit']['dataScene']['shape'][$i]['rot']['yAxis'] === false) $template['listWarning'][] = 'objet '.$i.' : rotation Y';
                    if ($_SESSION['edit']['dataScene']['shape'][$i]['rot']['zAxis'] === false) $template['listWarning'][] = 'objet '.$i.' : rotation Z';
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

        //fin d'exploitation : 'detruit' la variable
        $_POST['script'] = false;
    }


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