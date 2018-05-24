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
                $file = fopen('link/data.txt', 'w');
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


                //simplification des variables
                $nbElli = isset($_SESSION['ellipsoid'])? count($_SESSION['ellipsoid']) : 0 ;
                $nbPoly = isset($_SESSION['polyhedron'])? count($_SESSION['polyhedron']) : 0 ;

                //boucle bloc ellipsoides
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


                $string = "\r\n".'Polyhedron:'."\r\n".'0';
                fwrite($file, $string);
/*
                //boucle bloc polyedres
                if ($nbPoly == 0) {
                    $string = "\r\n".'NumberOfSpheres:'."\r\n".'0';
                    fwrite($file, $string);
                }
                else {
                    $detailPoly = $_SESSION['polyhedron'];
                    
                    $string = "\r\n".'NumberOfSpheres:'."\r\n".'1';
                    fwrite($file, $string);
                    foreach ($ellipsoids as $object) {
                        $string = "\r\n".'Object'.$object['id'].':'."\r\n";
                        fwrite($file, $string);

                    }
                }
*/
                fclose($file);

                exec('Link\ProjetEx.exe');
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
