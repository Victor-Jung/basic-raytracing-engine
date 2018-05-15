<?php
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
                                Dimensions du fichier :
                            </td>
                            <td>
                                <?= htmlspecialchars($_SESSION['edit']['dataFile']['dimX'].' x '.$_SESSION['edit']['dataFile']['dimY']) ?> pixels
                            </td>
                        </tr>
                        </tr>
                            <td>
                                Profondeur :
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
                    <div class="listSelection" style="max-height: <?= htmlspecialchars(22*($_SESSION['edit']['dataFile']['dimY'] / $_SESSION['edit']['dataFile']['dimX'])) ?>em">
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
                        <?php if (isset($_SESSION['edit']['dataScene']['shape'])) {
                            foreach ($_SESSION['edit']['dataScene']['shape'] as $figure) { ?>
                                <br>
                                <table class="fiche">
                                    <tr>
                                        <th colspan="2">
                                            Objet <?= htmlspecialchars($figure['id'].' : '.ucfirst($figure['name'])) ?>
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
                                    <?php if ($figure['name'] != 'Sphère') { ?>
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
                            <?php }
                        } ?>
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
                                    <input type="number" class="number" name="dimZ" 
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
                                <input type="number" name="duration" 
                                value="<?= htmlspecialchars($_SESSION['edit']['dataFile']['video']['duration']) ?>" 
                                step="1" min="1" max="<?= MAX_DURATION ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Images par seconde :
                            </td>
                            <td>
                                <input type="number" name="frequency" 
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

        function shapeData($shape) {
            if ($shape['name'] == 'Sphère') { ?>
                <tr>
                    <th colspan="2">Rayon de l'objet :</th>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php $diff = ($_SESSION['edit']['dataFile']['dimX'] > $_SESSION['edit']['dataFile']['dimY'])? $_SESSION['edit']['dataFile']['dimX'] / 2 : $_SESSION['edit']['dataFile']['dimY'] / 2 ?>
                        <label title="Entrez une valeur entre 1 et <?= htmlspecialchars($diff) ?>">
                            <input type="number" class="number" name="radius<?= htmlspecialchars($shape['id']) ?>" 
                            value="<?= htmlspecialchars($diff) ?>" 
                            step="<?= STEP_AXIS ?>" min="1" max="<?= htmlspecialchars($diff) ?>" required>
                        </label>
                    </td>
                </tr>
            <?php } 
            else { ?>
                <tr>
                    <th colspan="2">Dimensions de l'objet (X-Y<?php if ($shape['name'] != 'Surface') echo '-Z' ?>) :</th>
                </tr>
                <tr>
                    <td colspan="2">
                        <label title="Entrez une valeur entre 1 et <?= htmlspecialchars($_SESSION['edit']['dataFile']['dimX']) ?>">
                            <input type="number" class="number" name="xDim<?= htmlspecialchars($shape['id']) ?>" 
                            value="<?= htmlspecialchars($shape['dim']['xAxis']) ?>" 
                            step="<?= STEP_AXIS ?>" min="1" max="<?= htmlspecialchars($_SESSION['edit']['dataFile']['dimX']) ?>" required>
                        </label>
                        -
                        <label title="Entrez une valeur entre 1 et <?= htmlspecialchars($_SESSION['edit']['dataFile']['dimY']) ?>">
                            <input type="number" class="number" name="yDim<?= htmlspecialchars($shape['id']) ?>" 
                            value="<?= htmlspecialchars($shape['dim']['yAxis']) ?>" 
                            step="<?= STEP_AXIS ?>" min="1" max="<?= htmlspecialchars($_SESSION['edit']['dataFile']['dimY']) ?>" required>
                        </label>
                        <?php if ($shape['name'] != 'Surface') { ?>
                            -
                            <label title="Entrez une valeur entre 1 et <?= htmlspecialchars($_SESSION['edit']['dataFile']['dimZ']) ?>">
                                <input type="number" class="number" name="zDim<?= htmlspecialchars($shape['id']) ?>" 
                                value="<?= htmlspecialchars($shape['dim']['zAxis']) ?>" 
                                step="<?= STEP_AXIS ?>" min="1" max="<?= htmlspecialchars($_SESSION['edit']['dataFile']['dimZ']) ?>" required>
                            </label>
                        <?php } ?>
                    </td>
                </tr>
            <?php }?>

            <tr><td colspan="2"><br></td></tr>
            <tr>
                <th colspan="2">Position du <?= ($shape['name'] == 'Sphère')? 'centre' : 'premier sommet' ?> (X-Y) :</th>
            </tr>
            <tr>
                <td colspan="2">
                    <?php $diff = array(2 - $shape['dim']['xAxis'], $_SESSION['edit']['dataFile']['dimX'] - $shape['dim']['xAxis']) ?>
                    <label title="Entrez une valeur entre <?= htmlspecialchars($diff[0].' et '.$diff[1]) ?>">
                        <input type="number" class="number" name="xPos<?= htmlspecialchars($shape['id']) ?>" 
                        value="<?= htmlspecialchars($shape['dim']['xAxis']) ?>" 
                        step="<?= STEP_AXIS ?>" min="<?= htmlspecialchars($diff[0]) ?>" max="<?= htmlspecialchars($diff[1]) ?>" required>
                    </label>
                    -
                    <?php $diff = array(2 - $shape['dim']['xAxis'], $_SESSION['edit']['dataFile']['dimX'] - $shape['dim']['xAxis']) ?>
                    <label title="Entrez une valeur entre <?= htmlspecialchars($diff[0].' et '.$diff[1]) ?>">
                        <input type="number" class="number" name="yPos<?= htmlspecialchars($shape['id']) ?>" 
                        value="<?= htmlspecialchars($shape['dim']['yAxis']) ?>" 
                        step="<?= STEP_AXIS ?>" min="<?= htmlspecialchars($diff[0]) ?>" max="<?= htmlspecialchars($diff[1]) ?>" required>
                    </label>
                </td>
            </tr>
            <tr><td colspan="2"><br></td></tr>
            <tr>
                <th>Plan de l'objet :</th>
                <td>
                    <label title="Entrez une valeur entre 1 et <?= htmlspecialchars($_SESSION['edit']['dataFile']['dimZ']) ?>">
                        <input type="number" class="number" name="zPos<?= htmlspecialchars($shape['id']) ?>" 
                        value="<?= htmlspecialchars($shape['pos']['zAxis']) ?>" 
                        step="<?= STEP_AXIS ?>" min="1" max="<?= htmlspecialchars($_SESSION['edit']['dataFile']['dimZ']) ?>" required>
                    </label>
                </td>
            </tr>

            <?php if ($shape['name'] != 'Sphère') { ?>
                <tr><td colspan="2"><br></td></tr>
                <tr>
                    <th colspan="2">Rotation (X-Y-Z) :</th>
                </tr>
                <tr>
                    <td colspan="2">
                        <label title="Entrez une valeur entre -90 et 90">
                            <input type="number" class="number" name="xRot<?= htmlspecialchars($shape['id']) ?>" 
                            value="<?= htmlspecialchars($shape['rot']['xAxis']) ?>"
                            step="1" min="-90" max="90" required disabled>
                        </label>
                        -
                        <label title="Entrez une valeur entre -90 et 90">
                            <input type="number" class="number" name="yRot<?= htmlspecialchars($shape['id']) ?>" 
                            value="<?= htmlspecialchars($shape['rot']['yAxis']) ?>" 
                            step="1" min="-90" max="90" required disabled>
                        </label>
                        -
                        <label title="Entrez une valeur entre -90 et 90">
                            <input type="number" class="number" name="zRot<?= htmlspecialchars($shape['id']) ?>" 
                            value="<?= htmlspecialchars($shape['rot']['zAxis']) ?>" 
                            step="1" min="-90" max="90" required disabled>
                        </label>
                    </td>
                </tr>
            <?php }
        }
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
                                Choisissez un objet :<br>
                                <select id="shape" name="shape">
                                    <option>Aucun</option>
                                    <optgroup label="Objets simples">
                                        <option>Surface</option>
                                        <option>Sphère</option>
                                        <option>Pavé</option>
                                    </optgroup>
                                    <optgroup label="Objets avancés" disabled>
                                        <option>Pyramide</option><!--peut préciser nb de faces-->
                                    </optgroup>
                                    <optgroup label="Objets complexes" disabled>
                                        <option>(Courbes)</option>
                                    </optgroup>
                                </select>
                                <input type="submit" name="confirmShape" value="Confirmer">
                            </td>
                        </tr>
                        <?php if (isset($_SESSION['edit']['dataScene']['shape'])) { ?>
                            <tr>
                                <td>
                                    <div class="listSelection" style="max-height: <?= htmlspecialchars(20*($_SESSION['edit']['dataFile']['dimY'] / $_SESSION['edit']['dataFile']['dimX'])) ?>em">
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
                                                <h3>Objet <?= htmlspecialchars($figure['id'].' : '.$figure['name']) ?></h3>
                                            </label>
                                            <table>
                                                <tr><td colspan="2">(bouton) supprimer l'objet<br><br></td></tr>
                                                <?php shapeData($figure) ?>
                                                <tr><td colspan="2"><br></td></tr>
                                                <tr>
                                                    <th>Coloration de l'objet :</th>
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
                        <?php } ?>
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
                                        <input type="checkbox" id="nextStep" name="nextStep" value="true">
                                        <label for="nextStep">
                                            <?= ($i != 3) ? 'Passer à l\'étape suivante' : 'Nouveau fichier' ?>
                                        </label>
                                        <?php if ($i == 3) { ?>
                                            <br>
                                            <input type="checkbox" id="reuseData" name="reuseData" value="true">
                                            <label for="reuseData">
                                                <?= 'Conserver les données' ?>
                                            </label>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <input type="submit" value="Actualiser">
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
