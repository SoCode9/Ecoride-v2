<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titlePage ?? "EcoRide") ?></title>

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
            <nav>
                <ul class="navigation">
                    <li><a href="<?= BASE_URL ?>/" id='home-page' class="<?= $current === '/' ? 'active' : '' ?>">Accueil</a></li>
                    <li><a href="<?= BASE_URL ?>/covoiturages" id='carpool-button' class="<?= $current === '/covoiturages' ? 'active' : '' ?>">Covoiturages</a></li>
                    <li><a href="" id='contact-button' class=" <?= $current === '/contact' ? 'active' : '' ?>">Contact</a></li>
                    <li><a href="<?= BASE_URL ?>/mon-profil" id='user-space' class="btn border-white <?= ($current === '/mon-profil' || $current === '/mes-covoiturages') ? 'activeBtn' : '' ?>">Espace Utilisateur</a></li>
                    <li><a href="" id='login-button' class="<?= $current === '/espace-utilisateur' ? 'activeBtn' : '' ?>">Connexion</a></li>
                </ul>
            </nav>

            <!--Navigation display for small screens-->
            <div id="my-sidenav" class="sidenav" style="display: none;">
                <button id="close-btn" href="#" class="close">×</button>
                <ul>
                    <li><a href="<?= BASE_URL ?>/" id='home-page' class="<?= $current === '/' ? 'active' : '' ?>">Accueil</a></li>
                    <li><a href="<?= BASE_URL ?>/covoiturages" id='carpool-button' class="<?= $current === '/covoiturages' ? 'active' : '' ?>">Covoiturages</a></li>
                    <li><a href="" id='contact-button' class="<?= $current === '/contact' ? 'active' : '' ?>">Contact</a></li>
                    <li><a href="<?= BASE_URL ?>/mon-profil" id='user-space' class="btn border-white <?= $current === '/mon-profil' ? 'activeBtn' : '' ?>">Espace Utilisateur</a></li>
                    <li><a href="" id='login-button' class="<?= $current === '/espace-utilisateur' ? 'activeBtn' : '' ?>">Connexion</a></li>
                </ul>
            </div>
            <div class="current-tab hidden" id="current-tab-mobile"></div>

            <button href="#" id="open-btn" style="display: none;">
                <span class="burger-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </button>
        </div>
    </header>

    <!--display of error or success messages-->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="message">
            <?php
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']); // Deletes after display
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="message" style="background-color: var(--col-light-red); color: var(--col-dark-red);">
            <?php
            echo $_SESSION['error_message'];
            unset($_SESSION['error_message']); // Deletes after display
            ?>
        </div>
    <?php endif; ?>


    <main>
        <?php require_once $template ?>
    </main>

    <footer>
        <div class="header font-size-small" style="color:white;">
            <div class="flex-row">
                <span>@2025 EcoRide</span>
            </div>
            <div>
                <a href="mailto:info@ecoride.fr">info@ecoride.fr</a>
            </div>
            <div>
                <a href="<?= BASE_URL ?>/mentions-legales" class="nav-btn"> Mentions légales</a>
            </div>
        </div>
    </footer>
</body>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const message = document.querySelector(".message");
        if (message) {
            setTimeout(() => {
                message.style.opacity = "0"; 
                setTimeout(() => {
                    message.style.display = "none"; 
                }, 500);
            }, 4000); 
        }
    });
</script>

</html>