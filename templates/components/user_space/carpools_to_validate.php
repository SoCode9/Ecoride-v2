<?php foreach ($carpoolListToValidate as $carpool): ?>
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


        <?php if ($carpool['is_owner'] === false): ?>
            <div class="btn action-btn" onclick="event.stopPropagation();" style="grid-column: 5/6; grid-row: 3/5;">
                <button class="font-size-small" onclick="showPopupValidate(event)"
                    data-id="<?= $carpool['reservationId'] ?>" style="width: 100%;">Valider</button>
            </div>
        <?php endif; ?>

    </div>
<?php endforeach; ?>

<?php /* include __DIR__ . '/../popup/carpool_to_validate.php'; */ ?> <!-- @todo Ajouter la popup -->