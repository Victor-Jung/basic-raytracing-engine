<?php
define('RESOLUTION_LEVELS', 16);
define('LENGTH_NAME', 20);
define('MAX_DURATION', 5*60);
define('MAX_Z_IMG', 100);
define('STEP_AXIS', 1);


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