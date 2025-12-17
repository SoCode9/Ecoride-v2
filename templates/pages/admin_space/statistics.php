<nav class="tabs" style="justify-content: space-between;">
    <button class="btn main-tab-btn" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/espace-admin/employes'">Comptes employés</button>
    <button class="btn main-tab-btn" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/espace-admin/utilisateurs'">Comptes utilisateurs</button>
    <button class="btn main-tab-btn active" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/espace-admin/statistiques'">Statistiques</button>
</nav>

<section>
    <div class="flex-row flex-between m-tb-20">
        <div class="block-light-grey flex-column gap-12 flex-center text-bold item-center" style="width:fit-content;">
            <span class="text-green">Crédits gagnés par la plateforme</span>
            <span class="font-size-very-big ">
                <?= htmlspecialchars($creditsEarned); ?>
            </span>
        </div>
        <div class="block-light-grey flex-column gap-12 flex-center text-bold item-center" style="width:fit-content;">
            <span class="text-green">Nombre d'utilisateurs</span>
            <span class="font-size-very-big ">
                <?= htmlspecialchars($nbUsers); ?>
            </span>
        </div>
    </div>

    <!--CHART nb carpools in the next 10 days-->
    <div class="block-light-grey flex-column gap-12 flex-center text-bold m-tb-20">
        <span class="text-green">Evolution des covoiturages</span>
        <canvas id="carpools-per-day-chart" width="2500" height="2000"></canvas>
    </div>

    <!--CHART credits earned by the platform over the last 10 days-->
    <div class="block-light-grey flex-column gap-12 flex-center text-bold m-tb-20">
        <span class="text-green">Evolution des crédits gagnés par la plateforme</span>
        <canvas id="credits-earned-by-platform" width="2500" height="2000"></canvas>
    </div>

</section>

<script src="<?= ASSETS_PATH ?>js/charts.js" defer></script>