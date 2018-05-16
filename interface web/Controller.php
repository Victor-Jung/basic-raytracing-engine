<?php
require('Model.php');


function Edition($listWarning) {
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

function Update($listWarning) {
    //variables du template
    if ($listWarning) $template['listWarning'] = $listWarning;
    $template['pageName'] = 'Ajout de données';
    $template['actual'] = 'add';

    //variables de la page
    $edition['legend'][1] = 'Caractérisation du fichier';
    $edition['legend'][2] = 'Composition du fichier';
    $edition['legend'][3] = 'Validation du résultat';

    //remplissage de la page
    require('View/vUpdate.php');
}
