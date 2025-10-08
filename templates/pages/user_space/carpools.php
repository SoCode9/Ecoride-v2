<div class="tabs" style="justify-content: space-between;">
    <button class="btn main-tab-btn" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/mon-profil'">Mon profil</button>
    <button class="btn main-tab-btn active" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/mes-covoiturages'">Mes
        covoiturages</button>
</div>

<div class="flex-column gap-12">
    <div class="main-header">
        <div class="tabs">
            <button class="btn tab-btn active" data-target="not-started">En cours</button>
            <button class="btn tab-btn" data-target="completed">Terminés</button>
        </div>

        <?= $carpoolButton; ?>
    </div>
    <section id="not-started" class="tab-content active">
        <div class="block-column-g20">

            <!--carpool finished but not validated-->
            <h3 style="color: black ;">Covoiturages terminés, en attente de validation</h3>
            <?php include TEMPLATE_PATH . '/components/user_space/carpools_to_validate.php'; ?>

            <!--carpool not started or in progress-->
            <h3 style="color: black ;">Covoiturages à venir</h3>

            <?php include TEMPLATE_PATH . '/components/user_space/carpools_not_started.php'; ?>

        </div>
    </section>

    <!--carpool finished and validated-->
    <section id="completed" class="tab-content">
        <?php include TEMPLATE_PATH . '/components/user_space/carpools_completed.php'; ?>

    </section>
</div>