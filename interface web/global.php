<?php
define('RESOLUTION_LEVELS', 16);
define('LENGTH_NAME', 20);
define('MAX_Z_IMG', 1000);
define('STEP_AXIS', 1);


function initialSession() {
    $_SESSION['pageBlock'] = 0;

    $_SESSION['file']['name'] = 'temporaire';
    $_SESSION['file']['dim']['x'] = 1024;
    $_SESSION['file']['dim']['y'] = 1024;
    $_SESSION['file']['video']['selected'] = 0;
    $_SESSION['file']['video']['frames'] = 10;
    $_SESSION['file']['video']['move']['x'] = 0;
    $_SESSION['file']['video']['move']['y'] = 0;
    $_SESSION['file']['video']['move']['z'] = 0;
    $_SESSION['file']['effects']['shadows'] = 1;
    $_SESSION['file']['effects']['aliasing'] = 1;

    $_SESSION['scene']['color'] = '#000000';
    $_SESSION['scene']['light'][0]['bright'] = 100;
    $_SESSION['scene']['light'][0]['pos'] = array('x' => 0, 'y' => 0, 'z' => 0);
    $_SESSION['scene']['viewer'] = array('x' => 0, 'y' => 0, 'z' => 0);

    $_SESSION['ellipsoid'] = array();
    $_SESSION['polyhedron'] = array();
}

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

function createFile($file) {
    //simplification des variables
    $detailFile = $_SESSION['file'];
    $detailScene = $_SESSION['scene'];

    //preparation ecriture en fichier
    $fileContent['title'][] = array('Name:'."\r\n",
                                    'Height:'."\r\n",
                                    'Width:'."\r\n", 
                                    'Shadows:'."\r\n",
                                    'Antialiasing:'."\r\n",
                                    'Video:'."\r\n");
    $fileContent['detail'][] = array($detailFile['name'], 
                                    $detailFile['dim']['x'], 
                                    $detailFile['dim']['y'], 
                                    $detailFile['effects']['shadows'],
                                    $detailFile['effects']['aliasing'],
                                    $detailFile['video']['selected']);
    if ($detailFile['video']['selected'] != 0) {
        $fileContent['title'][] = array("\t".'NumberofFrames:'."\r\n\t",
                                        "\t\t".'x:'."\r\n\t\t",
                                        "\t\t".'y:'."\r\n\t\t",
                                        "\t\t".'z:'."\r\n\t\t");
        $fileContent['detail'][] = array($detailFile['video']['frames'],
                                        $detailFile['video']['move']['x'],
                                        $detailFile['video']['move']['y'],
                                        $detailFile['video']['move']['z']);
    }
                                    
    $background = hex2rgb($_SESSION['scene']['color']);
    $fileContent['title'][] = array('Background-color:'."\r\n\t",
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
    $fileContent['detail'][] = array('r:', 
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
    $nbPart = count($fileContent['title']);
    for ($i = 0; $i < $nbPart; $i++) {
        for ($j = 0; $j < count($fileContent['title'][$i]); $j++) {
            $string = "\r\n".$fileContent['title'][$i][$j].$fileContent['detail'][$i][$j];
            fwrite($file, $string);
        }
    }

    //boucle bloc ellipsoides
    $nbElli = isset($_POST['selectElli'])? $_POST['selectElli'] : 0;//securiser
    
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
}