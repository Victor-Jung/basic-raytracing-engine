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
            /*
                fileName -> $_SESSION['edit']['dataFile']['name']
                dimXFile -> $_SESSION['edit']['dataFile']['dimX']
                dimYFile -> $_SESSION['edit']['dataFile']['dimY']
                dimZFile -> $_SESSION['edit']['dataFile']['dimZ']

                typeFile -> $_SESSION['edit']['dataFile']['format']
                duration -> $_SESSION['edit']['dataFile']['video']['duration']
                frequency-> $_SESSION['edit']['dataFile']['video']['frequency']
            */
        } 

        //bloc 2
        if ($_POST['script'] == 'sceneConfig') {
            //enregistrement des entrees
            /*
                bright -> $_SESSION['edit']['dataScene']['bright']
                backgroundColor -> $_SESSION['edit']['dataScene']['backgroundColor']

                color<id> -> $_SESSION['edit']['dataScene']['shape'][<id>]['color']

                posX<id> -> $_SESSION['edit']['dataScene']['shape'][<id>]['pos']['xAxis']
                posY<id> -> $_SESSION['edit']['dataScene']['shape'][<id>]['pos']['yAxis']
                posZ<id> -> $_SESSION['edit']['dataScene']['shape'][<id>]['pos']['zAxis']

                rotX<id> -> $_SESSION['edit']['dataScene']['shape'][<id>]['rot']['xAxis']
                rotY<id> -> $_SESSION['edit']['dataScene']['shape'][<id>]['rot']['yAxis']
                rotZ<id> -> $_SESSION['edit']['dataScene']['shape'][<id>]['rot']['zAxis']

                sphère : 
                    radius<id> -> $_SESSION['edit']['dataScene']['shape'][<id>]['radius']
                autre :
                    dimX<id> -> $_SESSION['edit']['dataScene']['shape'][<id>]['dim']['xAxis']
                    dimY<id> -> $_SESSION['edit']['dataScene']['shape'][<id>]['dim']['yAxis']
                    dimZ<id> -> $_SESSION['edit']['dataScene']['shape'][<id>]['dim']['zAxis']
            */

            //initialisation d objet
            if (isset($_POST['confirmShape']) && $_POST['confirmShape'] == 'Confirmer' && $_POST['shape'] != 'Aucun') {
                if (!isset($_SESSION['edit']['dataScene']['shape'])) {
                    $nbShape = 1;
                }
                else {
                    $nbShape = count($_SESSION['edit']['dataScene']['shape']) + 1;
                }

                $_SESSION['edit']['dataScene']['shape'][$nbShape]['name'] = $_POST['shape'];
                $_SESSION['edit']['dataScene']['shape'][$nbShape]['id'] = $nbShape;
                $_SESSION['edit']['dataScene']['shape'][$nbShape]['color'] = '#ffffff';

                if ($_SESSION['edit']['dataScene']['shape'][$nbShape]['name'] == 'Sphère') {
                    $_SESSION['edit']['dataScene']['shape'][$nbShape]['radius'] = 1;
                }
                else {
                    $_SESSION['edit']['dataScene']['shape'][$nbShape]['dim'] = array('xAxis' => 1, 'yAxis' => 1, 'zAxis' => 1);
                }
                $_SESSION['edit']['dataScene']['shape'][$nbShape]['pos'] = array('xAxis' => $_SESSION['edit']['dataFile']['dimX']-1, 
                                                                                'yAxis' => $_SESSION['edit']['dataFile']['dimY']-1, 
                                                                                'zAxis' => $_SESSION['edit']['dataFile']['dimZ']);
                $_SESSION['edit']['dataScene']['shape'][$nbShape]['rot'] = array('xAxis' => 0, 'yAxis' => 0, 'zAxis' => 0);
                
            }

            //suppression d objet
            if (isset($_SESSION['edit']['dataScene']['shape'])) {
                $nbObjects = count($_SESSION['edit']['dataScene']['shape']);

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
    }

    unset($_POST);//fin d'exploitation : vide la variable





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