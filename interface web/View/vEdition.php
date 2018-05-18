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
                        <?php if (isset($_SESSION['edit']['dataScene']['shape']) && count($_SESSION['edit']['dataScene']['shape']) > 0) {
                            foreach ($_SESSION['edit']['dataScene']['shape'] as $shape) { ?>
                                <br>
                                <table class="fiche">
                                    <tr>
                                        <th colspan="2">
                                            Objet <?= htmlspecialchars($shape['id'].' : '.$shape['name']) ?>
                                        </th>
                                    </tr>
                                    <tr><th colspan="2"><br></th></tr>
                                    <tr>
                                        <td>
                                            Couleur :
                                        </td>
                                        <td>
                                            <input type="color" value="<?= htmlspecialchars($shape['color']) ?>" disabled>
                                        </td>
                                    </tr>
                                    
                                    <tr><td colspan="2"><br></td></tr>
                                    <tr>
                                        <td><?= ($shape['name'] == 'Sphère')? 'Centre' : 'Premier sommet' ?> :</td>
                                        <td>
                                            <?= htmlspecialchars($shape['pos']['xAxis'].'-'.$shape['pos']['yAxis'].'-'.$shape['pos']['zAxis']) ?>
                                        </td>
                                    </tr>
                                    <tr><td colspan="2"><br></td></tr>
                                    <?php if ($shape['name'] == 'Sphère') { ?>
                                        <tr>
                                            <td>Rayon :</td>
                                            <td><?= htmlspecialchars($shape['radius']) ?></td>
                                        </tr>
                                    <?php } 
                                    else { ?>
                                        <tr>
                                            <td>Dimensions :</td>
                                            <td>
                                                <?= htmlspecialchars($shape['dim']['xAxis'].'-'.$shape['dim']['yAxis'])?>
                                                <?php if ($shape['name'] != 'Surface') echo htmlspecialchars('-'.$shape['dim']['zAxis']) ?>
                                            </td>
                                        </tr>
                                        <tr><td colspan="2"><br></td></tr>
                                        <tr>
                                            <td>Rotation :</td>
                                            <td>
                                                <?= (empty($shape['rot']['xAxis']))? 0 : htmlspecialchars($shape['rot']['xAxis']) ?>
                                                -
                                                <?= (empty($shape['rot']['yAxis']))? 0 : htmlspecialchars($shape['rot']['yAxis']) ?>
                                                -
                                                <?= (empty($shape['rot']['zAxis']))? 0 : htmlspecialchars($shape['rot']['zAxis']) ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
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
            <select id="dim<?= $axis ?>File" name="dim<?= $axis ?>File">
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
                                    <input type="number" class="number" name="dimZFile" 
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

        function shapeForm($shape, $data) { ?>
            <tr>
                <td><?= htmlspecialchars($data['titre']) ?></td>
                <td>
                    <?php $listAxis = array('x', 'y');
                    if (!($data['categorie'] == 'dim' && ($shape['name'] == 'Surface' || $shape['name'] == 'Sphère'))) $listAxis[] = 'z';

                    foreach ($listAxis as $axis) { ?>
                        <label title="Entrez une valeur<?= $data['legend'] ?>">
                            <input type="number" class="number" name="<?= htmlspecialchars($data['categorie'].strtoupper($axis).$shape['id']) ?>" 
                            value="<?= htmlspecialchars($shape[''.$data['categorie'].''][''.$axis.'Axis']) ?>" step="<?= STEP_AXIS ?>" 
                            min="<?= htmlspecialchars($data['min']) ?>" max="<?= htmlspecialchars($data['max']) ?>" required
                            <?php if (isset($data['disable'])) echo 'disabled' ?>>
                        </label>
                    <?php } ?>
                </td>
            </tr>
        <?php }
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
                        <input type="color" id="backgroundColor" name="backgroundColor" 
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
                                    <optgroup label="Objets 2D">
                                        <option>Surface</option>
                                        <option disabled>Cercle</option>
                                    <optgroup label="Objets 3D simples">
                                        <option>Pavé</option>
                                        <option>Sphère</option>
                                    </optgroup>
                                    <optgroup label="Objets 3D avancés" disabled>
                                        <option>Pyramide</option>
                                        <option>Ellipsoïde</option>
                                    </optgroup>
                                    <optgroup label="Objets personnalisés" disabled>
                                        <option>Polyèdre</option>
                                    </optgroup>
                                </select>
                                <input type="submit" name="confirmShape" value="Confirmer">
                            </td>
                        </tr>
                        <?php if (isset($_SESSION['edit']['dataScene']['shape'])) { ?>
                            <tr>
                                <td>
                                    <div class="listSelection" style="max-height: <?= htmlspecialchars(20*($_SESSION['edit']['dataFile']['dimY'] / $_SESSION['edit']['dataFile']['dimX'])) ?>em">
                                        <?php foreach ($_SESSION['edit']['dataScene']['shape'] as $shape) {
                                            if ($shape['id'] > 1) {
                                                echo '<hr>';
                                            } ?>
                                            <style type="text/css">
                                                #display<?= htmlspecialchars($shape['id']) ?>, #display<?= htmlspecialchars($shape['id']) ?> + label + table {
                                                    display: none;
                                                }
                                                #display<?= htmlspecialchars($shape['id']) ?>:checked + label + table {
                                                    display: initial;
                                                }
                                            </style>

                                            <input type="hidden" name="name<?= htmlspecialchars($shape['id']) ?>" value="<?= htmlspecialchars($shape['name']) ?>">
                                            <input type="checkbox" id="display<?= htmlspecialchars($shape['id']) ?>">
                                            <label for="display<?= htmlspecialchars($shape['id']) ?>">
                                                <table>
                                                    <tr>
                                                        <td>
                                                            <h3>Objet <?= htmlspecialchars($shape['id']) ?></h3>
                                                        </td>
                                                        <td>
                                                            <h3><?= htmlspecialchars($shape['name']) ?></h3>
                                                        </td>
                                                        <td>
                                                            <button name="delete_<?= htmlspecialchars($shape['id']) ?>" value="true">Supprimer</button>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </label>
                                            <table>
                                                <?php if ($shape['name'] == 'Sphère' || $shape['name'] == 'Surface') { ?>
                                                    <tr>
                                                        <td>Couleur de l'objet</td>
                                                        <td>
                                                            <input type="color" class="color" name="color<?= htmlspecialchars($shape['id']) ?>" 
                                                            value="<?= htmlspecialchars($shape['color']) ?>">
                                                        </td>
                                                    </tr>
                                                <?php }
                                                else {
                                                    $table = array('dessus', 'dessous', 'devant', 'derrière', 'droite', 'gauche');
                                                    for ($i = 0; $i < 6; $i++) { ?>
                                                        <tr>
                                                            <td>Couleur de <?= htmlspecialchars($table[$i]) ?></td>
                                                            <td>
                                                                <input type="color" class="color" name="color<?= htmlspecialchars($shape['id'].'_face'.$i) ?>" 
                                                                value="<?= htmlspecialchars($shape['faces'][$i]['color']) ?>">
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } 

                                                echo '<tr><td colspan="2"><br></td></tr>';

                                                $formData = array('legend' => '', 'min' => '', 'max' => '');

                                                $formData['titre'] = ($shape['name'] == 'Sphère')? 'Position du centre (X-Y-Z)' : 'Position du premier sommet (X-Y-Z)';
                                                $formData['categorie'] = 'pos';
                                                shapeForm($shape, $formData); 

                                                echo '<tr><td colspan="2"><br></td></tr>';

                                                if ($shape['name'] == 'Sphère') { ?>
                                                    <tr>
                                                        <td>Rayon</td>
                                                        <td>
                                                            <label title="Entrez une valeur">
                                                                <input type="number" class="number" name="radius<?= htmlspecialchars($shape['id']) ?>" 
                                                                value="<?= htmlspecialchars($shape['radius']) ?>" step="<?= STEP_AXIS ?>" required>
                                                            </label>
                                                        </td>
                                                    </tr>
                                                <?php } 
                                                else { 
                                                    $formData['titre'] = ($shape['name'] != 'Surface')? 'Dimensions (X-Y-Z)' : 'Dimensions (X-Y)';
                                                    $formData['categorie'] = 'dim';
                                                    shapeForm($shape, $formData);

                                                    echo '<tr><td colspan="2"><br></td></tr>';

                                                    $formData['titre'] = 'Rotation (X-Y-Z)';
                                                    $formData['categorie'] = 'rot';
                                                    $formData['legend'] = ' entre -90 et 90';
                                                    $formData['min'] = -90;
                                                    $formData['max'] = 90;
                                                    $formData['disable'] = true;
                                                    shapeForm($shape, $formData);
                                                }

                                                echo '<tr><td colspan="2"><br></td></tr>'; ?>
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
                    <img src="../link/preview.bmp" alt="Smiley face" width="100%">
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
