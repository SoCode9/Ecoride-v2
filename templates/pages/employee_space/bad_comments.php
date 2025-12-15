<div class="tabs" style="justify-content: space-between;">
    <button class="btn main-tab-btn" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/espace-employe/valider-avis'">Valider les avis</button>
    <button class="btn main-tab-btn active" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/espace-employe/controler'">Covoiturages mal passés</button>
</div>

<section id="bad-carpool" class="tab-content active">
    <h2 class="text-green">Contrôler les covoiturages mal passés (<?= $totalBadComments ?>)</h2>
    <?php $index = 0;
    foreach ($badComments as $badComment):
        $index++; ?>
        <div class="flex-column gap-12 block-light-grey">
            <div class="half-separation">
                <div class="flex-column flex-between gap-12">
                    <div class="flex-row flex-between flex-row-wrap">
                        <div class="flex-row gap-12">
                            <span><?= htmlspecialchars($badComment['pseudoPassenger']) ?></span>
                            <span>~</span>
                            <span class="font-size-very-small"
                                style="padding-right:15px;"><?= htmlspecialchars($badComment['mailPassenger']) ?></span>
                        </div>
                    </div>
                    <p class="text-bold" style="padding-right:15px;">
                        "<?= htmlspecialchars($badComment['bad_comment']) ?>"
                    </p>
                    <div class="flex-row">
                        <img src=" <?= ASSETS_PATH ?>/icons/Voiture.png" class="img-width-20" alt="Icone voiture">
                        <div class="flex-row flex-row-wrap">
                            <span><?= htmlspecialchars($badComment['pseudoDriver']) ?></span>
                            <span>~</span>
                            <span class="font-size-very-small"><?= htmlspecialchars($badComment['mailDriver']) ?></span>
                        </div>

                    </div>
                </div>
                <div class="flex-column gap-12">
                    <div class="flex-column flex-center">
                        <span>De <?= htmlspecialchars($badComment['departure_city']) ?> </span>
                        <span>À <?= htmlspecialchars($badComment['arrival_city']) ?></span>
                    </div>

                    <div class="flex-column flex-center">
                        <span> Date du trajet : <?= htmlspecialchars($badComment['date']) ?></span>
                        <span>Id du covoiturage :
                            <?= htmlspecialchars($badComment['id']) ?></span>
                    </div>
                    <button class="btn bg-light-green resolve-bad-comment" data-id="<?= $badComment['id'] ?>">Litige
                        résolu</button>

                </div>
            </div>
        </div>
        <?php if ($index < $totalBadComments) {
            echo '<hr>';
        }
        ?>

    <?php endforeach; ?>

    <div class="flex-row gap-12 flex-center m-8">
        <?php if ($pageBadComments > 1): ?>
            <a href="?page=<?= $pageBadComments - 1 ?>" class="btn">Page précédente</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPagesBadComments; $i++): ?>
            <a href="?page=<?= $i ?>" class="btn <?= $i === $pageBadComments ? 'bg-light-green' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($pageBadComments < $totalPagesBadComments): ?>
            <a href="?page=<?= $pageBadComments + 1 ?>" class="btn">Page suivante</a>
        <?php endif; ?>
    </div>

</section>

<script src="<?= ASSETS_PATH ?>js/employee_space.js" defer></script>