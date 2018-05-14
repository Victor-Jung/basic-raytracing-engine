<?php
require('Model.php');


function Edition() {
    if (!isset($_SESSION['edit']['step'])) {
        $_SESSION['edit']['step'] = 1;
    }
    else if (isset($_POST['nextStep']) && $_POST['nextStep']) {
        if ($_SESSION['edit']['step'] >= 3) {
            $_SESSION['edit']['step'] = 1;
        }
        else {
            $_SESSION['edit']['step']++;
        }
        $_POST['nextStep'] = false;
    }
    


    require('View/vEdition.php');
}