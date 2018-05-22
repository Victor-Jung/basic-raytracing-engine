<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>
            <?= htmlspecialchars($template['pageName']) ?>
        </title>
        <link href="View/Style/global.css" rel="stylesheet">
        <link href="View/Style/edition.css" rel="stylesheet">
        <link href="View/Style/geometry.css" rel="stylesheet">
    </head>
    
    <body>
        <!--
            <div style="max-height: 300px; overflow-y: scroll">
                <table>
                    <tr>
                        <td>
                            Session
                        </td>
                        <td>
                            Post
                        </td>
                        <td>
                            Formes
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <pre>< ?= print_r($_SESSION) ?></pre>
                        </td>
                        <td>
                            <pre>< ?php if (isset($_POST)) print_r($_POST) ?></pre>
                        </td>
                        <td>
                            <pre>< ?php if (isset($_SESSION['shape'])) print_r($_SESSION['shape']) ?></pre>
                        </td>
                    </tr>
                </table>
            </div>
        -->
        <header>
            <h1>Modélisation d'images en ray tracing - étape <?= $_SESSION['pageBlock'] ?></h1>
        </header>

        <menu class="alert">
            <li <?php if (isset($template['listWarning'])) echo 'style="font-weight: bold"' ?>>
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
                                <form method="post" action="index.php">
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
            <a href="#" id="top"><button><h4>Haut de page</h4></button></a>
        </section>
    </body>
</html>
