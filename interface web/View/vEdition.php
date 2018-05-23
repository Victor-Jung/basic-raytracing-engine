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
                                    Position observateur :
                                </td>
                                <td>
                                    <?= htmlspecialchars($_SESSION['scene']['viewer']['x']
                                    .';'.$_SESSION['scene']['viewer']['y']
                                    .';'.$_SESSION['scene']['viewer']['z']) ?>
                                </td>
                            </tr>
                            <tr><td colspan="2"><hr></td></tr>
                            <tr>
                                <th colspan="2">
                                    Source lumineuse
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    Puissance :
                                </td>
                                <td>
                                    <?= htmlspecialchars($_SESSION['scene']['light'][0]['bright']) ?>%
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Position :
                                </td>
                                <td>
                                    <?= htmlspecialchars($_SESSION['scene']['light'][0]['pos']['x']
                                    .';'.$_SESSION['scene']['light'][0]['pos']['y']
                                    .';'.$_SESSION['scene']['light'][0]['pos']['z']) ?>
                                </td>
                            </tr>
                            <tr><td colspan="2"><hr></td></tr>
                            <tr>
                                <td>
                                    Effets de lumière :
                                </td>
                                <td>
                                    <?= $_SESSION['file']['effects']['shadows']? 'Ombres<br>' : '' ?>
                                    <?= $_SESSION['file']['effects']['reflection']? 'Réflexion<br>' : '' ?>
                                    <?= $_SESSION['file']['effects']['refraction']? 'Réfraction<br>' : '' ?>
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
                    <input type="radio" id="picture" name="video" value="0"
                    <?= (!$_SESSION['file']['video']['selected'])? 'checked' : '' ?> required>
                    <label for="picture">
                        Image
                    </label>
                    <input type="radio" id="video" name="video" value="1"
                    <?= ($_SESSION['file']['video']['selected'])? 'checked' : '' ?> required>
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
                            <td colspan="2">
                                <h3>Fond</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Couleur :
                            </td>
                            <td>
                                <label>
                                    <input type="color" id="sceneColor" name="sceneColor" 
                                    value="<?= htmlspecialchars($_SESSION['scene']['color']) ?>">
                                </label>
                            </td>
                        </tr>
                        <tr><td colspan="2"><hr></td></tr>
                        <tr>
                            <td colspan="2">
                                <h3>Observateur</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Position :
                            </td>
                            <td>
                                <input type="number" class="smallNumber" name="viewerX" 
                                value="<?= htmlspecialchars($_SESSION['scene']['viewer']['x']) ?>" 
                                step="1" required>
                                -
                                <input type="number" class="smallNumber" name="viewerY" 
                                value="<?= htmlspecialchars($_SESSION['scene']['viewer']['y']) ?>" 
                                step="1" required>
                                -
                                <input type="number" class="smallNumber" name="viewerZ" 
                                value="<?= htmlspecialchars($_SESSION['scene']['viewer']['z']) ?>" 
                                step="1" required>
                            </td>
                        </tr>
                        <tr><td colspan="2"><hr></td></tr>
                        <tr>
                            <td colspan="2">
                                <h3>Source lumineuse</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Puissance :
                            </td>
                            <td>
                                <input type="number" class="smallNumber" name="bright" 
                                value="<?= htmlspecialchars($_SESSION['scene']['light'][0]['bright']) ?>" 
                                step="0.5" min="0" max="100" required>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Position :
                            </td>
                            <td>
                                <input type="number" class="smallNumber" name="lightX" 
                                value="<?= htmlspecialchars($_SESSION['scene']['light'][0]['pos']['x']) ?>" 
                                step="1" required>
                                -
                                <input type="number" class="smallNumber" name="lightY" 
                                value="<?= htmlspecialchars($_SESSION['scene']['light'][0]['pos']['y']) ?>" 
                                step="1" required>
                                -
                                <input type="number" class="smallNumber" name="lightZ" 
                                value="<?= htmlspecialchars($_SESSION['scene']['light'][0]['pos']['z']) ?>" 
                                step="1" required>
                            </td>
                        </tr>
                        <tr><td colspan="2"><hr></td></tr>
                        <tr>
                            <td colspan="2">
                                <h3>Effets de lumière</h3>
                                <table class="tab3col">
                                    <tr>
                                        <td>
                                            <input type="checkbox" id="shadows" name="shadows" value="1" 
                                            <?= $_SESSION['file']['effects']['shadows']? 'checked' : '' ?>>
                                            <label for="shadows">Ombres</label>
                                        </td>
                                        <td>
                                            <input type="checkbox" id="reflection" name="reflection" value="1"
                                            <?= $_SESSION['file']['effects']['reflection']? 'checked' : '' ?>>
                                            <label for="reflection">Réflexion</label>
                                        </td>
                                        <td>
                                            <input type="checkbox" id="refraction" name="refraction" value="1"
                                            <?= $_SESSION['file']['effects']['refraction']? 'checked' : '' ?>>
                                            <label for="refraction">Réfraction</label>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
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
                        <option selected>Aucun</option>
                        <option>Ellipsoïde</option>
                        <option>Polyèdre</option>
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
                    <?php if (isset($_SESSION['polyhedron'])) {
                        $idPoly = 0; ?>
                        <div class="listSelection">
                            <?php foreach ($_SESSION['polyhedron'] as $shape) {
                                $idPoly++;
                                if ($idPoly > 1) {
                                    echo '<hr>';
                                } ?>
                                <style type="text/css">
                                    #displayPoly<?= htmlspecialchars($idPoly) ?>, #displayPoly<?= htmlspecialchars($idPoly) ?> + label + div {
                                        display: none;
                                    }
                                    #displayPoly<?= htmlspecialchars($idPoly) ?>:checked + label + div {
                                        display: initial;
                                    }
                                </style>

                                <input type="hidden" name="name<?= htmlspecialchars($idPoly) ?>" value="<?= htmlspecialchars($shape['name']) ?>">
                                <input type="checkbox" id="displayPoly<?= htmlspecialchars($idPoly) ?>">
                                <label for="displayPoly<?= htmlspecialchars($idPoly) ?>">
                                    <h3><?= htmlspecialchars($shape['name']) ?> <?= htmlspecialchars($idPoly) ?> - <button name="delete_<?= htmlspecialchars($idPoly) ?>" value="true">Supprimer</button></h3>
                                </label>
                                <div>
                                    Choisissez le nombre de faces :
                                    <select id="<?= htmlspecialchars('poly'.$idPoly) ?>" name="<?= htmlspecialchars('poly'.$idPoly) ?>">_face+$idFace
                                        <?php for ($nbFace = 1; $nbFace <= 10; $nbFace++) {
                                            echo '<option>'.$nbFace.'</option>';
                                        } ?>
                                    </select>
                                    <div id="<?= htmlspecialchars('showPoly'.$idPoly) ?>">
                                        <script>
                                            $("#poly"+htmlspecialchars($idPoly)).on("change", load);	
                                            function load(){
                                                    alert("ok");
                                                var nbFaces = $("#poly"+htmlspecialchars($idPoly)).val();
                                                var texte="<table>";
                                                $("#showPoly"+htmlspecialchars($idPoly)).text("");
                                                for(var facePoly=1; facePoly<=nbFaces; facePoly++){
                                                    texte += "<tr id=\"poly"+htmlspecialchars($idPoly)+"_face"+facePoly+"\">";
                                                    texte += "<td>Color</td>";
                                                    texte += "</tr>";
                                                }
                                                texte += "</table>";
                                                $("#show").html(texte);
                                            }
                                        </script>
                                    </div>
                                </div>

                                <div>selecteur nombre de poly</div>
                                <div>
                                    affichage des poly
                                    <div>selecteur nombre de faces</div>
                                    <div>
                                        affichage des faces
                                        <div> selecteur nombre de sommets</div>
                                        <div>
                                            affichage des sommets
                                        </div>
                                    </div>
                                </div>



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
                            <option>Aucune</option>
                            <option>Anti-aliasing</option>
                            <option>Filtre lumineux</option>
                        </select>
                    </label>
                    <input type="submit" value="Confirmer">
                </td>
                <td>
                    <img src="../link/preview.bmp" alt="Fichier produit" width="100%">
                </td>
            </tr>
        <?php $edition['content']['fillable'] = ob_get_clean();
    break;
}

//remplissage du template
require('View/Template.php');
