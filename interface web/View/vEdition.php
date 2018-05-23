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
            <tr>
                <td colspan="2">
                    <br><hr><br>
                </td>
            </tr>
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
                    <div id="allEllis">
                        <script>
                            $(document).ready(function() {
                                var texte = "";
                                var nbElli = 0;
                                //On initialise les tableaux
                                
                                for(var i = 0; i<=10; i++){
                                    texte+="<option>"+i+"</option>"; //creation des premieres options
                                }
                                
                                $("#selectElli").html(texte);
                                
                                $("#selectElli").on("change", load);
                                
                                function load(){
                                    loadPoly();
                                    loadFace();
                                }
                                
                                function loadPoly(){
                                    nbElli = $("#selectElli").val();
                                    texte = $("#allEllis").val();
                                    if(texte==undefined){
                                        texte=" ";
                                    }
                                    if(nbElli==0){
                                        $("#allEllis").html("");
                                    }
                                    for(var i=1; i <= nbElli; i++){
                                        texte += "<div id='elli"+i+"'><p>----Elli"+i+"</p>";
                                        texte+="</div>";
                                        $("#allEllis").html(texte);
                                    }
                                }		
                                load();
                            });
                        </script>
                    </div>
                </td>
                <td>
                    <div id="allPolys">
                        <script>
                            $(document).ready(function() {
                                var texte = "";
                                var nbPoly = 0;
                                var nbFaces = new Array(10);
                                var nbPeaks = new Array(10);
                                for(var i=0; i<10;i++){
                                    nbPeaks[i] = new Array(10);
                                }
                                //On initialise les tableaux
                                
                                for(var i = 0; i<=10; i++){
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
                                    texte = $("#allPolys").val();
                                    if(texte==undefined){
                                        texte=" ";
                                    }
                                    if(nbPoly==0){
                                        $("#allPolys").html("");
                                    }
                                    for(var i=1; i <= nbPoly; i++){
                                        texte += "<div id='poly"+i+"'><p>----Poly"+i+" <select id='selectFace"+i+"' name='selectFace"+i+"' class='selectFace'></p>";
                                        for(var j=1; j<=10; j++){
                                            texte+="<option";
                                            if(j==nbFaces[i]){
                                                texte+=" selected";
                                            }
                                            texte+=">"+j+"</option>";
                                        }
                                        texte+="</select><div id='allFaces"+i+"'></div>";
                                        texte+="</div>";
                                        $("#allPolys").html(texte);
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
                                            texte += "<div id='face"+j+"'><p>--------Face"+j+" <select id='selectPeak"+i+j+"' name='selectPeak"+i+j+"' class='selectPeak'></p>";
                                            for(var k=1; k<=10; k++){
                                                texte+="<option";
                                                if(k==nbPeaks[i][j]){
                                                    texte+=" selected";
                                                }
                                                texte+=">"+k+"</option>";
                                            }
                                            texte+="</select><div id='allPeaks"+i+j+"'>";
                                            texte+="</div></div>";
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
                                        texte = $("#allPeaks"+i+j).val();
                                        if(texte==undefined){
                                            texte=" ";
                                        }
                                        nbFaces[i] = $("#selectFace"+i).val();
                                        for(j=1; j<=nbFaces[i]; j++){
                                            texte = $("#allPeaks"+i+j).val();
                                            nbPeaks[i][j] = $("#selectPeak"+i+j).val();
                                            for(var l=1; l<=nbPeaks[i][j]; l++){
                                                texte+= "<p>------------Sommet"+l+"</p>";
                                            };
                                            $("#allPeaks"+i+j).html(texte);
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
