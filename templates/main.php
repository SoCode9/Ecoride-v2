<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>css/style.css">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="">Lien 1</a></li>
                <li><a href="">Lien 2</a></li>
                <li><a href="">Lien 3</a></li>
                <li><a href="">Lien 4</a></li>
                <li><a href="">Lien 5</a></li>
                <li><a href="">Lien 6</a></li>
            </ul>

        </nav>
    </header>

    <main>
        <?php require_once $template ?>
    </main>

    <footer>
        Copyright
    </footer>
</body>

</html>