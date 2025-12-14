<div class="tabs" style="justify-content: space-between;">
    <button class="btn main-tab-btn active" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/espace-employe/valider-avis'">Valider les avis</button>
    <button class="btn main-tab-btn" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/espace-employe/controler'">Covoiturages mal passés</button>
</div>

<div class="flex-row gap-12 m-tb-12 m-8">
    <span><?= htmlspecialchars($user->getPseudo()) ?></span>
    <span><?= htmlspecialchars($user->getMail()) ?></span>
</div>

<section id="validate-rating" class="tab-content active">
    <h2 class="text-green">Valider les avis des participants (<?= $totalRatings ?>)</h2>
    <?php

    if (isset($ratingsInValidation)):
        $totalRatings = count($ratingsInValidation);
        $index = 0;
        foreach ($ratingsInValidation as $rating):
            //$driver = new Driver($pdo, $rating['driver_id']);
            $index++;
            ?>
            <div class="flex-column gap-8 block-light-grey">
                <div class="flex-row flex-between ">
                    <span><?= htmlspecialchars($rating['passenger_pseudo']) ?></span>
                    <div class="flex-row gap-8">
                        <a class="btn bg-light-green"
                            href="../back/user/employee_space.php?action=validate_rating&id=<?= $rating['id'] ?>">Valider</a>
                        <!-- @todo -->
                        <a class="btn bg-light-red"
                            href="../back/user/employee_space.php?action=reject_rating&id=<?= $rating['id'] ?>">Refuser</a>
                        <!-- @todo -->
                    </div>
                </div>
                <div class="flex-row flex-between">
                    <span class="text-bold text-breakable" style="width:100%">
                        <?php if (isset($rating['description'])) {
                            echo '"' . htmlspecialchars($rating['description']) . '"';
                        } else {
                            echo '<span class="font-size-very-small italic" style="font-weight:normal">(pas de commentaire)</span>';
                        }
                        ?></span>
                    <div class="flex-row m-8">
                        <img src="<?= ASSETS_PATH ?>/icons/EtoileJaune.png" class="img-width-20" alt="Icone étoile">
                        <span class="text-bold"><?= htmlspecialchars($rating['rating']) ?></span>
                    </div>
                </div>
                <div class="flex-row flex-between">
                    <div class="flex-row gap-4">
                        <img src="<?= ASSETS_PATH ?>/icons/Voiture.png" class="img-width-20" alt="Icone voiture">
                        <span><?= htmlspecialchars($rating['driver_pseudo']) ?></span>
                    </div>
                    <span class="italic font-size-very-small"><?= $rating['created_at'] ?></span>
                </div>
            </div>
            <?php if ($index !== $totalRatings):
                echo '<hr>' ?>
            <?php endif; ?>
        <?php endforeach; ?>

        <div class="flex-row gap-12 flex-center m-8">
            <?php
            if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="btn">Page précédente</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?= $i ?>" class="btn <?= $i === $page ? 'bg-light-green' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>" class="btn">Page suivante</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<script src="<?= ASSETS_PATH ?>js/employee_space.js" defer></script>