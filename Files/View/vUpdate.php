<?php
$page['pageName'] = 'Ajout de données';
$page['actual'] = 'add';
$page['script'] = 'formula';

if ($page['script'] != 'formula') {
    $page['legend'] = 'Formes géométriques';

    ob_start(); ?>
        champs
    <?php $page['fieldset'] = ob_get_clean();
}
else {
    $page['legend'] = 'Textures';

    ob_start(); ?>
        champs
    <?php $page['fieldset'] = ob_get_clean();
}

ob_start(); ?>
    <h2>
        Remplissage des bases de données
    </h2>

    <fieldset>
        <legend>
            <?= htmlspecialchars($page['legend']) ?>
        </legend>

        <?= $page['fieldset'] ?>
    </fieldset>
<?php $template['content'] = ob_get_clean();


require('View/Template.php');
