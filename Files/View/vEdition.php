<?php
define('CHOIX_RESOLUTION', 8);
define('DUREE_MAX', 5*60);
define('LENGTH_FILE_NAME', 20);
define('MIN_GROWTH', -10);
define('MAX_GROWTH', 10);
define('STEP_GROWTH', 0.1);
define('STEP_AXIS', 4);

// debut provisoire
$_SESSION['fileConfig'] = true;//determine affchage du 2e cadre
$_SESSION['sceneConfig'] = true;//determine affchage du 3e cadre

$page['dimXimg'] = 768;
$page['dimYimg'] = 768;

$page['pageName'] = 'Edition d\'image';
$page['actual'] = 'edit';
$page['script'] = false;

$page['fileName'] = 'test';
$page['fileType'] = 'BMP';
$page['fileXdim'] = $page['fileYdim'] = 768;
$page['fileBright'] = 50;
$page['fileBackColor'] = '#000000';
// fin provisoire


$edition['display'][1] = true; // (isset($_SESSION['fileConfig']) && !$_SESSION['fileConfig']) ? true : false;
$edition['display'][2] = (isset($_SESSION['fileConfig']) && $_SESSION['fileConfig']) ? true : false;
$edition['display'][3] = (isset($_SESSION['sceneConfig']) && $_SESSION['sceneConfig']) ? true : false;

$edition['modify'][1] = (isset($_SESSION['fileConfig']) && !$_SESSION['fileConfig']) ? true : false;
$edition['modify'][2] = (isset($_SESSION['sceneConfig']) && !$_SESSION['sceneConfig']) ? true : false;
$edition['modify'][3] = true; // (isset($_SESSION['sceneConfig']) && $_SESSION['sceneConfig']) ? true : false;

$edition['legend'][1] = 'Caractérisation du fichier';
$edition['legend'][2] = 'Choix des objets et caractérisation';
$edition['legend'][3] = 'Validation du résultat';

$edition['script'][1] = 'fileConfig';
$edition['script'][2] = 'sceneConfig';
$edition['script'][3] = 'finishesConfig';

$edition['button']['message'][1] = $edition['button']['message'][2] = 'Passer à l\'étape suivante';
$edition['button']['value'][1]   = $edition['button']['value'][2]   = 'Actualiser';
$edition['button']['message'][3] = 'Conserver les données';
$edition['button']['value'][3]   = 'Nouveau fichier';


function definition($axis) { ?>
    <select id="def<?= $axis ?>" name="def<?= $axis ?>" required>
        <?php for ($i = 1; $i <= CHOIX_RESOLUTION; $i++) {
            $pixels = 256*$i;

            echo '<option';
            if ((isset($var) && $var == $pixels) || (!isset($var) && $i == 3)) {
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
                <input type="text" id="fileName" name="fileName" maxlength="<?= LENGTH_FILE_NAME ?>" 
                value="<?php if (isset($val)) echo htmlspecialchars($val) ?>" required
                <?php if (!$edition['modify'][1]) echo 'disabled'; ?>>
            </label>
        </td>
        <td>
            Type de fichier :
            <label>
                <input type="radio" id="typeFile" name="typeFile" value="picture" checked required>
                Image
            </label>
            <label>
                <input type="radio" id="typeFile" name="typeFile" value="video" required disabled>
                Animation
            </label>
        </td>
    </tr>
    <tr><td colspan="2"><br></td></tr>
    <tr>
        <td>
            Dimensions du fichier :
            <?php definition('X', $edition['modify'][1]) ?> x <?php definition('Y', $edition['modify'][1]) ?> pixels
        </td>
        <td>
            <!--(if (typeFile == video) afficher bloc animation)-->
            <table id="animation">
                <tr>
                    <td>
                        Durée en secondes :
                    </td>
                    <td>
                        <input type="number" id="duration" name="duration" value=<?php if (isset($val)) echo htmlspecialchars($val); ?> 
                        step="1" min="1" max="<?= DUREE_MAX ?>" required disabled>
                    </td>
                </tr>
                <tr>
                    <td>
                        Images par seconde :
                    </td>
                    <td>
                        <input type="number" id="frequency" name="frequency" value=<?php if (isset($val)) echo htmlspecialchars($val); ?> 
                        step="1" min="1" max="60" required disabled>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
<?php $edition['content'][1]['fillable'] = ob_get_clean();

ob_start(); ?>
    Fichier : <?= $page['fileName'] ?>.<?= $page['fileType'] ?>
<?php $edition['content'][1]['fixed'][1] = ob_get_clean();

ob_start(); ?>
    Dimensions du fichier : <?= $page['fileXdim'] ?> x <?= $page['fileYdim'] ?> pixels
<?php $edition['content'][1]['fixed'][2] = ob_get_clean();


ob_start(); ?>
    <tr>
        <td>
            <label>
                Luminosité du fichier :
                <input type="number" id="bright" name="bright" value=<?php echo (isset($val)) ? htmlspecialchars($val) : 50; ?> step="1" min="0" max="100" required>
            </label>
        </td>
        <td>
            <label>
                Couleur de fond du fichier :
                <input type="color" id="fileName" name="fileName" value="<?php if (isset($val)) echo htmlspecialchars($val); ?>">
            </label>
        </td>
    </tr>
    <tr><td colspan="2"><br></td></tr>
    <tr>
        <td>
            <table id="shapeSelection">
                <tr>
                    <td>
                        Choisissez une forme :<br>
                        <select>
                            <optgroup label="Formes simples">
                                <option>Cube</option>
                                <option>Sphère</option>
                            </optgroup>
                            <optgroup label="Formes avancées" disabled>
                                <option>Pyramide</option>
                            </optgroup>
                            <optgroup label="Formes complexes" disabled>
                                <option>(Courbes)</option>
                            </optgroup>
                        </select>
                        <input type="submit" value="Confirmer">
                    </td>
                </tr>
                <tr>
                    <td>
                        <table>
                            <tr>
                                <th>Forme :</th>
                                <td>(Nom)</td>
                            </tr>
                            <tr><td colspan="2"><br></td></tr>
                            <tr>
                                <th>Position X :</th>
                                <td>
                                    <input type="number" id="xAxis" name="xAxis" value=<?php echo (isset($val)) ? htmlspecialchars($val) : htmlspecialchars($page['dimXimg'] / 2); ?> 
                                    step="<?= STEP_AXIS ?>" min="0" max="<?= htmlspecialchars($page['dimXimg']) ?>" required>
                                </td>
                            </tr>
                            <tr>
                                <th>Position Y :</th>
                                <td>
                                    <input type="number" id="yAxis" name="yAxis" value=<?php echo (isset($val)) ? htmlspecialchars($val) : htmlspecialchars($page['dimYimg'] / 2); ?> 
                                    step="<?= STEP_AXIS ?>" min="0" max="<?= htmlspecialchars($page['dimYimg']) ?>" required>
                                </td>
                            </tr>
                            <tr>
                                <th>Position Z :</th>
                                <td>
                                    <input type="number" id="zAxis" name="zAxis" value=<?php echo (isset($val)) ? htmlspecialchars($val) : MAX_Z_IMG / 2; ?> 
                                    step="5" min="0" max="<?= MAX_Z_IMG ?>" required>
                                </td>
                            </tr>
                            <tr><td colspan="2"><br></td></tr>
                            <tr>
                                <th>Grossissement :</th>
                                <td>
                                    <input type="number" id="growth" name="growth" value=<?php echo (isset($val)) ? htmlspecialchars($val) : 0; ?> 
                                    step="<?= STEP_GROWTH ?>" min="<?= MIN_GROWTH ?>" max="<?= MAX_GROWTH ?>" required>
                                </td>
                            </tr>
                            <tr><td colspan="2"><br></td></tr>
                            <tr>
                                <th>Couleur :</th>
                                <td>
                                    <input type="color" id="color" name="color" value="<?php if (isset($val)) echo htmlspecialchars($val); ?>">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td>
            <div id="grid">
                <!--peut agir sur width et height pour représenter l'image finale, 
                avec background-size: contain; si plus haut que large
                et background-size: cover; si plus large que haut
                en ajoutant  style="property:value;"-->
                <!--Ajoute objets avec des images de fond, que l'on déplace selon les axes x et y, ou des span modifiés en css?-->
            </div>
        </td>
    </tr>
<?php $edition['content'][2]['fillable'] = ob_get_clean();

ob_start(); ?>
    Luminosité du fichier : <?= $page['fileBright'] ?>%<br>
    Couleur de fond du fichier : <?= $page['fileBackColor'] ?><br>
    <br>
    <div class="fiche">
        (nom forme) (numero forme)<br>
        <br>
        Positionnement :<br>
        (valeur) - (valeur) - (valeur)<br>
        <br>
        Couleur : (ref)<br>
        <br>
        Grossit : (valeur) fois
    </div>
<?php $edition['content'][2]['fixed'][1] = ob_get_clean();

ob_start(); ?>
    <div id="grid">
        <!--peut agir sur width et height pour représenter l'image finale, 
        avec background-size: contain; si plus haut que large
        et background-size: cover; si plus large que haut
        en ajoutant  style="property:value;"-->
        <!--Ajoute objets avec des images de fond, que l'on déplace selon les axes x et y, ou des span modifiés en css?-->
    </div>
<?php $edition['content'][2]['fixed'][2] = ob_get_clean();


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
<?php $edition['content'][3]['fillable'] = ob_get_clean();




ob_start(); ?>
    <table>
        <thead>
            <tr>
                <th>
                    Édition d'image ou de vidéo
                </th>
            </tr>
        </thead>
        <tbody>
            <?php for($i = 1; $i < 4; $i++) {
                if ($edition['display'][$i]) {
                    if ($edition['modify'][$i]) { ?>
                        <tr>
                            <td>
                                <fieldset>
                                    <legend><?= $edition['legend'][$i] ?></legend>
                                    <form method="post" action="index.php?action=edit">
                                        <input type="hidden" name="script" value="<?= $edition['script'][$i] ?>">
                                        <table>

                                            <?= $edition['content'][$i]['fillable'] ?>
                                            
                                            <?php if ($edition['modify'][$i]) { ?>
                                                <tr><td colspan="2"><br><hr></td></tr>
                                                <tr>
                                                    <td>
                                                        <label><input type="checkbox" id="nextStep" name="nextStep" value="validation"><?= $edition['button']['message'][$i] ?></label>
                                                    </td>
                                                    <td>
                                                        <input type="submit" value="<?= $edition['button']['value'][$i] ?>">
                                                    </td>
                                                </tr>
                                            <?php }?>

                                        </table>
                                    </form>
                                </fieldset>
                            </td>
                        </tr>
                    <?php }
                    else {
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
                                                <?= $edition['legend'][$i] ?>
                                            </th>
                                            <td>
                                                <?= $edition['content'][$i]['fixed'][1] ?>
                                            </td>
                                            <td>
                                                <?= $edition['content'][$i]['fixed'][2] ?>
                                            </td>
                                        </tr>
                                        
                                        <?php if (($i == 1 && $edition['modify'][2]) || $i == 2) { ?>
                                        <tr>
                                            <td colspan="3"><br></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        <?php }
                    }
                }
            } ?>
        </tbody>
    </table>
<?php $template['section'] = ob_get_clean();


require('View/Template.php');
