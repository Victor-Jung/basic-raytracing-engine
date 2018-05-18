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


function presetEdition() {
    $listWarning = false;

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
                
                //ajouter aux formulaires
                for ($i = 0; $i < 6; $i++) {//6 faces pour le pave
                    $_SESSION['edit']['dataScene']['shape'][$nbObjects+1]['faces'][$i]['color'] = '#ffffff';
                }
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

    //gestion des blocs form
    {
        //changement de bloc
        if (isset($_SESSION['edit']['step'], $_POST['nextStep']) && $_POST['nextStep']) {
            if ($_SESSION['edit']['step'] < 3) {
                if ($_SESSION['edit']['step'] == 2){
                    //creee fichiers textes
                    $file = fopen('link/data.txt', 'a');
 
                    //simplification des variables
                    $detailFile = $_SESSION['edit']['dataFile'];
                    $detailScene = $_SESSION['edit']['dataScene'];

                    //part 0
                    $fileContent['title'][0] = array('Name:'."\r"."\n",
                                                    'Height:'."\r"."\n",
                                                    'Width:'."\r"."\n", 
                                                    'Brightness:'."\r"."\n");
                    $fileContent['detail'][0] = array($detailFile['name'], 
                                                    $detailFile['dimX'], 
                                                    $detailFile['dimY'], 
                                                    ($detailScene['bright'] / 100));
                    //part 1
                    $background = hex2rgb($_SESSION['edit']['dataScene']['backgroundColor']);
                    $fileContent['title'][1] = array('Background-color:'."\r"."\n"."\t",
                                                    "\t",
                                                    "\t", 
                                                    "\t", 
                                                    "\t", 
                                                    "\t");
                    $fileContent['detail'][1] = array('r:', 
                                                    $background['R'], 
                                                    'g:',
                                                    $background['G'], 
                                                    'b:',
                                                    $background['B']);
                    //part 2
                    $fileContent['title'][2] = array('LightPosition:'."\r"."\n"."\t",
                                                    "\t",
                                                    "\t", 
                                                    "\t", 
                                                    "\t", 
                                                    "\t");
                    $fileContent['detail'][2] = array('x:', 
                                                    $detailScene['lightPosition']['x'], 
                                                    'y:',
                                                    $detailScene['lightPosition']['y'], 
                                                    'z:',
                                                    $detailScene['lightPosition']['z']);
                    //part 3
                    $fileContent['title'][3] = array('ViewerPosition:'."\r"."\n"."\t",
                                                    "\t",
                                                    "\t", 
                                                    "\t", 
                                                    "\t", 
                                                    "\t");
                    $fileContent['detail'][3] = array('x:', 
                                                    $detailScene['viewerPosition']['x'], 
                                                    'y:',
                                                    $detailScene['viewerPosition']['y'], 
                                                    'z:',
                                                    $detailScene['viewerPosition']['z']);

                    //boucle bloc fichier
                    for ($i = 0; $i < 4; $i++) {
                        for ($j = 0; $j < count($fileContent['title'][$i]); $j++) {
                            $string = "\r"."\n".$fileContent['title'][$i][$j].$fileContent['detail'][$i][$j];
                            fwrite($file, $string);
                        }
                    }


                    //simplification des variables
                    $nbPoly = 0;
                    $nbElli = 0;
                    if (isset($_SESSION['edit']['dataScene']['shape'])) {
                        foreach ($_SESSION['edit']['dataScene']['shape'] as $shape) {
                            if ($shape['name'] != 'Sphère' && $shape['name'] != 'Ellipsoïde') {
                                $nbPoly++;
                                $polyhedron[] = $shape;
                                $polyhedron[$nbPoly-1]['id'] = $nbPoly;

                                switch ($shape['name']) {
                                    case 'Surface':
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][1]['x'] = $shape['pos']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][1]['y'] = $shape['pos']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][1]['z'] = $shape['pos']['zAxis'];
                                        
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][2]['x'] = $shape['pos']['xAxis'] + $shape['dim']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][2]['y'] = $shape['pos']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][2]['z'] = $shape['pos']['zAxis'];

                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][3]['x'] = $shape['pos']['xAxis'] + $shape['dim']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][3]['y'] = $shape['pos']['yAxis'] + $shape['dim']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][3]['z'] = $shape['pos']['zAxis'];

                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][4]['x'] = $shape['pos']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][4]['y'] = $shape['pos']['yAxis'] + $shape['dim']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][4]['z'] = $shape['pos']['zAxis'];
                                    break;
                                    case 'Pavé': 
                                        //premiere face (devant : z fixe)
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][1]['x'] = $shape['pos']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][1]['y'] = $shape['pos']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][1]['z'] = $shape['pos']['zAxis'];
                                        
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][2]['x'] = $shape['pos']['xAxis'] + $shape['dim']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][2]['y'] = $shape['pos']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][2]['z'] = $shape['pos']['zAxis'];

                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][3]['x'] = $shape['pos']['xAxis'] + $shape['dim']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][3]['y'] = $shape['pos']['yAxis'] + $shape['dim']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][3]['z'] = $shape['pos']['zAxis'];

                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][4]['x'] = $shape['pos']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][4]['y'] = $shape['pos']['yAxis'] + $shape['dim']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][1]['peaks'][4]['z'] = $shape['pos']['zAxis'];

                                        //seconde face (derriere : z fixe)
                                        $polyhedron[$nbPoly-1]['faces'][2]['peaks'][1]['x'] = $shape['pos']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][2]['peaks'][1]['y'] = $shape['pos']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][2]['peaks'][1]['z'] = $shape['pos']['zAxis'] + $shape['dim']['zAxis'];
                                        
                                        $polyhedron[$nbPoly-1]['faces'][2]['peaks'][2]['x'] = $shape['pos']['xAxis'] + $shape['dim']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][2]['peaks'][2]['y'] = $shape['pos']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][2]['peaks'][2]['z'] = $shape['pos']['zAxis'] + $shape['dim']['zAxis'];

                                        $polyhedron[$nbPoly-1]['faces'][2]['peaks'][3]['x'] = $shape['pos']['xAxis'] + $shape['dim']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][2]['peaks'][3]['y'] = $shape['pos']['yAxis'] + $shape['dim']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][2]['peaks'][3]['z'] = $shape['pos']['zAxis'] + $shape['dim']['zAxis'];

                                        $polyhedron[$nbPoly-1]['faces'][2]['peaks'][4]['x'] = $shape['pos']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][2]['peaks'][4]['y'] = $shape['pos']['yAxis'] + $shape['dim']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][2]['peaks'][4]['z'] = $shape['pos']['zAxis'] + $shape['dim']['zAxis'];

                                        //troisieme face (gauche : x fixe)
                                        $polyhedron[$nbPoly-1]['faces'][3]['peaks'][1]['x'] = $shape['pos']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][3]['peaks'][1]['y'] = $shape['pos']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][3]['peaks'][1]['z'] = $shape['pos']['zAxis'];
                                        
                                        $polyhedron[$nbPoly-1]['faces'][3]['peaks'][2]['x'] = $shape['pos']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][3]['peaks'][2]['y'] = $shape['pos']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][3]['peaks'][2]['z'] = $shape['pos']['zAxis'] + $shape['dim']['zAxis'];

                                        $polyhedron[$nbPoly-1]['faces'][3]['peaks'][3]['x'] = $shape['pos']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][3]['peaks'][3]['y'] = $shape['pos']['yAxis'] + $shape['dim']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][3]['peaks'][3]['z'] = $shape['pos']['zAxis'] + $shape['dim']['zAxis'];

                                        $polyhedron[$nbPoly-1]['faces'][3]['peaks'][4]['x'] = $shape['pos']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][3]['peaks'][4]['y'] = $shape['pos']['yAxis'] + $shape['dim']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][3]['peaks'][4]['z'] = $shape['pos']['zAxis'];

                                        //quatieme face (droite : x fixe)
                                        $polyhedron[$nbPoly-1]['faces'][4]['peaks'][1]['x'] = $shape['pos']['xAxis'] + $shape['dim']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][4]['peaks'][1]['y'] = $shape['pos']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][4]['peaks'][1]['z'] = $shape['pos']['zAxis'];
                                    
                                        $polyhedron[$nbPoly-1]['faces'][4]['peaks'][2]['x'] = $shape['pos']['xAxis'] + $shape['dim']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][4]['peaks'][2]['y'] = $shape['pos']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][4]['peaks'][2]['z'] = $shape['pos']['zAxis'] + $shape['dim']['zAxis'];

                                        $polyhedron[$nbPoly-1]['faces'][4]['peaks'][3]['x'] = $shape['pos']['xAxis'] + $shape['dim']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][4]['peaks'][3]['y'] = $shape['pos']['yAxis'] + $shape['dim']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][4]['peaks'][3]['z'] = $shape['pos']['zAxis'] + $shape['dim']['zAxis'];

                                        $polyhedron[$nbPoly-1]['faces'][4]['peaks'][4]['x'] = $shape['pos']['xAxis'] + $shape['dim']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][4]['peaks'][4]['y'] = $shape['pos']['yAxis'] + $shape['dim']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][4]['peaks'][4]['z'] = $shape['pos']['zAxis'];

                                        //cinquieme face (dessus : y fixe)
                                        $polyhedron[$nbPoly-1]['faces'][5]['peaks'][1]['x'] = $shape['pos']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][5]['peaks'][1]['y'] = $shape['pos']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][5]['peaks'][1]['z'] = $shape['pos']['zAxis'];
                                        
                                        $polyhedron[$nbPoly-1]['faces'][5]['peaks'][2]['x'] = $shape['pos']['xAxis'] + $shape['dim']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][5]['peaks'][2]['y'] = $shape['pos']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][5]['peaks'][2]['z'] = $shape['pos']['zAxis'];

                                        $polyhedron[$nbPoly-1]['faces'][5]['peaks'][3]['x'] = $shape['pos']['xAxis'] + $shape['dim']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][5]['peaks'][3]['y'] = $shape['pos']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][5]['peaks'][3]['z'] = $shape['pos']['zAxis'] + $shape['dim']['zAxis'];

                                        $polyhedron[$nbPoly-1]['faces'][5]['peaks'][4]['x'] = $shape['pos']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][5]['peaks'][4]['y'] = $shape['pos']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][5]['peaks'][4]['z'] = $shape['pos']['zAxis'] + $shape['dim']['zAxis'];

                                        //sixieme face (dessous : y fixe)
                                        $polyhedron[$nbPoly-1]['faces'][6]['peaks'][1]['x'] = $shape['pos']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][6]['peaks'][1]['y'] = $shape['pos']['yAxis'] + $shape['dim']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][6]['peaks'][1]['z'] = $shape['pos']['zAxis'];
                                        
                                        $polyhedron[$nbPoly-1]['faces'][6]['peaks'][2]['x'] = $shape['pos']['xAxis'] + $shape['dim']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][6]['peaks'][2]['y'] = $shape['pos']['yAxis'] + $shape['dim']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][6]['peaks'][2]['z'] = $shape['pos']['zAxis'];

                                        $polyhedron[$nbPoly-1]['faces'][6]['peaks'][3]['x'] = $shape['pos']['xAxis'] + $shape['dim']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][6]['peaks'][3]['y'] = $shape['pos']['yAxis'] + $shape['dim']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][6]['peaks'][3]['z'] = $shape['pos']['zAxis'] + $shape['dim']['zAxis'];

                                        $polyhedron[$nbPoly-1]['faces'][6]['peaks'][4]['x'] = $shape['pos']['xAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][6]['peaks'][4]['y'] = $shape['pos']['yAxis'] + $shape['dim']['yAxis'];
                                        $polyhedron[$nbPoly-1]['faces'][6]['peaks'][4]['z'] = $shape['pos']['zAxis'] + $shape['dim']['zAxis'];

                                    break;
                                }
                            }
                            else {
                                $nbElli++;
                                $ellipsoids[] = $shape;
                                $ellipsoids[$nbElli-1]['id'] = $nbElli;
                            }
                        }
                    }

                    //boucle bloc polyedres
                    if (!isset($polyhedron)) {
                        $string = "\r"."\n".'Polyhedron:'."\r"."\n".'0';
                        fwrite($file, $string);
                    }
                    else {
                        $string = "\r"."\n".'Polyhedron:'."\r"."\n".'1';
                        fwrite($file, $string);
                        foreach ($polyhedron as $object) {
                            $string = "\r"."\n".'Polyedron'.$object['id'].':';
                            fwrite($file, $string);
                            
                            $string = "\r"."\n"."\t".'NumberOfFaces:'."\r"."\n"."\t".count($object['faces']);
                            fwrite($file, $string);
                            $countFace = 0;
                            foreach ($object['faces'] as $face) {
                                $countFace++;
                                $string = "\r"."\n"."\t"."\t".'Face'.$countFace.':';
                                fwrite($file, $string);

                                $color = hex2rgb($object['color']);
                                $string = "\r"."\n"."\t"."\t"."\t".'Color:';
                                fwrite($file, $string);
                                $string = "\r"."\n"."\t"."\t"."\t"."\t".'r:';
                                fwrite($file, $string);
                                $string = "\r"."\n"."\t"."\t"."\t"."\t".$color['R'];
                                fwrite($file, $string);
                                $string = "\r"."\n"."\t"."\t"."\t"."\t".'g:';
                                fwrite($file, $string);
                                $string = "\r"."\n"."\t"."\t"."\t"."\t".$color['G'];
                                fwrite($file, $string);
                                $string = "\r"."\n"."\t"."\t"."\t"."\t".'b:';
                                fwrite($file, $string);
                                $string = "\r"."\n"."\t"."\t"."\t"."\t".$color['B'];
                                fwrite($file, $string);
                                
                                $string = "\r"."\n"."\t"."\t"."\t".'Numberofpeaks:';
                                fwrite($file, $string);
                                $string = "\r"."\n"."\t"."\t"."\t".count($face['peaks']);
                                fwrite($file, $string);
                                $countPeak = 0;
                                foreach ($face['peaks'] as $peak) {
                                    $countPeak++;
                                    $string = "\r"."\n"."\t"."\t"."\t".'x'.$countPeak.':';
                                    fwrite($file, $string);
                                    $string = "\r"."\n"."\t"."\t"."\t".$peak['x'];
                                    fwrite($file, $string);
                                    $string = "\r"."\n"."\t"."\t"."\t".'y'.$countPeak.':';
                                    fwrite($file, $string);
                                    $string = "\r"."\n"."\t"."\t"."\t".$peak['y'];
                                    fwrite($file, $string);
                                    $string = "\r"."\n"."\t"."\t"."\t".'z'.$countPeak.':';
                                    fwrite($file, $string);
                                    $string = "\r"."\n"."\t"."\t"."\t".$peak['z'];
                                    fwrite($file, $string);
                                }
                            }
                        }
                    }

                    //boucle bloc ellipsoides
                    if (!isset($ellipsoids)) {
                        $string = "\r"."\n".'NumberOfSpheres:'."\r"."\n".'0';
                        fwrite($file, $string);
                    }
                    else {
                        $string = "\r"."\n".'NumberOfSpheres:'."\r"."\n".'1';
                        fwrite($file, $string);
                        foreach ($ellipsoids as $object) {
                            $string = "\r"."\n".'Object'.$object['id'].':'."\r"."\n";
                            fwrite($file, $string);

                        }
                    }
                    

                    fclose($file);
                    
                    //createTextFiles(); -> les fichiers objets
                }
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
            //ajouter aux formulaires
            $_SESSION['edit']['dataScene']['lightPosition']['x'] = -3;
            $_SESSION['edit']['dataScene']['lightPosition']['y'] = 5;
            $_SESSION['edit']['dataScene']['lightPosition']['z'] = 3;
            $_SESSION['edit']['dataScene']['viewerPosition']['x'] = -10;
            $_SESSION['edit']['dataScene']['viewerPosition']['y'] = -3;
            $_SESSION['edit']['dataScene']['viewerPosition']['z'] = -2;
        }
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