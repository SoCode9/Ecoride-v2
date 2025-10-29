<?php if (empty($carpoolListCompleted)): ?>
    <span class="italic font-size-small"> Aucun covoiturage terminé </span>
<?php endif ?>

<div class="flex-column gap-8 grid-auto-columns">
    <?php foreach ($carpoolListCompleted as $carpool): ?>
        <div class="travel flex-column-ms" onclick="window.location.href='<?= $carpool['detail_url'] ?>'" style="<?= $carpool['owner_style'] ?>">
            <div class="user-header-mobile">
                <div class="photo-user-container " style="justify-self:center;">
                    <img src="<?= htmlspecialchars($carpool['driver_photo']) ?>" alt="Photo de l'utilisateur" class="photo-user">
                </div>
                <div class="user-info-mobile">
                    <span class="pseudo-user"><?= htmlspecialchars($carpool['driver_pseudo']) ?></span>
                    <div class="driver-rating">
                        <div class="flex-row font-size-very-small">
                            <?= $carpool['driver_rating']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <span class="date-travel text-bold"><?= htmlspecialchars($carpool['date']) ?></span>
            <span class="hours-travel">De <?= htmlspecialchars($carpool['departure_city']) ?></span>
            <span class="criteria-eco-div">À <?= htmlspecialchars($carpool['arrival_city']) ?></span>
            <span class="seats-available">De
                <?= htmlspecialchars($carpool['departure_time']) ?> à
                <?= htmlspecialchars($carpool['arrival_time']) ?></span>
            <span class="travel-price text-bold"><?= $carpool['price_label'] ?></span>
        </div>
    <?php endforeach; ?>
</div>