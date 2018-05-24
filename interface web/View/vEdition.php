<?php
//fragments de code de la page
if ($_SESSION['pageBlock'] > 0) {
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
if ($_SESSION['pageBlock'] == 2) {
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
                                    Effets :
                                </td>
                                <td>
                                    <?= $_SESSION['file']['effects']['shadows']? 'Ombres<br>' : '' ?>
                                    <?= $_SESSION['file']['effects']['aliasing']? 'Anti-aliasing<br>' : '' ?>
                                </td>
                            </tr>
                        </table>
<!--
                        < ?php if (isset($_SESSION['ellipsoid']) && count($_SESSION['ellipsoid']) > 0) {
                            $i = 0;
                            foreach ($_SESSION['ellipsoid'] as $shape) {
                                $i++; ?>
                                <br>
                                <table class="fiche">
                                    <tr>
                                        <th colspan="2">
                                            Objet < ?= htmlspecialchars($i.' : '.$shape['name']) ?>
                                        </th>
                                    </tr>
                                    <tr><th colspan="2"><br></th></tr>
                                    <tr>
                                        <td>
                                            Couleur :
                                        </td>
                                        <td>
                                            <input type="color" value="< ?= htmlspecialchars($shape['color']) ?>" disabled>
                                        </td>
                                    </tr>
                                    
                                    <tr><td colspan="2"><br></td></tr>
                                    <tr>
                                        <td>Centre :</td>
                                        <td>
                                            < ?= htmlspecialchars($shape['pos']['x'].'-'.$shape['pos']['y'].'-'.$shape['pos']['z']) ?>
                                        </td>
                                    </tr>
                                    <tr><td colspan="2"><br></td></tr>
                                    <tr>
                                        <td>Rayon :</td>
                                        <td>
                                            < ?= htmlspecialchars($shape['rad']['x'].'-'.$shape['rad']['y'].'-'.$shape['rad']['z']) ?>    
                                        </td>
                                    </tr>
                                    <tr><td colspan="2"><br></td></tr>
                                    <tr>
                                        <td>Rotation :</td>
                                        <td>
                                            < ?= (empty($shape['rot']['x']))? 0 : htmlspecialchars($shape['rot']['x']) ?>
                                            -
                                            < ?= (empty($shape['rot']['y']))? 0 : htmlspecialchars($shape['rot']['y']) ?>
                                            -
                                            < ?= (empty($shape['rot']['z']))? 0 : htmlspecialchars($shape['rot']['z']) ?>
                                        </td>
                                    </tr>
                                </table>
                            < ?php }
                        }
                        if (isset($_SESSION['polyhedron']) && count($_SESSION['polyhedron']) > 0) {
                            $i = 0;
                            foreach ($_SESSION['polyhedron'] as $shape) {
                                $i++; ?>
                                <br>
                                <table class="fiche">
                                    <tr>
                                        <th colspan="2">
                                            Objet < ?= htmlspecialchars($i.' : '.$shape['name']) ?>
                                        </th>
                                    </tr>
                                    <tr><th colspan="2"><br></th></tr>
                                    < ?php foreach($shape['face'] as $face) { ?>
                                        <tr>
                                            <td>
                                                Couleur :
                                            </td>
                                            <td>
                                                <input type="color" value="< ?= htmlspecialchars($face['color']) ?>" disabled>
                                            </td>
                                        </tr>
                                        <tr><td colspan="2"><br></td></tr>
                                        < ?php $j = 0;
                                        foreach($face['peak'] as $peak) {
                                            $j++; ?>
                                            <tr>
                                                <td colspan="2">
                                                    Sommet < ?= htmlspecialchars($j) ?> : position 
                                                    < ?= htmlspecialchars($peak['x'].'-'.$peak['y'].'-'.$peak['z']) ?>
                                                </td>
                                            </tr>
                                        < ?php }
                                    } ?>
                                </table>
                            < ?php }
                        } ?>
-->
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
    case 0:
        $edition['display'] = array(true, false, false);
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
                        <tr>
                            <td colspan="2">
                                <br><hr><br>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" id="video">
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
                                        <td colspan="2">
                                            <br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Durée en secondes :
                                        </td>
                                        <td>
                                            <input type="number" class="number" name="duration" 
                                            value="<?= htmlspecialchars($_SESSION['file']['video']['duration']) ?>" 
                                            step="1" min="1" max="<?= MAX_DURATION ?>" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Images par seconde :
                                        </td>
                                        <td>
                                            <input type="number" class="number" name="frequency" 
                                            value="<?= htmlspecialchars($_SESSION['file']['video']['frequency']) ?>" 
                                            step="1" min="1" max="60" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Déplacement caméra :
                                        </td>
                                        <td>
                                            <input type="number" class="xSmallNumber" name="moveX" 
                                            value="<?= htmlspecialchars($_SESSION['file']['video']['move']['x']) ?>" 
                                            step="1" required>
                                            -
                                            <input type="number" class="xSmallNumber" name="moveY" 
                                            value="<?= htmlspecialchars($_SESSION['file']['video']['move']['y']) ?>" 
                                            step="1" required>
                                            -
                                            <input type="number" class="xSmallNumber" name="moveZ" 
                                            value="<?= htmlspecialchars($_SESSION['file']['video']['move']['z']) ?>" 
                                            step="1" required>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <br><hr><br>
                            </td>
                        </tr>
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
                <td>
                    <table>
                        <tr>
                            <td>
                                Position caméra :
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
                        <tr>
                            <td colspan="2">
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Position de la lumière :
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
                        <tr>
                            <td>
                                Puissance de la lumière :
                            </td>
                            <td>
                                <input type="number" class="number" name="bright" 
                                value="<?= htmlspecialchars($_SESSION['scene']['light'][0]['bright']) ?>" 
                                step="0.5" min="0" max="100" required>%
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <br><hr><br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Couleur de fond :
                            </td>
                            <td>
                                <input type="color" id="sceneColor" name="sceneColor" 
                                value="<?= htmlspecialchars($_SESSION['scene']['color']) ?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="checkbox" id="shadows" name="shadows" value="1" 
                                <?= $_SESSION['file']['effects']['shadows']? 'checked' : '' ?>>
                                <label for="shadows">Ombres</label>
                            </td>   
                            <td>
                                <input type="checkbox" id="aliasing" name="aliasing" value="1"
                                <?= $_SESSION['file']['effects']['aliasing']? 'checked' : '' ?>>
                                <label for="aliasing">Anti-Aliasing</label>
                            </td>   
                        </tr>
                    </table>
                </td>
            </tr>
        <?php $edition['content']['fillable'] = ob_get_clean();
    break;
    case 1:
        $edition['display'] = array(true, true, false);
        $edition['script'] = 'sceneConfig';

        ob_start(); ?>
            <tr>
                <td>
                    Nombre d'ellipsoïdes : <select id="selectElli" name="selectElli"></select>
                </td>
                <td>
                    Nombre de polyèdres: <select id="selectPoly" name="selectPoly"></select>
                </td>
            </tr>
            <tr>
                <td>
                    <div id="allElli">
                        <script>
                            $(document).ready(function() {
                                var texte = "";
                                var nbElli = 0;
                                
                                for(var i = 0; i<=6; i++){
                                    texte+="<option>"+i+"</option>"; //creation des options
                                }
                                
                                $("#selectElli").html(texte);
                                $("#selectElli").on("change", loadElli);
                                
                                function loadElli(){
                                    nbElli = $("#selectElli").val();
                                    texte = $("#allElli").val();
                                    if(texte==undefined){
                                        texte=" ";
                                    }
                                    if(nbElli==0){
                                        $("#allElli").html("");
                                    }
                                    <?php $idElli = 0; ?>
                                    for(var i=1; i <= nbElli; i++){
                                        <?php 
                                            $idElli++;
                                            if (!isset($_SESSION['ellipsoid'][$idElli])) {
                                                $_SESSION['ellipsoid'][$idElli]['pos'] = array('x' => 1, 'y' => 1, 'z' => 1);
                                                $_SESSION['ellipsoid'][$idElli]['rad'] = array('x' => 1, 'y' => 1, 'z' => 1);
                                            }
                                        ?>
                                        texte+= '<br><hr><br>';
                                        texte+= '<table><tr><td>';
                                                    texte+= 'Couleur : ';
                                                texte+= '</td><td>';
                                                    texte+= '<input type="color" name="elli'+i+'_color" value="#ffffff">';
                                            texte+= '</td></tr><tr><td>';
                                                    texte+= 'Position du centre : ';
                                                texte+= '</td><td>';
                                                    texte+= '<input type="number" class="smallNumber" name="elli'+i+'_xPos" value="'+<?= $_SESSION['ellipsoid'][$idElli]['pos']['x'] ?>+'"> - ';
                                                    texte+= '<input type="number" class="smallNumber" name="elli'+i+'_yPos" value="'+<?= $_SESSION['ellipsoid'][$idElli]['pos']['y'] ?>+'"> - ';
                                                    texte+= '<input type="number" class="smallNumber" name="elli'+i+'_zPos" value="'+<?= $_SESSION['ellipsoid'][$idElli]['pos']['z'] ?>+'">';
                                            texte+= '</td></tr><tr><td>';
                                                    texte+= 'Rayons : ';
                                                texte+= '</td><td>';
                                                    texte+= '<input type="number" class="smallNumber" name="elli'+i+'_xRad" value="'+<?= $_SESSION['ellipsoid'][$idElli]['rad']['x'] ?>+'"> - ';
                                                    texte+= '<input type="number" class="smallNumber" name="elli'+i+'_yRad" value="'+<?= $_SESSION['ellipsoid'][$idElli]['rad']['y'] ?>+'"> - ';
                                                    texte+= '<input type="number" class="smallNumber" name="elli'+i+'_zRad" value="'+<?= $_SESSION['ellipsoid'][$idElli]['rad']['z'] ?>+'">';
                                        texte+= '</td></tr></table>';
                                        $("#allElli").html(texte);
                                    }
                                }		
                                loadElli();
                            });
                        </script>
                    </div>
                </td>
                <td>
                    <div id="allPoly">
                        <script>
                            $(document).ready(function() {
                                var texte = "";
                                var nbPoly = 0;
                                var nbFaces = new Array(10);
                                var nbPeaks = new Array(10);
                                for(var i=0; i<5;i++){
                                    nbPeaks[i] = new Array(10);
                                }
                                //On initialise les tableaux
                                
                                for(var i = 0; i<=4; i++){
                                    texte+="<option>"+i+"</option>"; //creation des premieres options
                                }
                                
                                $("#selectPoly").html(texte);
                                
                                $("#selectPoly").on("change", load);
                                
                                function load(){
                                    loadPoly();
                                    loadFace();
                                }
                                
                                function loadPoly(){
                                    nbPoly = $("#selectPoly").val();
                                    texte = $("#allPoly").val();
                                    if(texte==undefined){
                                        texte=" ";
                                    }
                                    if(nbPoly==0){
                                        $("#allPoly").html("");
                                    }
                                    for(var i=1; i <= nbPoly; i++){
                                        texte+= '<br><hr><br>';
                                        texte+= '<table><tr><td>';
                                                texte+= 'Faces du polyèdre '+i+' : <select id="selectFace'+i+'" name="selectFace'+i+'" class="selectFace">';
                                                for(var j=1; j<=10; j++){
                                                    texte+= '<option';
                                                    if(j==nbFaces[i]){
                                                        texte+= ' selected';
                                                    }
                                                    texte+= '>'+j+'</option>';
                                                }
                                                texte+= '</select>';
                                            texte+= '</td></tr><tr><td id="allFaces'+i+'">';
                                        texte+= '</td></tr></table>';
                                        $("#allPoly").html(texte);
                                    }
                                    $(".selectFace").on("change", loadFace);
                                }		
                                load();	//Premier chargement
                                
                                function loadFace(){
                                    for(var i=1; i<=nbPoly; i++){
                                        texte = $("#allFaces"+i).val();
                                        if(texte==undefined){
                                            texte=" ";
                                        }
                                        nbFaces[i] = $("#selectFace"+i).val();
                                        for(var j=1; j<=nbFaces[i]; j++){
                                            texte+= '<br>';
                                            texte+= '<table><tr><td>';
                                                texte+= '<table><tr><th>';
                                                        texte+= 'Face '+j;
                                                    texte+= '</th><td style="width: 60%;">';
                                                        texte+= '<input type="color" name="poly'+i+'_face'+j+'_color" value="#ffffff">';
                                                        texte+= ' | ';
                                                        texte+= '<label><input type="checkbox" name="poly'+i+'_face'+j+'_reflex" value="1">Réflexion</label>';
                                                texte+= '</td></tr></table>';
                                                texte+= '</td></tr><tr><td>';
                                                    texte+= 'Nombre de sommets : <select id="selectPeak'+i+'_'+j+'" name="selectPeak'+i+'_'+j+'" class="selectPeak">';
                                                    for(var k=3; k<=9; k++){
                                                        texte+="<option";
                                                        if(k==nbPeaks[i][j]){
                                                            texte+=" selected";
                                                        }
                                                        texte+=">"+k+"</option>";
                                                    }
                                                    texte+= '</select>';
                                                texte+= '</td></tr><tr><td id="allPeaks'+i+'_'+j+'">';
                                            texte+= '</td></tr></table>';
                                        }
                                        $("#allFaces"+i).html(texte);
                                    }
                                    $(".selectPeak").on("change", loadPeak);
                                    loadPeak();
                                }
                                
                                function loadPeak(){
                                    var j=1;
                                    var k = 1;
                                    for(var i=1; i<=nbPoly; i++){
                                        texte = $("#allPeaks"+i+'_'+j).val();
                                        if(texte==undefined){
                                            texte=" ";
                                        }
                                        nbFaces[i] = $("#selectFace"+i).val();
                                        for(j=1; j<=nbFaces[i]; j++){
                                            texte = $("#allPeaks"+i+'_'+j).val();
                                            nbPeaks[i][j] = $("#selectPeak"+i+'_'+j).val();
                                            for(var l=1; l<=nbPeaks[i][j]; l++){
                                                texte+= '<table><tr><td>';
                                                        texte+= 'Position '+l;
                                                    texte+= '</td><td>';
                                                        texte+= '<input type="number" class="xSmallNumber" name="poly'+i+'_face'+j+'_peak'+l+'_xPos" value="1"> - ';
                                                        texte+= '<input type="number" class="xSmallNumber" name="poly'+i+'_face'+j+'_peak'+l+'_yPos" value="1"> - ';
                                                        texte+= '<input type="number" class="xSmallNumber" name="poly'+i+'_face'+j+'_peak'+l+'_zPos" value="1">';
                                                texte+= '</td></tr></table>';
                                            };
                                            $("#allPeaks"+i+'_'+j).html(texte);
                                        }
                                    }
                                }
                            });
                        </script>
                    </div>
                </td>
            </tr>
        <?php $edition['content']['fillable'] = ob_get_clean();
    break;
    case 2:
        $edition['display'] = array(true, true, true);
        $edition['script'] = '';

        ob_start(); ?>
            <tr>
                <td colspan="2">
                    <img src="../link/<?= $_SESSION['file']['name'] ?>.bmp" alt="Fichier produit" width="75%">
                </td>
            </tr>
        <?php $edition['content']['fillable'] = ob_get_clean();
    break;
}

//remplissage du template
require('View/Template.php');
