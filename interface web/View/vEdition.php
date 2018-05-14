<?php
define('RESOLUTION_LEVELS', 8);
define('LENGTH_NAME', 20);
define('MAX_DURATION', 5*60);
define('MAX_Z_IMG', 100);
define('MAX_GROWTH', 10);
define('STEP_GROWTH', 0.1);
define('STEP_AXIS', 1);


// debut provisoire : test de la page
    //valeurs par defaut (initialisees au lancement de la page) :
        $_SESSION['edit']['dataFile']['name'] = '';
        $_SESSION['edit']['dataFile']['format'] = 'BMP';
        $_SESSION['edit']['dataFile']['dimX'] = 768;
        $_SESSION['edit']['dataFile']['dimY'] = 768;
        $_SESSION['edit']['dataFile']['dimZ'] = MAX_Z_IMG;
        $_SESSION['edit']['dataFile']['video']['duration'] = MAX_DURATION;
        $_SESSION['edit']['dataFile']['video']['frequency'] = 1;

        $_SESSION['edit']['dataScene']['bright'] = 100;
        $_SESSION['edit']['dataScene']['backgroundColor'] = '#000000';
    
        //pour chaque ajout de shape : initialise tous les champs
        $_SESSION['edit']['dataScene']['shape'][0] = array('name' => '(nom)',
                                                    'id' => count($_SESSION['edit']['dataScene']['shape']),
                                                    'growth' => 0,
                                                    'color' => '#ffffff',
                                                    'pos' => array('xAxis' => $_SESSION['edit']['dataFile']['dimX'], 
                                                                'yAxis' => $_SESSION['edit']['dataFile']['dimY'], 
                                                                'zAxis' => $_SESSION['edit']['dataFile']['dimZ']),
                                                    'rot' => array('xAxis' => 0, 'yAxis' => 0, 'zAxis' => 0));
// fin provisoire



//variables du template
$template['pageName'] = 'Edition d\'image';
$template['actual'] = 'edit';
$template['script'] = false;


//variables de la page
$edition['legend'][1] = 'Caractérisation du fichier';
$edition['legend'][2] = 'Composition du fichier';
$edition['legend'][3] = 'Validation du résultat';


//fragments de code de la page
if ($_SESSION['edit']['step'] > 1) {
    ob_start(); ?>
        <table>
            <tr>
                <td>
                    Fichier : <?= htmlspecialchars($_SESSION['edit']['dataFile']['name'].'.'.$_SESSION['edit']['dataFile']['format']) ?>
                </td>
                <td>
                    <table>
                        <tr>
                            <td>
                                Dimensions du fichier
                            </td>
                            <td>
                                <?= htmlspecialchars($_SESSION['edit']['dataFile']['dimX'].' x '.$_SESSION['edit']['dataFile']['dimY']) ?> pixels
                            </td>
                        </tr>
                        </tr>
                            <td>
                                Profondeur
                            </td>
                            <td>
                                <?= htmlspecialchars($_SESSION['edit']['dataFile']['dimZ']) ?> couches
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    <?php $edition['content']['fixed'][1] = ob_get_clean();
}
if ($_SESSION['edit']['step'] == 3) {
    ob_start(); ?>
        <table>
            <tr>
                <td>
                    <table>
                        <tr>
                            <td>
                                Couleur de fond :
                            </td>
                            <td>
                                <input type="color" value="<?= htmlspecialchars($_SESSION['edit']['dataScene']['backgroundColor']) ?>" disabled>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Luminosité : 
                            </td>
                            <td>
                                <?= htmlspecialchars($_SESSION['edit']['dataScene']['bright']) ?>%
                            </td>
                        </tr>
                    </table>
                    <br>
                    <div class="listSelection" style="max-height: <?= htmlspecialchars(20*($_SESSION['edit']['dataFile']['dimY'] / $_SESSION['edit']['dataFile']['dimX'])) ?>em">
                        <?php foreach ($_SESSION['edit']['dataScene']['shape'] as $figure) { ?>
                            <table class="fiche">
                                <tr>
                                    <th colspan="2">
                                        Forme <?= htmlspecialchars($figure['id'].' : '.ucfirst($figure['name'])) ?>
                                    </th>
                                </tr>
                                <tr><th colspan="2"><br></th></tr>
                                <tr>
                                    <td>
                                        Translation :
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($figure['pos']['xAxis'].' ; '.$figure['pos']['yAxis'].' ; '.$figure['pos']['zAxis']) ?>
                                    </td>
                                </tr>
                                <?php if ($figure['name'] != 'sphère') { ?>
                                    <tr>
                                        <td>
                                            Rotation :
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($figure['rot']['xAxis'].' ; '.$figure['rot']['yAxis'].' ; '.$figure['rot']['zAxis']) ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td>
                                        Grossissement :
                                    </td>
                                    <td>
                                        x<?= htmlspecialchars($figure['growth']) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Coloration :
                                    </td>
                                    <td>
                                        <input type="color" value="<?= htmlspecialchars($figure['color']) ?>" disabled>
                                    </td>
                                </tr>
                            </table>
                            <br>
                        <?php } ?>
                    </div>
                </td>
                <td>
                    <div id="gridFixed" style="height: <?= htmlspecialchars(25*($_SESSION['edit']['dataFile']['dimY'] / $_SESSION['edit']['dataFile']['dimX'])) ?>em;
                    background-size: <?= ($_SESSION['edit']['dataFile']['dimX'] >= $_SESSION['edit']['dataFile']['dimY'])? 'cover' : 'contain' ?>;
                    background-color: <?= htmlspecialchars($_SESSION['edit']['dataScene']['backgroundColor']) ?>">
                        <!--Ajoute objets avec des images de fond, que l'on déplace selon les axes x et y, ou des span modifiés en css?-->
                    </div>
                </td>
            </tr>
        </table>
    <?php $edition['content']['fixed'][2] = ob_get_clean();
}

switch ($_SESSION['edit']['step']) {
    case 1:
        $edition['display'][1] = true;
        $edition['display'][2] = false;
        $edition['display'][3] = false;

        $edition['script'] = 'fileConfig';

        function definition($axis) { ?>
            <select id="dim<?= $axis ?>" name="dim<?= $axis ?>">
                <?php for ($i = 1; $i <= RESOLUTION_LEVELS; $i++) {
                    $pixels = 256*$i;

                    echo '<option';
                    if ($_SESSION['edit']['dataFile']['dim'.$axis] == $pixels) {
                        echo ' selected';
                    }
                    echo '>'.$pixels.'</option>';
                } ?>
            </select>
        <?php }
        ob_start(); ?>
            <tr>
                <td>
                    <label>
                        Nom du fichier :
                        <input type="text" id="fileName" name="fileName" maxlength="<?= LENGTH_NAME ?>" 
                        value="<?= htmlspecialchars($_SESSION['edit']['dataFile']['name']) ?>" required>
                    </label>
                    <br><br>
                    <table>
                        <tr>
                            <td>
                                Dimensions du fichier :
                            </td>
                            <td>
                                <?php definition('X') ?> x <?php definition('Y') ?> pixels
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Profondeur de la scène :
                            </td>
                            <td>
                                <label title="Entrez une valeur entre 1 et <?= MAX_Z_IMG ?>">
                                    <input type="number" id="dimZ" name="dimZ" 
                                    value="<?= htmlspecialchars($_SESSION['edit']['dataFile']['dimZ']) ?>"
                                    step="1" min="1" max="<?= MAX_Z_IMG ?>" required> couches
                                </label>
                            </td>
                        </tr>
                    </table>
                </td>
                <td id="typeFile">
                    Type de fichier :
                    <label>
                        <input type="radio" id="picture" name="typeFile" value="picture" checked required>
                        Image
                    </label>

                    <input type="radio" id="video" name="typeFile" value="video" required disabled>
                    <label for="video" id="buttonVideo">
                        Animation
                    </label>
                    <table>
                        <tr>
                            <td>
                                Durée en secondes :
                            </td>
                            <td>
                                <input type="number" id="duration" name="duration" 
                                value="<?= htmlspecialchars($_SESSION['edit']['dataFile']['video']['duration']) ?>" 
                                step="1" min="1" max="<?= MAX_DURATION ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Images par seconde :
                            </td>
                            <td>
                                <input type="number" id="frequency" name="frequency" 
                                value="<?= htmlspecialchars($_SESSION['edit']['dataFile']['video']['frequency']) ?>" 
                                step="1" min="1" max="60" required>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        <?php $edition['content']['fillable'] = ob_get_clean();
    break;
    case 2:
        $edition['display'][1] = true;
        $edition['display'][2] = true;
        $edition['display'][3] = false;

        $edition['script'] = 'sceneConfig';

        ob_start(); ?>
            <tr>
                <td>
                    <label title="Entrez une valeur entre 0 et 100">
                        Luminosité du fichier :
                        <input type="number" id="bright" name="bright" 
                        value="<?= htmlspecialchars($_SESSION['edit']['dataScene']['bright']) ?>" 
                        step="1" min="0" max="100" required>%
                    </label>
                </td>
                <td>
                    <label>
                        Couleur de fond du fichier :
                        <input type="color" id="fileName" name="fileName" 
                        value="<?= htmlspecialchars($_SESSION['edit']['dataScene']['backgroundColor']) ?>">
                    </label>
                </td>
            </tr>
            <tr><td colspan="2"><br></td></tr>
            <tr>
                <td>
                    <table id="shapeSelection">
                        <tr>
                            <td class="listSelection">
                                Choisissez une forme :<br>
                                <select>
                                    <optgroup label="Formes simples">
                                        <option>Surface</option>
                                        <option>Sphère</option>
                                        <option>Pavé</option>
                                    </optgroup>
                                    <optgroup label="Formes avancées" disabled>
                                        <option>Pyramide</option><!--peut préciser nb de faces-->
                                    </optgroup>
                                    <optgroup label="Formes complexes" disabled>
                                        <option>(Courbes)</option>
                                    </optgroup>
                                </select>
                                <input type="submit" value="Confirmer">
                            </td>
                        </tr>
                        <tr>
                            <td class="listSelection">
                                <div style="max-height: <?= htmlspecialchars(20*($_SESSION['edit']['dataFile']['dimY'] / $_SESSION['edit']['dataFile']['dimX'])) ?>em">
                                    <?php foreach ($_SESSION['edit']['dataScene']['shape'] as $figure) {
                                        if ($figure['id'] > 1) {
                                            echo '<hr>';
                                        } ?>
                                        <style type="text/css">
                                            #display<?= htmlspecialchars($figure['id']) ?>, #display<?= htmlspecialchars($figure['id']) ?> + label + table {
                                                display: none;
                                            }
                                            #display<?= htmlspecialchars($figure['id']) ?>:checked + label + table {
                                                display: initial;
                                            }
                                        </style>

                                        <input type="checkbox" id="display<?= htmlspecialchars($figure['id']) ?>">
                                        <label for="display<?= htmlspecialchars($figure['id']) ?>">
                                            <h3>Forme <?= htmlspecialchars($figure['id'].' : '.ucfirst($figure['name'])) ?></h3>
                                        </label>
                                        <table>
                                            <tr>
                                                <th colspan="2">Dimensions (X-Y-Z) :</th>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    ->faire switch<br>
                                                    si plan : x y<br>
                                                    si pavé : x y z<br>
                                                    si sphère : r<br>
                                                    si pyramide : ??
                                                </td>
                                            </tr>
                                            <tr><td colspan="2"><br></td></tr>
                                            <tr>
                                                <th colspan="2">Translation (X-Y-Z) :</th>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <label title="Entrez une valeur entre 0 et <?= htmlspecialchars($_SESSION['edit']['dataFile']['dimX']) ?>">
                                                        <input type="number" id="xPos" name="xPos" 
                                                        value="<?= htmlspecialchars($figure['pos']['xAxis']) ?>" 
                                                        step="<?= STEP_AXIS ?>" min="0" max="<?= htmlspecialchars($_SESSION['edit']['dataFile']['dimX']) ?>" required>
                                                    </label>
                                                    -
                                                    <label title="Entrez une valeur entre 0 et <?= htmlspecialchars($_SESSION['edit']['dataFile']['dimY']) ?>">
                                                        <input type="number" id="yPos" name="yPos" 
                                                        value="<?= htmlspecialchars($figure['pos']['yAxis']) ?>" 
                                                        step="<?= STEP_AXIS ?>" min="0" max="<?= htmlspecialchars($_SESSION['edit']['dataFile']['dimY']) ?>" required>
                                                    </label>
                                                    -
                                                    <label title="Entrez une valeur entre 0 et <?= htmlspecialchars($_SESSION['edit']['dataFile']['dimZ']) ?>">
                                                        <input type="number" id="zPos" name="zPos" 
                                                        value="<?= htmlspecialchars($figure['pos']['zAxis']) ?>" 
                                                        step="<?= STEP_AXIS ?>" min="0" max="<?= htmlspecialchars($_SESSION['edit']['dataFile']['dimZ']) ?>" required>
                                                    </label>
                                                </td>
                                            </tr>
                                            <?php if ($figure['name'] != 'sphère') { ?>
                                                <tr><td colspan="2"><br></td></tr>
                                                <tr>
                                                    <th colspan="2">Rotation (X-Y-Z) :</th>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <label title="Entrez une valeur entre -90 et 90">
                                                            <input type="number" id="xRot" name="xRot" 
                                                            value="<?= htmlspecialchars($figure['rot']['xAxis']) ?>"
                                                            step="1" min="-90" max="90" required disabled>
                                                        </label>
                                                        -
                                                        <label title="Entrez une valeur entre -90 et 90">
                                                            <input type="number" id="yRot" name="yRot" 
                                                            value="<?= htmlspecialchars($figure['rot']['yAxis']) ?>" 
                                                            step="1" min="-90" max="90" required disabled>
                                                        </label>
                                                        -
                                                        <label title="Entrez une valeur entre -90 et 90">
                                                            <input type="number" id="zRot" name="zRot" 
                                                            value="<?= htmlspecialchars($figure['rot']['zAxis']) ?>" 
                                                            step="1" min="-90" max="90" required disabled>
                                                        </label>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <tr><td colspan="2"><br></td></tr>
                                            <tr>
                                                <th>Grossissement :</th>
                                                <td>
                                                    <label title="Entrez une valeur entre <?= '-'.MAX_GROWTH ?> et <?= MAX_GROWTH ?>">
                                                        x<input type="number" id="growth" name="growth" 
                                                        value="<?= htmlspecialchars($figure['growth']) ?>" 
                                                        step="<?= STEP_GROWTH ?>" min="<?= '-'.MAX_GROWTH ?>" max="<?= MAX_GROWTH ?>" required disabled>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr><td colspan="2"><br></td></tr>
                                            <tr>
                                                <th>Coloration :</th>
                                                <td>
                                                    <input type="color" id="color" name="color" 
                                                    value="<?= htmlspecialchars($figure['color']) ?>">
                                                </td>
                                            </tr>
                                            <tr><td colspan="2"><br></td></tr>
                                        </table>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <div id="gridFillable" style="height: <?= htmlspecialchars(25*($_SESSION['edit']['dataFile']['dimY'] / $_SESSION['edit']['dataFile']['dimX'])) ?>em;
                    background-size: <?= ($_SESSION['edit']['dataFile']['dimX'] >= $_SESSION['edit']['dataFile']['dimY'])? 'cover' : 'contain' ?>;">
                        <!--Ajoute objets avec des images de fond, que l'on déplace selon les axes x et y, ou des span modifiés en css?-->
                    </div>
                </td>
            </tr>
        <?php $edition['content']['fillable'] = ob_get_clean();
    break;
    case 3:
        $edition['display'][1] = true;
        $edition['display'][2] = true;
        $edition['display'][3] = true;

        $edition['script'] = 'patchConfig';

        ob_start(); ?>
            <tr>
                <td>
                    <label>
                        Choisissez une retouche :<br>
                        <select>
                            <option>Luminosité</option>
                            <option>Filtre lumineux</option>
                        </select>
                    </label>
                    <input type="submit" value="Confirmer">
                </td>
                <td>
                    (fichier final)
                </td>
            </tr>
        <?php $edition['content']['fillable'] = ob_get_clean();
    break;
}


//fragments de code du template
ob_start(); ?>
    <table>
        <thead>
            <tr>
                <th>
                    Édition d'image ou de vidéo
                </th>
            </tr>
            <tr>
                <td>
                    (Survolez les champs numériques pour connaître leur amplitude)
                </td>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            if (isset($edition['content']['fixed'])) {
                foreach ($edition['content']['fixed'] as $content) {
                    if ($i == 1) { ?>
                        <tr>
                            <td>
                                <br><br>
                                <table id="fixed">
                    <?php }
                    else { ?>
                                    <tr>
                                        <td colspan="3"><hr></td>
                                    </tr>
                    <?php } ?>
                                    <tr>
                                        <th>
                                            <?= htmlspecialchars($edition['legend'][$i]) ?>
                                        </th>
                                        <td>
                                            <?= $content ?>
                                        </td>
                                    </tr>
                                    
                    <?php if ($i == 2 || ($i == 1 && !$edition['display'][3])) { ?>
                                    <tr>
                                        <td colspan="3"><br></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    <?php }
                    $i++;
                }
            } ?>
            <tr>
                <td>
                    <fieldset>
                        <legend><?= $edition['legend'][$i] ?></legend>
                        <form method="post" action="index.php?action=edit">
                            <input type="hidden" name="script" value="<?= htmlspecialchars($edition['script']) ?>">
                            <table>
                                <?= $edition['content']['fillable'] ?>
                                <tr><td colspan="2"><br><hr></td></tr>
                                <tr>
                                    <td>
                                        <label>
                                            <input type="checkbox" id="nextStep" name="nextStep" value="true">
                                            <?= ($i != 3) ? 'Passer à l\'étape suivante' : 'Nouveau fichier' ?>
                                        </label>
                                        <?php if ($i == 3) { ?>
                                            <br>
                                            <label>
                                                <input type="checkbox" id="nextStep" name="reuseData" value="true">
                                                <?= 'Conserver les données' ?>
                                            </label>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <input type="submit" value="<?= ($i != 3) ? 'Actualiser' : 'Nouveau fichier' ?>">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </fieldset>
                </td>
            </tr>
        </tbody>
    </table>
<?php $template['content'] = ob_get_clean();


//remplissage du template
require('View/Template.php');
