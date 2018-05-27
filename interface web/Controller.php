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

            //creee fichiers textes
            $file = null;
            if ($_SESSION['pageBlock'] == 2) {
                $file = fopen('data.txt', 'w');
                createFile($file);

                $detailFile = $_SESSION['file'];

                $nbImg = $detailFile['video']['selected']? $detailFile['video']['frames'] : 1;
                exec("ProjetEx.exe");
               // echo '<script>window.open("Link/anim.html?name='.$detailFile['name'].'&nbImages='.$nbImg.'&antialiasing='.$detailFile['effects']['aliasing'].'&height='.$detailFile['dim']['x'].'&width='.$detailFile['dim']['y'].'");</script>';
            }
            if (isset($_POST['saveData'])) {
                $file = fopen('link/'.$_SESSION['file']['name'].'.txt', 'w');
                createFile($file);
            }
        }
    }

    //variables de la page
    if ($listWarning) $template['listWarning'] = $listWarning;
    $edition['legend'] = array('Caractérisation du fichier', 'Composition du fichier', 'Validation du résultat');

    //remplissage de la page
    require('View/vEdition.php');
}
