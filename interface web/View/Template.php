<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>
            <?= htmlspecialchars($template['pageName']) ?>
        </title>
        <link href="View/Ressources/global.css" rel="stylesheet">
        <link href="View/Ressources/edition.css" rel="stylesheet">
        <link href="View/Ressources/geometry.css" rel="stylesheet">
        <script src="View/Ressources/jquery-3.1.1.min.js"></script>
        <link rel="icon" type="image/png" href="View/Ressources/icone.ico"/>
    </head>
    
    <body>
        
            <div style="max-height: 300px; overflow-y: scroll; text-align: left;">
                <table>
                    <tr>
                        <td>
                            ellipses
                        </td>
                        <td>
                            Post
                        </td>
                        <td>
                            polyèdres
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre><?= print_r($_SESSION['ellipsoid']) ?></pre>
                        </td>
                        <td>
                            <pre><?php if (isset($_POST)) print_r($_POST) ?></pre>
                        </td>
                        <td>
                            <pre><?= print_r($_SESSION['polyhedron']) ?></pre>
                        </td>
                    </tr>
                </table>
            </div>
        
        <header>
            <h1>Modélisation d'images en ray tracing - étape <?= $_SESSION['pageBlock']+1 ?></h1>
        </header>

        <menu class="alert">
            <li <?= isset($template['listWarning'])? 'style="font-weight: bold"' : '' ?>>
                Notification
                <ul>
                    <li style="font-weight: initial">
                        <?php if (isset($template['listWarning'])) {
                            echo 'Champs incorrects ou manquants :';
                            foreach($template['listWarning'] as $message) {
                                echo '<br><br>- '.htmlspecialchars($message);
                            }
                        }
                        else {
                            echo 'Pas de notification';
                        } ?>
                    </li>
                </ul>
            </li>
        </menu>
            

        <section>
            <table>
                <thead>
                    <tr>
                        <th>
                            Édition d'image ou de vidéo
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $pageBlock = 0;
                    if (isset($edition['content']['fixed'])) {
                        foreach ($edition['content']['fixed'] as $content) {
                            if ($pageBlock == 0) { ?>
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
                                                    <?= htmlspecialchars($edition['legend'][$pageBlock]) ?>
                                                </th>
                                                <td>
                                                    <?= $content ?>
                                                </td>
                                            </tr>
                                            
                            <?php if ($pageBlock == 1 || ($pageBlock == 0 && !$edition['display'][2])) { ?>
                                            <tr>
                                                <td colspan="3"><br></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            <?php }
                            $pageBlock++;
                        }
                    } ?>
                    <tr>
                        <td>
                            <fieldset>
                                <legend><?= $edition['legend'][$pageBlock] ?></legend>
                                <form method="post" action="index.php">
                                    <input type="hidden" name="script" value="<?= htmlspecialchars($edition['script']) ?>">
                                    <table>
                                        <?= $edition['content']['fillable'] ?>
                                        <tr><td colspan="2"><br><hr></td></tr>
                                        <tr>
                                            <?php if ($pageBlock == 2) { ?>
                                                <td>
                                                    <input type="checkbox" id="reuseData" name="reuseData" value="1">
                                                    <label for="reuseData">Réutiliser les données</label>
                                                    <br>
                                                    <input type="checkbox" id="saveData" name="saveData" value="1">
                                                    <label for="saveData">Conserver les données</label>
                                                </td>
                                                <td>
                                                    <input type="submit" value="Nouveau fichier">
                                                </td>
                                            <?php } 
                                            else { ?>
                                                <td colspan="2">
                                                    <input type="submit" value="Suivant">
                                                </td>
                                            <?php } ?>
                                        </tr>
                                    </table>
                                </form>
                            </fieldset>
                        </td>
                    </tr>
                </tbody>
            </table>
            <a href="#" id="top"><button><h4>Haut de page</h4></button></a>
        </section>
    </body>
</html>
