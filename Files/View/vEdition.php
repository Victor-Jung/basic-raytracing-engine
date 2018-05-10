<?php
define('CHOIX_RESOLUTION', 8);
define('DUREE_MAX', 5*60);
define('LENGTH_FILE_NAME', 20);
define('MIN_GROWTH', -10);
define('MAX_GROWTH', 10);
define('STEP_GROWTH', 0.1);
define('STEP_AXIS', 0.5);
$page['dimXimg'] = 768;
$page['dimYimg'] = 768;

$page['pageName'] = 'Edition d\'image';
$page['actual'] = 'edit';
$page['script'] = false;

function definition($axis) { ?>
    <select id="def<?= $axis ?>" name="def<?= $axis ?>" required>
        <?php for ($i = 1; $i <= CHOIX_RESOLUTION; $i++) {
            $pixels = 256*$i;
            echo '<option';
            if ((isset($var) && $var == $pixels) || 
                (!isset($var) && $i == 3)) {
                echo ' selected';
            }
            echo '>'.$pixels;
        } ?>
    </select>
<?php }

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
            <tr id="file">
                <td>
                    <fieldset>
                        <legend>Caractérisation du fichier</legend>
                        <form method="post" action="index.php?action=edit">
                            <input type="hidden" name="script" value="fileConfig">

                            <table>
                                <tr>
                                    <td>
                                        <label>
                                            Nom du fichier :
                                            <input type="text" id="fileName" name="fileName" maxlength="<?= LENGTH_FILE_NAME ?>" value="<?php if (isset($val)) echo htmlspecialchars($val); ?>" required>
                                        </label>
                                    </td>
                                    <td>
                                        Type de fichier :
                                        <label><input type="radio" id="typeFile" name="typeFile" value="picture" checked required>Image</label>
                                        <label><input type="radio" id="typeFile" name="typeFile" value="video" required disabled>Animation</label>
                                    </td>
                                </tr>
                                <tr><td colspan="2"><br></td></tr>
                                <tr>
                                    <td>
                                        Dimensions du fichier :
                                        <?php definition("X") ?> x <?php definition("Y") ?> pixels
                                    </td>
                                    <td>
                                        <!--(if (typeFile == video) afficher bloc animation)-->
                                        <table id="animation">
                                            <tr>
                                                <td>
                                                    Durée en secondes :
                                                </td>
                                                <td>
                                                    <input type="number" id="duration" name="duration" value=<?php if (isset($val)) echo htmlspecialchars($val); ?> step="1" min="1" max="<?= DUREE_MAX ?>" required disabled>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    Images par seconde :
                                                </td>
                                                <td>
                                                    <input type="number" id="frequency" name="frequency" value=<?php if (isset($val)) echo htmlspecialchars($val); ?> step="1" min="1" max="60" required disabled>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <?php if (!isset($var)) { ?>
                                    <tr><td colspan="2"><br><hr></td></tr>
                                    <tr>
                                        <td>
                                            <label><input type="checkbox" id="nextStep" name="nextStep" value="validation">Passer à l'étape suivante</label>
                                        </td>
                                        <td>
                                            <input type="submit" value="Actualiser">
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </form>
                    </fieldset>
                </td>
            </tr>

            <tr id="scene">
                <!--n'afficher que quand le premier est validé -> retire le bouton de la première partie-->
                <td>
                    <fieldset>
                        <legend>Choix des objets et caractérisation</legend>
                        <form method="post" action="index.php?action=edit">
                            <input type="hidden" name="script" value="sceneConfig">

                            <table>
                                <tr>
                                    <td>
                                        Luminosité du fichier :
                                        <input type="number" id="bright" name="bright" value=<?php if (isset($val)) echo htmlspecialchars($val); else echo 50 ?> step="1" min="1" max="100" required>
                                    </td>
                                    <td>
                                        Couleur de fond du fichier :
                                        <input type="color" id="fileName" name="fileName" maxlength="30" value="<?php if (isset($val)) echo htmlspecialchars($val); ?>">
                                    </td>
                                </tr>
                                <tr><td colspan="2"><br></td></tr>
                                <tr>
                                    <td>
                                        <table id="shapeSelection">
                                            <tr>
                                                <td>
                                                    <label>
                                                        Choisissez une forme :<br>
                                                        <select>
                                                            <optgroup label="Formes simples">
                                                                <option>Cube</option>
                                                                <option>Sphère</option>
                                                            </optgroup>
                                                            <optgroup label="Formes avancées" disabled>
                                                                <option>Pyramide (3 faces)</option>
                                                                <option>Pyramide (4 faces)</option>
                                                            </optgroup>
                                                            <optgroup label="Formes complexes" disabled>
                                                                <option>(Courbes)</option>
                                                            </optgroup>
                                                        </select>
                                                    </label>
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
                                                        <tr>
                                                            <th>Grossissement :</th>
                                                            <td>
                                                                <input type="number" id="growth" name="growth" value=<?php if (isset($val)) echo htmlspecialchars($val); else echo 1 ?> step="<?= STEP_GROWTH ?>" min="<?= MIN_GROWTH ?>" max="<?= MAX_GROWTH ?>" required>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Positionnement :</th>
                                                            <td>
                                                                <input type="number" id="xAxis" name="xAxis" value=<?php if (isset($val)) echo htmlspecialchars($val); else echo 1 ?> step="<?= STEP_AXIS ?>" min="0" max="<?= htmlspecialchars($page['dimXimg']) ?>" required>
                                                                <br>
                                                                <input type="number" id="yAxis" name="yAxis" value=<?php if (isset($val)) echo htmlspecialchars($val); else echo 1 ?> step="<?= STEP_AXIS ?>" min="0" max="<?= htmlspecialchars($page['dimYimg']) ?>" required>
                                                                <br>
                                                                <input type="number" id="zAxis" name="zAxis" value=<?php if (isset($val)) echo htmlspecialchars($val); else echo 1 ?> step="<?= STEP_AXIS ?>" min="0" max="<?= MAX_Z_IMG ?>" required>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table>
                                                        <tr>
                                                            <th>Forme :</th>
                                                            <td>(Nom)</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Grossissement :</th>
                                                            <td>
                                                                <input type="number" id="growth" name="growth" value=<?php if (isset($val)) echo htmlspecialchars($val); else echo 1 ?> step="<?= STEP_GROWTH ?>" min="<?= MIN_GROWTH ?>" max="<?= MAX_GROWTH ?>" required>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Positionnement :</th>
                                                            <td>
                                                                <input type="number" id="xAxis" name="xAxis" value=<?php if (isset($val)) echo htmlspecialchars($val); else echo 1 ?> step="<?= STEP_AXIS ?>" min="0" max="<?= htmlspecialchars($page['dimXimg']) ?>" required>
                                                                <br>
                                                                <input type="number" id="yAxis" name="yAxis" value=<?php if (isset($val)) echo htmlspecialchars($val); else echo 1 ?> step="<?= STEP_AXIS ?>" min="0" max="<?= htmlspecialchars($page['dimYimg']) ?>" required>
                                                                <br>
                                                                <input type="number" id="zAxis" name="zAxis" value=<?php if (isset($val)) echo htmlspecialchars($val); else echo 1 ?> step="<?= STEP_AXIS ?>" min="0" max="<?= MAX_Z_IMG ?>" required>
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
                                            et background-size: cover; si plus large que haut-->
                                            <!--Ajoute objets avec des images de fond, que l'on déplace selon les axes x et y, ou des span modifiés en css?-->
                                        </div>
                                    </td>
                                </tr>
                                <?php if (!isset($var)) { ?>
                                    <tr><td colspan="2"><br><hr></td></tr>
                                    <tr>
                                        <td>
                                            <label><input type="checkbox" id="nextStep" name="nextStep" value="validation">Passer à l'étape suivante</label>
                                        </td>
                                        <td>
                                            <input type="submit" value="Actualiser">
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </form>
                    </fieldset>
                </td>
            </tr>

            <tr class="finishes">
                <td>
                    <fieldset>
                        <legend>Validation du résultat</legend>
                        <form method="post" action="index.php?action=edit">
                            <input type="hidden" name="script" value="postprodConfig">
                            <table>
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
                                <?php if (!isset($var)) { ?>
                                    <tr><td colspan="2"><br><hr></td></tr>
                                    <tr>
                                        <td>
                                            <label><input type="checkbox" id="nextStep" name="nextStep" value="validation">Conserver les données</label>
                                        </td>
                                        <td>
                                            <input type="submit" value="Nouveau fichier">
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        </form>
                    </fieldset>
                </td>
            </tr>
        </tbody>
    </table>
<?php $template['section'] = ob_get_clean();


require('View/Template.php');
