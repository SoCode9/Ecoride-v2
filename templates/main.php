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

        <?php
        $isLoggedIn  = !empty($_SESSION['user_id'] ?? null);
        $roleId      = $_SESSION['role_user'] ?? null;
        ?>

        <div class="header">
            <a href="<?= ASSETS_PATH ?>/index.php">
                <img src="<?= ASSETS_PATH ?>icons/Logo.png" alt="Logo" style=" width: 100px;">
            </a>

            <!--Navigation display for big screens-->

            <nav>
                <ul class="navigation">
                    <li><a href="<?= BASE_URL ?>/" class="<?= $current === '/' ? 'active' : '' ?>">Accueil</a></li>
                    <li><a href="<?= BASE_URL ?>/covoiturages" class="<?= $current === '/covoiturages' ? 'active' : '' ?>">Covoiturages</a></li>
                    <li><a href="<?= BASE_URL ?>/contact" class="<?= $current === '/contact' ? 'active' : '' ?>">Contact</a></li>

                    <?php if (in_array((int)$roleId, [1, 2, 3], true)): ?>
                        <li><a href="<?= BASE_URL ?>/mon-profil"
                                class="btn border-white <?= ($current === '/mon-profil' || $current === '/mes-covoiturages') ? 'activeBtn' : '' ?>">
                                Mon espace
                            </a></li>
                    <?php elseif ((int)$roleId === 4): ?>
                        <li><a href="<?= BASE_URL ?>/espace-employe"
                                class="btn border-white <?= $current === '/espace-employe' ? 'activeBtn' : '' ?>">
                                Espace employé
                            </a></li>
                    <?php elseif ((int)$roleId === 5): ?>
                        <li><a href="<?= BASE_URL ?>/admin"
                                class="btn border-white <?= $current === '/admin' ? 'activeBtn' : '' ?>">
                                Espace admin
                            </a></li>
                    <?php endif; ?>

                    <?php if ($isLoggedIn): ?>
                        <li>
                            <form method="POST" action="<?= BASE_URL ?>/deconnexion" style="display:inline;">
                                <button type="submit"><img src='<?= ASSETS_PATH ?>/icons/Deconnexion.png' alt='logout' class='logout-btn'></button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li><a href="<?= BASE_URL ?>/connection"
                                class="btn <?= ($current === '/connection') || ($current === '/deconnexion') ? 'activeBtn' : '' ?>">
                                Connexion
                            </a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- Navigation mobile -->
            <div id="my-sidenav" class="sidenav" style="display:none;">
                <button id="close-btn" class="close">×</button>
                <ul>
                    <li><a href="<?= BASE_URL ?>/" class="<?= $current === '/' ? 'active' : '' ?>">Accueil</a></li>
                    <li><a href="<?= BASE_URL ?>/covoiturages" class="<?= $current === '/covoiturages' ? 'active' : '' ?>">Covoiturages</a></li>
                    <li><a href="<?= BASE_URL ?>/contact" class="<?= $current === '/contact' ? 'active' : '' ?>">Contact</a></li>

                    <?php if (in_array((int)$roleId, [1, 2, 3], true)): ?>
                        <li><a href="<?= BASE_URL ?>/mon-profil"
                                class="btn border-white <?= ($current === '/mon-profil' || $current === '/mes-covoiturages') ? 'activeBtn' : '' ?>">
                                Mon espace
                            </a></li>
                    <?php elseif ((int)$roleId === 4): ?>
                        <li><a href="<?= BASE_URL ?>/espace-employe"
                                class="btn border-white <?= $current === '/espace-employe' ? 'activeBtn' : '' ?>">
                                Espace employé
                            </a></li>
                    <?php elseif ((int)$roleId === 5): ?>
                        <li><a href="<?= BASE_URL ?>/admin"
                                class="btn border-white <?= $current === '/admin' ? 'activeBtn' : '' ?>">
                                Espace admin
                            </a></li>
                    <?php endif; ?>

                    <?php if ($isLoggedIn): ?>
                        <li>
                            <form method="POST" action="<?= BASE_URL ?>/deconnexion" style="display:inline;">
                                <button type="submit" class="btn logout-btn"><img src='<?= ASSETS_PATH ?>/icons/Deconnexion.png' alt='logout' class='logout-btn'></button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li><a href="<?= BASE_URL ?>/connection"
                                class="btn <?= ($current === '/connection') || ($current === '/deconnexion') ? 'activeBtn' : '' ?>">
                                Connexion
                            </a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="current-tab hidden" id="current-tab-mobile"></div>
            <button id="open-btn" style="display:none;">
                <span class="burger-icon"><span></span><span></span><span></span></span>
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