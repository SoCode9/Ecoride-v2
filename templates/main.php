<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="<?php echo ASSETS_PATH ?>css/style.css">
    <script src="<?= ASSETS_PATH ?>js/burger_menu.js" defer></script>
</head>

<body>
    <header>
        <div class="header">
            <a href="<?= ASSETS_PATH ?>/index.php">
                <img src="<?= ASSETS_PATH ?>icons/Logo.png" alt="Logo" style=" width: 100px;">
            </a>

            <!--Navigation display for big screens-->
            <nav >
                <ul class="navigation">
                    <li><a href="<?= BASE_URL ?>/" id='home-page'>Accueil</a></li>
                    <li><a href="" id='carpool-button'>Covoiturages</a></li>
                    <li><a href="" id='contact-button'>Contact</a></li>
                    <li><a href="" id='user-space' class='btn border-white'>Espace Utilisateur</a></li>
                    <li><a href="" id='login-button'>Connexion</a></li>
                </ul>
            </nav>

            <!--Navigation display for small screens-->
            <div id="my-sidenav" class="sidenav" style="display: none;">
                <a id="close-btn" href="#" class="close">×</a>
                <ul>
                    <?php /* @TODO  renderNavigationLinks(true); */ ?>

                    <li><a href="<?= BASE_URL ?>/" id='home-page'>Accueil</a></li>
                    <li><a href="" id='carpool-button'>Covoiturages</a></li>
                    <li><a href="" id='contact-button'>Contact</a></li>
                    <li><a href="" id='user-space' class='btn border-white'>Espace Utilisateur</a></li>
                    <li><a href="" id='login-button'>Connexion</a></li>
                </ul>
            </div>

            <div class="current-tab hidden" id="current-tab-mobile"></div>

            <a href="#" id="open-btn" style="display: none;">
                <span class="burger-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </a>
        </div>
    </header>

    <main>
        <?php require_once $template ?>
    </main>

    <footer>
        Copyright
    </footer>
</body>
</html>