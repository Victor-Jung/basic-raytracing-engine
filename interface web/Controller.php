<?php

function Edition($listWarning) {
    //gestion des blocs form
    {
        //changement de bloc
        if (isset($_SESSION['pageBlock'], $_POST['nextStep']) && $_POST['nextStep']) {
            if ($_SESSION['pageBlock'] < 3) {
                if ($_SESSION['pageBlock'] == 2) {
                    //creee fichiers textes
                    $file = fopen('link/data.txt', 'a');
 
                    //simplification des variables
                    $detailFile = $_SESSION;
                    $detailScene = $_SESSION;

                    //part 0
                    $fileContent['title'][0] = array('Name:'."\r"."\n",
                                                    'Height:'."\r"."\n",
                                                    'Width:'."\r"."\n", 
                                                    'Brightness:'."\r"."\n");
                    $fileContent['detail'][0] = array($detailFile['name'], 
                                                    $detailFile['dimX'], 
                                                    $detailFile['dimY'], 
                                                    ($detailScene['brightScene'] / 100));
                    //part 1
                    $background = hex2rgb($_SESSION['backgroundColor']);
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
                    if (isset($_SESSION['shape'])) {
                        foreach ($_SESSION['shape'] as $shape) {
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
                $_SESSION['pageBlock']++;
            }
            else {
                if (isset($_POST['reuseData']) && $_POST['reuseData']) {
                    $_SESSION['pageBlock'] = 1;
                }
                else {
                    unset($_SESSION['pageBlock']);
                    unset($_SESSION);
                }
            }
        }

        //initialisation de la page (premier bloc)
        if (!isset($_SESSION['pageBlock'])) {
            $_SESSION['pageBlock'] = 1;

            $_SESSION['name'] = 'temporaire';
            $_SESSION['format'] = 'BMP';
            $_SESSION['dimX'] = 768;
            $_SESSION['dimY'] = 768;
            $_SESSION['dimZ'] = MAX_Z_IMG;
            $_SESSION['video']['duration'] = MAX_DURATION;
            $_SESSION['video']['frequency'] = 1;

            $_SESSION['brightScene'] = 100;
            $_SESSION['backgroundColor'] = '#80D4FF';
            //ajouter aux formulaires
            $_SESSION['lightPosition']['x'] = -3;
            $_SESSION['lightPosition']['y'] = 5;
            $_SESSION['lightPosition']['z'] = 3;
            $_SESSION['viewerPosition']['x'] = -10;
            $_SESSION['viewerPosition']['y'] = -3;
            $_SESSION['viewerPosition']['z'] = -2;
        }
    }

    //variables du template
    if ($listWarning) $template['listWarning'] = $listWarning;
    $template['pageName'] = 'Edition d\'image';
    $template['actual'] = 'edit';

    //variables de la page
    $edition['legend'][1] = 'Caractérisation du fichier';
    $edition['legend'][2] = 'Composition du fichier';
    $edition['legend'][3] = 'Validation du résultat';

    //remplissage de la page
    require('View/vEdition.php');
}
