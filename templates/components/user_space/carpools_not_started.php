<?php if (empty($carpoolListNotStarted)): ?>
    <span class="italic font-size-small"> Aucun covoiturage à venir </span>
<?php endif ?>

<div class="flex-column gap-8 grid-auto-columns">
    <?php foreach ($carpoolListNotStarted as $carpool): ?>
        <div class="travel" onclick="window.location.href='<?= $carpool['detail_url'] ?>'"
            <?php if ($carpool['is_owner']) {
                echo "style='border:2px solid var(--col-green);cursor:pointer;'";
            } else {
                echo "style ='cursor:pointer;'";
            } ?>>
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

            <?php if ($carpool['status'] === 'not started'): ?>
                <div class="btn action-btn" onclick="event.stopPropagation();"
                    style="background-color:var(--col-light-grey); grid-column: 5/6; grid-row: 3/5;">
                    <a href="../back/user/user_space.php?action=cancel_carpool&id=<?= $carpool['id'] ?>"
                        class="font-size-small">Annuler</a>
                </div>
            <?php endif; ?>

            <?php
            $now = new DateTime(); 
            $departureDateTime = DateTime::createFromFormat("Y-m-d H:i:s", $carpool['date'] . ' ' . $carpool['departure_time']);

            if (($carpool['status'] === 'not started') && $carpool['is_owner'] && $departureDateTime !== false && $departureDateTime <= $now): ?>
                <div class="btn action-btn" onclick="event.stopPropagation();"
                    style=" background-color:var(--col-light-green); grid-column: 5/6; grid-row: 3/5;">
                    <a href="../back/user/user_space.php?action=start_carpool&id=<?= $carpool['id'] ?>"
                        class="font-size-small">Démarrer</a>
                </div>
            <?php endif; ?>

            <?php if (($carpool['status'] === 'in progress') && $carpool['is_owner']): ?>
                <div class="btn action-btn" onclick="event.stopPropagation();"
                    style=" background-color:var(--col-light-green); grid-column: 5/6; grid-row: 3/5;">
                    <a href="../back/user/user_space.php?action=complete_carpool&id=<?= $carpool['id'] ?>"
                        class="font-size-small">Terminer</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>