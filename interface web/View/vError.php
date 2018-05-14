<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Erreur</title>
    </head>
        
    <body>
        <h1>Erreur</h1>
        <p>
            <?= htmlspecialchars($errorMessage) ?><br>
            (<?= htmlspecialchars($errorDetail) ?>)
        </p>
        <a href="index.php?action=edit">Retours à la page principale</a>
    </body>
</html>
