<?php
define('RESOLUTION_LEVELS', 16);
define('LENGTH_NAME', 20);
define('MAX_DURATION', 5*60);
define('MAX_Z_IMG', 1000);
define('STEP_AXIS', 1);


function initialSession() {
    $_SESSION['pageBlock'] = 1;

    $_SESSION['file']['name'] = 'temporaire';
    $_SESSION['file']['dim']['x'] = 768;
    $_SESSION['file']['dim']['y'] = 768;
    $_SESSION['file']['video']['selected'] = 0;
    $_SESSION['file']['video']['duration'] = MAX_DURATION;
    $_SESSION['file']['video']['frequency'] = 1;
    $_SESSION['file']['effects']['shadows'] = 1;
    $_SESSION['file']['effects']['reflection'] = 0;
    $_SESSION['file']['effects']['refraction'] = 0;

    $_SESSION['scene']['color'] = '#2680AD';
    $_SESSION['scene']['light'][0]['bright'] = 100;
    $_SESSION['scene']['light'][0]['pos'] = array('x' => 1, 'y' => 1, 'z' => 1);
    $_SESSION['scene']['viewer'] = array('x' => 1, 'y' => 1, 'z' => 1);
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