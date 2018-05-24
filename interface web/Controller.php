<?php

function Edition($listWarning) {
    //gestion des blocs   
    if (!isset($_SESSION['pageBlock'], $_SESSION['file'], $_SESSION['scene'])) initialSession();//initialisation de la page
    else {//changement de bloc
        if ($_SESSION['pageBlock'] == 2) {
            if (isset($_POST['reuseData']) && $_POST['reuseData']) $_SESSION['pageBlock'] = 0;
            else initialSession();
        }
        else {
            $_SESSION['pageBlock']++;

            if ($_SESSION['pageBlock'] >= 2) {
                //creee fichiers textes
                $file = fopen('data.txt', 'w');
                if ($_SESSION == 3 && isset($_POST['saveData']) && $_POST['saveData']) {
                    $file = fopen('link/'.$_SESSION['file']['name'].'.txt', 'w');
                }

                //simplification des variables
                $detailFile = $_SESSION['file'];
                $detailScene = $_SESSION['scene'];

                //preparation ecriture en fichier
                $fileContent['title'][0] = array('Name:'."\r\n",
                                                'Height:'."\r\n",
                                                'Width:'."\r\n", 
                                                'Shadows:'."\r\n",
                                                'Antialiasing:'."\r\n",
                                                'Video:'."\r\n");
                $fileContent['detail'][0] = array($detailFile['name'], 
                                                $detailFile['dim']['x'], 
                                                $detailFile['dim']['y'], 
                                                $detailFile['effects']['shadows'],
                                                $detailFile['effects']['aliasing'],
                                                $detailFile['video']['selected']);
                                                
                $background = hex2rgb($_SESSION['scene']['color']);
                $fileContent['title'][1] = array('Background-color:'."\r\n\t",
                                                "\t",
                                                "\t", 
                                                "\t", 
                                                "\t", 
                                                "\t",
                                                'Brightness:'."\r\n",
                                                'LightPosition:'."\r\n\t",
                                                "\t",
                                                "\t", 
                                                "\t", 
                                                "\t", 
                                                "\t",
                                                'ViewerPosition:'."\r\n\t",
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
                                                $background['B'],
                                                ($detailScene['light'][0]['bright'] / 100),
                                                'x:',
                                                $detailScene['light'][0]['pos']['x'],
                                                'y:',
                                                $detailScene['light'][0]['pos']['y'], 
                                                'z:',
                                                $detailScene['light'][0]['pos']['z'],
                                                'x:',
                                                $detailScene['viewer']['x'],
                                                'y:',
                                                $detailScene['viewer']['y'], 
                                                'z:',
                                                $detailScene['viewer']['z']);
                
                //remplissage du fichier
                for ($i = 0; $i <= 1; $i++) {
                    for ($j = 0; $j < count($fileContent['title'][$i]); $j++) {
                        $string = "\r\n".$fileContent['title'][$i][$j].$fileContent['detail'][$i][$j];
                        fwrite($file, $string);
                    }
                }

                //boucle bloc ellipsoides
                $nbElli = $_POST['selectElli'];//securiser
                
                if ($nbElli == 0) {
                    $string = "\r\n".'NumberOfEllipse:'."\r\n".'0';
                    fwrite($file, $string);
                }
                else {
                    $detailElli = $_SESSION['ellipsoid'];

                    $string = "\r\n".'NumberOfEllipse:'."\r\n".$nbElli;
                    fwrite($file, $string);
                    for ($elli = 1; $elli <= $nbElli; $elli++) {
                        $string = "\r\n".'Ellipse'.$elli.':';
                        fwrite($file, $string);

                        $color = hex2rgb($detailElli[$elli]['color']);
                        $string = "\r\n\t".'Color:';
                        fwrite($file, $string);
                        $string = "\r\n\t\t".'r:'."\r\n\t\t".$color['R'];
                        fwrite($file, $string);
                        $string = "\r\n\t\t".'g:'."\r\n\t\t".$color['G'];
                        fwrite($file, $string);
                        $string = "\r\n\t\t".'b:'."\r\n\t\t".$color['B'];
                        fwrite($file, $string);

                        $string = "\r\n\t".'A:'."\r\n\t".$detailElli[$elli]['pos']['x'];
                        fwrite($file, $string);
                        $string = "\r\n\t".'B:'."\r\n\t".$detailElli[$elli]['pos']['y'];
                        fwrite($file, $string);
                        $string = "\r\n\t".'C:'."\r\n\t".$detailElli[$elli]['pos']['z'];
                        fwrite($file, $string);

                        $string = "\r\n\t".'alpha:'."\r\n\t".$detailElli[$elli]['rad']['x'];
                        fwrite($file, $string);
                        $string = "\r\n\t".'beta:'."\r\n\t".$detailElli[$elli]['rad']['y'];
                        fwrite($file, $string);
                        $string = "\r\n\t".'gamma:'."\r\n\t".$detailElli[$elli]['rad']['z'];
                        fwrite($file, $string);
                    }
                }

                //boucle bloc polyedres
                $polyhedron = array();
                foreach ($_SESSION['polyhedron'] as $poly) {
                    foreach ($poly as $face) {
                        $polyhedron[] = $face;
                    }
                }
                
                $nbFace = count($polyhedron);
                if ($nbFace == 0) {
                    $string = "\r\n".'Polyhedron:'."\r\n".'0';
                    fwrite($file, $string);
                }
                else {
                    $string = "\r\n".'Polyhedron:'."\r\n".'1'."\r\n".'Polyhedron1:';
                    fwrite($file, $string);
                    
                    $string = "\r\n\t".'NumberOfFaces:'."\r\n\t".$nbFace;
                    fwrite($file, $string);
                    
                    for ($face = 0; $face < $nbFace; $face++) {
                        $string = "\r\n\t".'Face'.($face+1).':';
                        fwrite($file, $string);

                        $color = hex2rgb($polyhedron[$face]['color']);
                        $string = "\r\n\t\t".'Color:';
                        fwrite($file, $string);
                        $string = "\r\n\t\t\t".'r:'."\r\n\t\t\t".$color['R'];
                        fwrite($file, $string);
                        $string = "\r\n\t\t\t".'g:'."\r\n\t\t\t".$color['G'];
                        fwrite($file, $string);
                        $string = "\r\n\t\t\t".'b:'."\r\n\t\t\t".$color['B'];
                        fwrite($file, $string);

                        $string = "\r\n\t\t".'isMirror:'."\r\n\t\t";
                        $string .= ($polyhedron[$face]['reflex'])? '1' : '0' ;
                        fwrite($file, $string);

                        $nbPeak = count($polyhedron[$face]['peak']);
                        $string = "\r\n\t\t".'Numberofpeaks:'."\r\n\t\t".$nbPeak;
                        fwrite($file, $string);
                        for ($peak = 1; $peak <= $nbPeak; $peak++) {
                            $string = "\r\n\t\t\t".'x'.$peak.':'."\r\n\t\t\t".$polyhedron[$face]['peak'][$peak]['x'];
                            fwrite($file, $string);
                            $string = "\r\n\t\t\t".'y'.$peak.':'."\r\n\t\t\t".$polyhedron[$face]['peak'][$peak]['y'];
                            fwrite($file, $string);
                            $string = "\r\n\t\t\t".'z'.$peak.':'."\r\n\t\t\t".$polyhedron[$face]['peak'][$peak]['z'];
                            fwrite($file, $string);   
                        }
                    }
                }

                fclose($file);

                exec("ProjetEx.exe");
            }
        }
    }

    //variables de la page
    $template['pageName'] = 'Edition d\'image';
    if ($listWarning) $template['listWarning'] = $listWarning;
    $edition['legend'] = array('Caractérisation du fichier', 'Composition du fichier', 'Validation du résultat');

    //remplissage de la page
    require('View/vEdition.php');
}
