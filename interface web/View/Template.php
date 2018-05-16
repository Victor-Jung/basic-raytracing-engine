<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title><?= htmlspecialchars($template['pageName']) ?></title>
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
                    <pre>< ?php if (isset($_SESSION['edit']['dataScene']['shape'])) print_r($_SESSION['edit']['dataScene']['shape']) ?></pre>
                </td>
            </tr>
        </table>
    </div>
-->
        <header>
            <h1>Modélisation d'images en ray tracing - étape <?= $_SESSION['edit']['step'] ?></h1>
        </header>

        <menu>
            <span>
                <?php if ($template['actual'] != 'edit') { ?>
                    <li>
                        <a href="index.php?action=edit">Créer une image</a>
                    </li>
                <?php } ?>
                <li>
                    Ajouter des options
                    <ul>
                        <?php if ($template['script'] != 'formula') { ?>
                            <li>
                                <a href="index.php?action=add">Formes géométriques</a>
                            </li>
                        <?php }
                        if ($template['script'] != 'texture') { ?>
                            <li>
                                <a href="index.php?action=add">Textures</a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>
            </span>
            <span class="alert">
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
            </span>
        </menu>
            

        <section>
            <?= $template['content'] ?>
            <a href="#" id="top"><button><h4>Haut de page</h4></button></a>
        </section>
    </body>
</html>

<?php exit(); //provisoire : quitte le script