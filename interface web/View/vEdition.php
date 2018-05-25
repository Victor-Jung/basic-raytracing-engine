<?php
//fragments de code de la page
if ($_SESSION['pageBlock'] > 0) {
    ob_start(); ?>
        <table>
            <tr>
                <td>
                    <table>
                        <tr>
                            <td>
                                Nom du fichier : <?= htmlspecialchars($_SESSION['file']['name']) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Type de fichier : <?= (!$_SESSION['file']['video']['selected'])? 'Image' : 'Vidéo' ?>
                                <?php if ($_SESSION['file']['video']['selected']) { ?>
                                    <br>Nombres d'images : <?= htmlspecialchars($_SESSION['file']['video']['frames']) ?>
                                    <br>Mouvement en x : <?= htmlspecialchars($_SESSION['file']['video']['move']['x']) ?>
                                    <br>Mouvement en y : <?= htmlspecialchars($_SESSION['file']['video']['move']['y']) ?>
                                    <br>Mouvement en z : <?= htmlspecialchars($_SESSION['file']['video']['move']['z']) ?>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Dimensions du fichier : <?= htmlspecialchars($_SESSION['file']['dim']['x']) ?>x<?= htmlspecialchars($_SESSION['file']['dim']['y']) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= $_SESSION['file']['effects']['shadows']? 'Ombres' : 'Pas d\'ombres' ?>
                                <br>
                                <?= $_SESSION['file']['effects']['aliasing']? 'Anti-aliasing' : 'Pas d\'anti-aliasing' ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table>
                        <tr>
                            <td>
                                Position caméra x : <?= htmlspecialchars($_SESSION['scene']['viewer']['x']) ?>
                                <br>
                                Position caméra y : <?= htmlspecialchars($_SESSION['scene']['viewer']['y']) ?>
                                <br>
                                Position caméra z : <?= htmlspecialchars($_SESSION['scene']['viewer']['z']) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Position x de la lumière : <?= htmlspecialchars($_SESSION['scene']['light'][0]['pos']['x']) ?>
                                <br>
                                Position y de la lumière : <?= htmlspecialchars($_SESSION['scene']['light'][0]['pos']['y']) ?>
                                <br>
                                Position z de la lumière : <?= htmlspecialchars($_SESSION['scene']['light'][0]['pos']['z']) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Puissance de la lumière : <?= htmlspecialchars($_SESSION['scene']['light'][0]['bright']) ?>%
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Couleur de fond : <input type="color" value="<?= htmlspecialchars($_SESSION['scene']['color']) ?>" disabled>
                            </td>
                        </tr>
                    </table>
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
                    <?php if ($_POST['selectElli'] != 0) {
                        $i = 0;
                        foreach ($_SESSION['ellipsoid'] as $elli) {
                            if ($i != 0) {
                                echo '<br><hr><br>';
                            }
                            $i++; ?>
                            <table>
                                <tr>
                                    <td>
                                        Couleur : <input type="color" value="<?= htmlspecialchars($elli['color']) ?>" disabled>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Position x du centre : <?= $elli['pos']['x'] ?>
                                        <br>
                                        Position y du centre : <?= $elli['pos']['y'] ?>
                                        <br>
                                        Position z du centre : <?= $elli['pos']['z'] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Rayon x : <?= $elli['rad']['x'] ?>
                                        <br>
                                        Rayon y : <?= $elli['rad']['y'] ?>
                                        <br>
                                        Rayon z : <?= $elli['rad']['z'] ?>
                                    </td>
                                </tr>
                            </table>
                        <?php }
                    } ?>
                </td>
                <td>
                    <?php $i = 0;
                    foreach ($_SESSION['polyhedron'] as $poly) {
                        if ($i != 0) {
                            echo '<br><hr><br>';
                        }
                        $i++;
                        $j = 0;
                        foreach ($poly as $face) { 
                            $j++; ?>
                            <table>
                                <tr>
                                    <td>
                                        Face <?= $j ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Couleur : <input type="color" value="<?= htmlspecialchars($face['color']) ?>" disabled><br>
                                        Réflexion : <?= ($face['reflex'])? 'Oui' : 'Non' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table>
                                            <?php  $k = 0;
                                            foreach ($face['peak'] as $peak) {
                                                $k++; ?>
                                                <tr>
                                                    <td>
                                                        Sommet <?= $k ?><br>
                                                        Position x : <?= $peak['x'] ?>
                                                        <br>
                                                        Position y : <?= $peak['y'] ?>
                                                        <br>
                                                        Position z : <?= $peak['z'] ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        <?php }
                    } ?>
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
                                            Nombre d'images :
                                        </td>
                                        <td>
                                            <input type="number" class="number" name="frames" 
                                            value="<?= htmlspecialchars($_SESSION['file']['video']['frames']) ?>" 
                                            step="0.1" min="2" required>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Déplacement caméra :
                                        </td>
                                        <td>
                                            <input type="number" class="xSmallNumber" name="moveX" 
                                            value="<?= htmlspecialchars($_SESSION['file']['video']['move']['x']) ?>" 
                                            step="0.1" required>
                                            -
                                            <input type="number" class="xSmallNumber" name="moveY" 
                                            value="<?= htmlspecialchars($_SESSION['file']['video']['move']['y']) ?>" 
                                            step="0.1" required>
                                            -
                                            <input type="number" class="xSmallNumber" name="moveZ" 
                                            value="<?= htmlspecialchars($_SESSION['file']['video']['move']['z']) ?>" 
                                            step="0.1" required>
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
                                step="0.1" required>
                                -
                                <input type="number" class="smallNumber" name="viewerY" 
                                value="<?= htmlspecialchars($_SESSION['scene']['viewer']['y']) ?>" 
                                step="0.1" required>
                                -
                                <input type="number" class="smallNumber" name="viewerZ" 
                                value="<?= htmlspecialchars($_SESSION['scene']['viewer']['z']) ?>" 
                                step="0.1" required>
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
                                step="0.1" required>
                                -
                                <input type="number" class="smallNumber" name="lightY" 
                                value="<?= htmlspecialchars($_SESSION['scene']['light'][0]['pos']['y']) ?>" 
                                step="0.1" required>
                                -
                                <input type="number" class="smallNumber" name="lightZ" 
                                value="<?= htmlspecialchars($_SESSION['scene']['light'][0]['pos']['z']) ?>" 
                                step="0.1" required>
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
                                                    texte+= '<input type="number" class="smallNumber" name="elli'+i+'_xPos" value="'+<?= $_SESSION['ellipsoid'][$idElli]['pos']['x'] ?>+'" step="0.1"> - ';
                                                    texte+= '<input type="number" class="smallNumber" name="elli'+i+'_yPos" value="'+<?= $_SESSION['ellipsoid'][$idElli]['pos']['y'] ?>+'" step="0.1"> - ';
                                                    texte+= '<input type="number" class="smallNumber" name="elli'+i+'_zPos" value="'+<?= $_SESSION['ellipsoid'][$idElli]['pos']['z'] ?>+'" step="0.1">';
                                            texte+= '</td></tr><tr><td>';
                                                    texte+= 'Rayons : ';
                                                texte+= '</td><td>';
                                                    texte+= '<input type="number" class="smallNumber" name="elli'+i+'_xRad" value="'+<?= $_SESSION['ellipsoid'][$idElli]['rad']['x'] ?>+'" step="0.1"> - ';
                                                    texte+= '<input type="number" class="smallNumber" name="elli'+i+'_yRad" value="'+<?= $_SESSION['ellipsoid'][$idElli]['rad']['y'] ?>+'" step="0.1"> - ';
                                                    texte+= '<input type="number" class="smallNumber" name="elli'+i+'_zRad" value="'+<?= $_SESSION['ellipsoid'][$idElli]['rad']['z'] ?>+'" step="0.1">';
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
                                                        texte+= '<input type="number" class="xSmallNumber" name="poly'+i+'_face'+j+'_peak'+l+'_xPos" value="1" step="0.1"> - ';
                                                        texte+= '<input type="number" class="xSmallNumber" name="poly'+i+'_face'+j+'_peak'+l+'_yPos" value="1" step="0.1"> - ';
                                                        texte+= '<input type="number" class="xSmallNumber" name="poly'+i+'_face'+j+'_peak'+l+'_zPos" value="1" step="0.1">';
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
                    Fichier généré : affichage sur la fenêtre pop-up.
                </td>
            </tr>
        <?php $edition['content']['fillable'] = ob_get_clean();
    break;
}

//remplissage du template
require('View/Template.php');
