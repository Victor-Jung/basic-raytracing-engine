<?php
//fragments de code de la page
if ($_SESSION['pageBlock'] > 1) {
    ob_start(); ?>
        <table>
            <tr>
                <td>
                    Fichier : <?= htmlspecialchars($_SESSION['file']['name'].'.') ?><?= (!$_SESSION['file']['video']['selected'])? 'BMP' : 'DVI' ?>
                </td>
                <td>
                    Dimensions du fichier :
                    <?= htmlspecialchars($_SESSION['file']['dim']['x'].' x '.$_SESSION['file']['dim']['y']) ?> pixels
                </td>
            </tr>
        </table>
    <?php $edition['content']['fixed'][1] = ob_get_clean();
}
if ($_SESSION['pageBlock'] == 3) {
    ob_start(); ?>
        <table>
            <tr>
                <td>
                    <div class="listSelection" style="max-height: <?= htmlspecialchars(22*($_SESSION['file']['dim']['y'] / $_SESSION['file']['dim']['x'])) ?>em">
                        <table>
                            <tr>
                                <td>
                                    Couleur de fond :
                                </td>
                                <td>
                                    <input type="color" value="<?= htmlspecialchars($_SESSION['scene']['color']) ?>" disabled>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Luminosité : 
                                </td>
                                <td>
                                    <?= htmlspecialchars($_SESSION['scene']['light'][0]['bright']) ?>%
                                </td>
                            </tr>
                        </table>
                        <?php if (isset($_SESSION['ellipsoid']) && count($_SESSION['ellipsoid']) > 0) {
                            $i = 0;
                            foreach ($_SESSION['ellipsoid'] as $shape) {
                                $i++; ?>
                                <br>
                                <table class="fiche">
                                    <tr>
                                        <th colspan="2">
                                            Objet <?= htmlspecialchars($i.' : '.$shape['name']) ?>
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
                                        <td>Centre :</td>
                                        <td>
                                            <?= htmlspecialchars($shape['pos']['x'].'-'.$shape['pos']['y'].'-'.$shape['pos']['z']) ?>
                                        </td>
                                    </tr>
                                    <tr><td colspan="2"><br></td></tr>
                                    <tr>
                                        <td>Rayon :</td>
                                        <td>
                                            <?= htmlspecialchars($shape['rad']['x'].'-'.$shape['rad']['y'].'-'.$shape['rad']['z']) ?>    
                                        </td>
                                    </tr>
                                    <tr><td colspan="2"><br></td></tr>
                                    <tr>
                                        <td>Rotation :</td>
                                        <td>
                                            <?= (empty($shape['rot']['x']))? 0 : htmlspecialchars($shape['rot']['x']) ?>
                                            -
                                            <?= (empty($shape['rot']['y']))? 0 : htmlspecialchars($shape['rot']['y']) ?>
                                            -
                                            <?= (empty($shape['rot']['z']))? 0 : htmlspecialchars($shape['rot']['z']) ?>
                                        </td>
                                    </tr>
                                </table>
                            <?php }
                        }
                        if (isset($_SESSION['polyhedron']) && count($_SESSION['polyhedron']) > 0) {
                            $i = 0;
                            foreach ($_SESSION['polyhedron'] as $shape) {
                                $i++; ?>
                                <br>
                                <table class="fiche">
                                    <tr>
                                        <th colspan="2">
                                            Objet <?= htmlspecialchars($i.' : '.$shape['name']) ?>
                                        </th>
                                    </tr>
                                    <tr><th colspan="2"><br></th></tr>
                                    <?php foreach($shape['face'] as $face) { ?>
                                        <tr>
                                            <td>
                                                Couleur :
                                            </td>
                                            <td>
                                                <input type="color" value="<?= htmlspecialchars($face['color']) ?>" disabled>
                                            </td>
                                        </tr>
                                        <tr><td colspan="2"><br></td></tr>
                                        <?php $j = 0;
                                        foreach($face['peak'] as $peak) {
                                            $j++; ?>
                                            <tr>
                                                <td colspan="2">
                                                    Sommet <?= htmlspecialchars($j) ?> : position 
                                                    <?= htmlspecialchars($peak['x'].'-'.$peak['y'].'-'.$peak['z']) ?>
                                                </td>
                                            </tr>

                                        <?php }
                                    } ?>
                                </table>
                            <?php }
                        } ?>
                    </div>
                </td>
                <td>
                    <div id="grid" style="height: <?= htmlspecialchars(25*($_SESSION['file']['dim']['y'] / $_SESSION['file']['dim']['x'])) ?>em;
                    background-color: <?= htmlspecialchars($_SESSION['scene']['color']) ?>">
                        <!--Ajoute objets avec des images de fond, que l'on déplace selon les axes x et y, ou des span modifiés en css?-->
                    </div>
                </td>
            </tr>
        </table>
    <?php $edition['content']['fixed'][2] = ob_get_clean();
}

switch ($_SESSION['pageBlock']) {
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
                    if ($_SESSION['file']['dim'][strtolower($axis)] == $pixels) {
                        echo ' selected';
                    }
                    echo '>'.$pixels.'</option>';
                } ?>
            </select>
        <?php }
        ob_start(); ?>
            <tr>
                <td>
                    <table>
                        <tr>
                            <td>
                                Nom du fichier :
                            </td>
                            <td>
                                <input type="text" id="fileName" name="fileName" maxlength="<?= LENGTH_NAME ?>" 
                                value="<?= htmlspecialchars($_SESSION['file']['name']) ?>" required>
                            </td>
                        </tr>
                        <tr><td><br></td></tr>
                        <tr>
                            <td>
                                Dimensions du fichier :
                            </td>
                            <td>
                                <?php definition('X') ?> x <?php definition('Y') ?> pixels
                            </td>
                        </tr>
                    </table>
                </td>
                <td id="video">
                    Type de fichier :
                    <input type="radio" id="picture" name="video" value="false" checked required>
                    <label for="picture">
                        Image
                    </label>
                    <input type="radio" id="video" name="video" value="true" required>
                    <label id="buttonVideo" for="video">
                        Animation
                    </label>
                    <table>
                        <tr>
                            <td>
                                Durée en secondes :
                            </td>
                            <td>
                                <input type="number" name="duration" 
                                value="<?= htmlspecialchars($_SESSION['file']['video']['duration']) ?>" 
                                step="1" min="1" max="<?= MAX_DURATION ?>" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Images par seconde :
                            </td>
                            <td>
                                <input type="number" name="frequency" 
                                value="<?= htmlspecialchars($_SESSION['file']['video']['frequency']) ?>" 
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
                    <table>
                        <tr>
                            <td>
                                Couleur de fond du fichier :
                            </td>
                            <td>
                                <label>
                                    <input type="color" id="backgroundColor" name="backgroundColor" 
                                    value="<?= htmlspecialchars($_SESSION['scene']['color']) ?>">
                                </label>
                            </td>
                        </tr>
                        <tr><td colspan="2"><hr></td></tr>
                        <tr>
                            <td>
                                Puissance de la source lumineuse :
                            </td>
                            <td>
                                <label title="Entrez une valeur entre 0 et 100">
                                    <input type="number" id="bright" name="bright" 
                                    value="<?= htmlspecialchars($_SESSION['scene']['light'][0]['bright']) ?>" 
                                    step="1" min="0" max="100" required>
                                </label>
                            </td>
                        </tr>
                        <tr><td colspan="2"><hr></td></tr>
                        <tr><td colspan="2">reste des options</td></tr>
                    </table> 
                </td>
                <td>
                    <div id="grid" style="height: <?= htmlspecialchars(25*($_SESSION['file']['dim']['y'] / $_SESSION['file']['dim']['x'])) ?>em;
                    background-color: <?= $_SESSION['scene']['color'] ?>;">
                        <!--Ajoute objets avec des images de fond, que l'on déplace selon les axes x et y, ou des span modifiés en css?-->
                    </div>
                </td>
            </tr>
            <tr><td colspan="2"><br><hr><br></td></tr>
            <tr>
                <td colspan="2" class="listSelection">
                    Choisissez un objet :
                    <select id="shape" name="shape">
                        <option>Aucun</option>
                        <optgroup label="Polyèdres">
                            <option>Surface</option>
                            <option>Pavé</option>
                            <option disabled>Polyèdre</option>
                        <optgroup label="Ellipsoïdes">
                            <option disabled>Disque</option>
                            <option>Sphère</option>
                            <option disabled>Ellipsoïde</option>
                        </optgroup>
                    </select>
                    <input type="submit" name="confirmShape" value="Confirmer">
                </td>
            </tr>
            <tr><td colspan="2"><br></td></tr>
            <tr>
                <td>
                    Ellipsoïdes
                    <?php if (isset($_SESSION['ellipsoid'])) { ?>
                        <div class="listSelection">
                            <?php
                            $i = 0;
                            foreach ($_SESSION['ellipsoid'] as $shape) {
                                $i++;
                                if ($i > 1) echo '<hr>'; ?>
                                <style type="text/css">
                                    #displayElli<?= htmlspecialchars($i) ?>, #displayElli<?= htmlspecialchars($i) ?> + label + table {
                                        display: none;
                                    }
                                    #displayElli<?= htmlspecialchars($i) ?>:checked + label + table {
                                        display: initial;
                                    }
                                </style>

                                <input type="hidden" name="name<?= htmlspecialchars($i) ?>" value="<?= htmlspecialchars($shape['name']) ?>">
                                <input type="checkbox" id="displayElli<?= htmlspecialchars($i) ?>">
                                <label for="displayElli<?= htmlspecialchars($i) ?>">
                                    <table>
                                        <tr>
                                            <td>
                                                <h3>Ellipsoïde <?= htmlspecialchars($i) ?></h3>
                                            </td>
                                            <td>
                                                <h3><?= htmlspecialchars($shape['name']) ?></h3>
                                            </td>
                                            <td>
                                                <button name="delete_<?= htmlspecialchars($i) ?>" value="true">Supprimer</button>
                                            </td>
                                        </tr>
                                    </table>
                                </label>
                                <table>
                                    <tr>
                                        <td>Couleur</td>
                                        <td>
                                            <input type="color" class="color" name="color<?= htmlspecialchars($i) ?>" 
                                            value="<?= htmlspecialchars($shape['color']) ?>">
                                        </td>
                                    </tr>
                                    <tr><td colspan="2"><br></td></tr>
                                    <tr>
                                        <td>Coordonnées du centre</td>
                                        <td>
                                            <input type="number" class="number" name="posX<?= htmlspecialchars($i) ?>" 
                                            value="<?= htmlspecialchars($shape['pos']['x']) ?>" step="<?= STEP_AXIS ?>" required>

                                            <input type="number" class="number" name="posY<?= htmlspecialchars($i) ?>" 
                                            value="<?= htmlspecialchars($shape['pos']['y']) ?>" step="<?= STEP_AXIS ?>" required>

                                            <input type="number" class="number" name="posZ<?= htmlspecialchars($i) ?>" 
                                            value="<?= htmlspecialchars($shape['pos']['z']) ?>" step="<?= STEP_AXIS ?>" required>
                                        </td>
                                    </tr>
                                    <tr><td colspan="2"><br></td></tr>
                                    <tr>
                                        <td>Rayons</td>
                                        <td>
                                            <input type="number" class="number" name="radX<?= htmlspecialchars($i) ?>" 
                                            value="<?= htmlspecialchars($shape['rad']['x']) ?>" step="<?= STEP_AXIS ?>" required>

                                            <input type="number" class="number" name="radY<?= htmlspecialchars($i) ?>" 
                                            value="<?= htmlspecialchars($shape['rad']['y']) ?>" step="<?= STEP_AXIS ?>" required>

                                            <input type="number" class="number" name="radZ<?= htmlspecialchars($i) ?>" 
                                            value="<?= htmlspecialchars($shape['rad']['z']) ?>" step="<?= STEP_AXIS ?>" required>
                                        </td>
                                    </tr>
                                    <tr><td colspan="2"><br></td></tr>
                                    <tr>
                                        <td>Rotation</td>
                                        <td>
                                            <input type="number" class="number" name="rotX<?= htmlspecialchars($i) ?>" 
                                            value="<?= htmlspecialchars($shape['rot']['x']) ?>" step="<?= STEP_AXIS ?>" required>

                                            <input type="number" class="number" name="rotY<?= htmlspecialchars($i) ?>" 
                                            value="<?= htmlspecialchars($shape['rot']['y']) ?>" step="<?= STEP_AXIS ?>" required>

                                            <input type="number" class="number" name="rotZ<?= htmlspecialchars($i) ?>" 
                                            value="<?= htmlspecialchars($shape['rot']['z']) ?>" step="<?= STEP_AXIS ?>" required>
                                        </td>
                                    </tr>
                                </table>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </td>
                <td>
                    Polyèdres
                    <?php if (isset($_SESSION['polyhedron'])) { ?>
                        <div class="listSelection" style="max-height: <?= htmlspecialchars(20*($_SESSION['file']['dim']['y'] / $_SESSION['file']['dim']['x'])) ?>em">
                            <?php foreach ($_SESSION['polyhedron'] as $shape) {
                                if ($i > 1) {
                                    echo '<hr>';
                                } ?>
                                <style type="text/css">
                                    #displayPoly<?= htmlspecialchars($i) ?>, #displayPoly<?= htmlspecialchars($i) ?> + label + table {
                                        display: none;
                                    }
                                    #displayPoly<?= htmlspecialchars($i) ?>:checked + label + table {
                                        display: initial;
                                    }
                                </style>

                                <input type="hidden" name="name<?= htmlspecialchars($i) ?>" value="<?= htmlspecialchars($shape['name']) ?>">
                                <input type="checkbox" id="displayPoly<?= htmlspecialchars($i) ?>">
                                <label for="displayPoly<?= htmlspecialchars($i) ?>">
                                    <table>
                                        <tr>
                                            <td>
                                                <h3>Polyèdre <?= htmlspecialchars($i) ?></h3>
                                            </td>
                                            <td>
                                                <h3><?= htmlspecialchars($shape['name']) ?></h3>
                                            </td>
                                            <td>
                                                <button name="delete_<?= htmlspecialchars($i) ?>" value="true">Supprimer</button>
                                            </td>
                                        </tr>
                                    </table>
                                </label>
                                <?php foreach($shape['face'] as $face) { ?>
                                    <table>
                                        <tr>
                                            <td>Couleur</td>
                                            <td>
                                                <input type="color" class="color" name="color<?= htmlspecialchars($i) ?>" 
                                                value="<?= htmlspecialchars($shape['color']) ?>">
                                            </td>
                                        </tr>
                                        <?php $j = 0;
                                        foreach($face['peak'] as $peak) {
                                            $j++; ?>
                                            <tr>
                                                <td>Coordonnées du point <?= htmlspecialchars($j) ?></td>
                                                <td>
                                                    <input type="number" class="number" name="posX<?= htmlspecialchars($i.'-'.$j) ?>" 
                                                    value="<?= htmlspecialchars($shape['pos']['x']) ?>" step="<?= STEP_AXIS ?>" required>

                                                    <input type="number" class="number" name="posY<?= htmlspecialchars($i.'-'.$j) ?>" 
                                                    value="<?= htmlspecialchars($shape['pos']['y']) ?>" step="<?= STEP_AXIS ?>" required>

                                                    <input type="number" class="number" name="posZ<?= htmlspecialchars($i.'-'.$j) ?>" 
                                                    value="<?= htmlspecialchars($shape['pos']['z']) ?>" step="<?= STEP_AXIS ?>" required>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr><td colspan="2"><br></td></tr>
                                        <tr>
                                            <td>Rotation</td>
                                            <td>
                                                <input type="number" class="number" name="rotX<?= htmlspecialchars($i) ?>" 
                                                value="<?= htmlspecialchars($shape['rot']['x']) ?>" step="<?= STEP_AXIS ?>" required>

                                                <input type="number" class="number" name="rotY<?= htmlspecialchars($i) ?>" 
                                                value="<?= htmlspecialchars($shape['rot']['y']) ?>" step="<?= STEP_AXIS ?>" required>

                                                <input type="number" class="number" name="rotZ<?= htmlspecialchars($i) ?>" 
                                                value="<?= htmlspecialchars($shape['rot']['z']) ?>" step="<?= STEP_AXIS ?>" required>
                                            </td>
                                        </tr>
                                    </table>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
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

//remplissage du template
require('View/Template.php');
