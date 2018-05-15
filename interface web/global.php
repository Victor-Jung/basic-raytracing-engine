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

    if (!isset($rgb['R']) || !isset($rgb['G']) || !isset($rgb['B']) ||
        $rgb['R'] < 0 || $rgb['R'] > 255 ||
        $rgb['G'] < 0 || $rgb['G'] > 255 ||
        $rgb['B'] < 0 || $rgb['B'] > 255) {
        return false;
    }
    
    return true;
}


function verifScript($scriptWanted) {
    if (!isset($_POST['script']) || $_POST['script'] != $scriptWanted) {
        return $_POST['script'] = false;
    }

    switch ($scriptWanted) {
        case 'edit':
            if (isset($_POST['fileName']) &&
                isset($_POST['dimXImg']) && isset($_POST['dimYImg']) &&
                isset($_POST['backgroundColor']) &&
                isset($_POST['brightness']) &&
                isset($_POST['nbStructure']) && $_POST['nbStructure'] > 0) {
                $received['fileName'] = filter_input(INPUT_POST, 'fileName', FILTER_SANITIZE_STRING);

                $received['dimXImg'] = filter_input(INPUT_POST, 'dimXImg', FILTER_VALIDATE_INT);
                $received['dimYImg'] = filter_input(INPUT_POST, 'dimYImg', FILTER_VALIDATE_INT);

                $received['refBackgroundColor'] = filter_input(INPUT_POST, 'backgroundColor', FILTER_SANITIZE_STRING);

                $received['brightness'] = filter_input(INPUT_POST, 'brightness', FILTER_VALIDATE_INT);


                if (!$received['fileName']) {
                    $received['echec'][] = 'nom du fichier';
                }
                if (!$received['dimXImg'] || 
                    ($received['dimXImg'] < 0) || ($received['dimXImg'] > MAX_X_IMG)) {
                    $received['echec'][] = 'dimension X du fichier';
                }
                if (!$received['dimYImg'] || 
                    ($received['dimYImg'] < 0) || ($received['dimYImg'] > MAX_Y_IMG)) {
                    $received['echec'][] = 'dimension Y du fichier';
                }
                if (!$received['refBackgroundColor'] ||
                    !verifHexaColor($received['refBackgroundColor'])) {
                    $received['echec'][] = 'couleur de fond du fichier';
                }
                if (!$received['brightness'] || 
                    ($received['brightness'] < 0) || ($received['brightness'] > 100)) {
                    $received['echec'][] = 'luminosité de la scène';
                }
            }
            else {
                throw new Exception("Création : Données formulaire incomplètes");
            }


            $cursor = 1;
            while($cursor < $_POST['nbStructure']) {
                if (isset($_POST['structure'.$cursor]) && $_POST['structure'.$cursor]) {//existe et est coché
                    if (isset($_POST[$cursor.'posX']) && isset($_POST[$cursor.'posY']) && isset($_POST[$cursor.'posZ'])) {
                        if (isset($_POST[$cursor.'texture'])) {
                            $received['structure'.$cursor]['refTexture'] = filter_input(INPUT_POST, $cursor.'texture', FILTER_SANITIZE_STRING);

                            if (!$received['structure'.$cursor]['refTexture']) {
                                $received['echec'][] = 'texture de l\'élément '.$cursor;
                            }
                        }
                        else if (isset($_POST[$cursor.'color'])) {
                            $received['structure'.$cursor]['refColor'] = filter_input(INPUT_POST, 'backgroundColor', FILTER_SANITIZE_STRING);

                            if (!$received['refColor'] ||
                                !verifHexaColor($received['refColor'])) {
                                $received['echec'][] = 'couleur de l\'élément '.$cursor;
                            }
                        }
                        else {
                            throw new Exception("Création : Données formulaire incomplètes");
                        }


                        if (isset($_POST[$cursor.'growth'])) {//absence de valeur : pas de grossissement
                            $received['structure'.$cursor]['growth'] = filter_input(INPUT_POST, $cursor.'growth', FILTER_VALIDATE_FLOAT);

                            if (!$received['structure'.$cursor]['growth']) {
                                $received['echec'][] = 'grossissement de l\'élément '.$cursor;
                            }
                        }


                        $received['structure'.$cursor]['refStruct'] = filter_input(INPUT_POST, $cursor.'refStruct', FILTER_SANITIZE_STRING);
                        $received['structure'.$cursor]['posX'] = filter_input(INPUT_POST, $cursor.'posX', FILTER_VALIDATE_FLOAT);
                        $received['structure'.$cursor]['posY'] = filter_input(INPUT_POST, $cursor.'posY', FILTER_VALIDATE_FLOAT);
                        $received['structure'.$cursor]['posZ'] = filter_input(INPUT_POST, $cursor.'posZ', FILTER_VALIDATE_FLOAT);

                        if (!$received['structure'.$cursor]['refStruct'] ||
                            $received['structure'.$cursor]['refStruct'] < 0) {
                            $received['echec'][] = 'structure de l\'élément '.$cursor;
                        }
                        if (!$received['structure'.$cursor]['posX'] ||
                            $received['structure'.$cursor]['posX'] < 0 || 
                            $received['structure'.$cursor]['posX'] > $received['dimXImg']) {
                            $received['echec'][] = 'position X de l\'élément '.$cursor;
                        }
                        if (!$received['structure'.$cursor]['posY'] ||
                            $received['structure'.$cursor]['posY'] < 0 || 
                            $received['structure'.$cursor]['posY'] > $received['dimYImg']) {
                            $received['echec'][] = 'position Y de l\'élément '.$cursor;
                        }
                        if (!$received['structure'.$cursor]['posZ'] ||
                            $received['structure'.$cursor]['posY'] < 0 || 
                            $received['structure'.$cursor]['posY'] > $received['dimYImg']) {
                            $received['echec'][] = 'position Z de l\'élément '.$cursor;
                        }
                    }
                }
                $cursor++;
            }
        break;
        case 'add':
            if (isset($_POST['structure'])) {
                $received['script'] = 'formula';

                if (isset($_POST['fName']) && isset($_POST['formula'])) {
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
        break;
    }

    if (isset($received['echec'])) {
        $_POST['script'] = false;//invalidate the script
    }
    else {
        $_POST['script'] = true;
    }

    return $received;
}
