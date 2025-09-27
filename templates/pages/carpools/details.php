<!--Travel's details and booking block-->

<h2 class="text-green text-bold">
    <?/* echo htmlspecialchars($dateLong)  */ ?>
</h2>

<section class="flex-row flex-between block-light-grey flex-column-ss no-background-ss">
    <div class="flex-row flex-between block-white" id="travel-details" style="width:65%;box-sizing: border-box;">

        <div class="course">

            <div class="time-location-ellipse">
                <div class="flex-column gap-8 time-location">
                    <span><?= htmlspecialchars($carpool['departure_time'])/* formatTime(htmlspecialchars($travel->getDepartureTime())) */ ?></span>
                    <span><?= htmlspecialchars($carpool['departure_city']) ?></span>
                </div>

                <div id="dot"></div>
            </div>

            <div class="line-container">
                <div class="line"></div>
                <div class="duration text-green">
                    <?= htmlspecialchars($carpool['duration']) ?>
                </div>
                <div class="line"></div>
            </div>

            <div class="time-location-ellipse">
                <div id="dot"></div>
                <div class="flex-column gap-8 time-location">
                    <span><?= htmlspecialchars($carpool['arrival_time']) ?></span>
                    <span><?= htmlspecialchars($carpool['arrival_city']) ?></span>
                </div>

            </div>

        </div>

        <div class="flex-column gap-8" id="travel-extra" style="align-items: end; min-width:max-content;">
            <?php if (!empty($carpool['seats_label'])): ?>
                <div>
                    <span class="seats-available" id="seats-bs">Encore <?= htmlspecialchars($carpool['seats_label']) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($carpool['eco_label'])): ?>
                <div class="criteria-eco-div">
                    <img src="<?= ASSETS_PATH ?>icons/Arbre1.png" alt="Arbre" width="20px">
                    <span class="criteria-eco"><?= htmlspecialchars($carpool['eco_label']) ?></span>
                </div>
            <?php endif; ?>
        </div>

    </div>
    <div class="flex-column gap-12" id="passenger-credits-btn" style="width: 30%;">
        <div class="flex-row flex-between block-white">
            <div>1 passager</div>
            <div class="text-bold">
                <?php htmlspecialchars($carpool['price_label']); ?>
            </div>
        </div>
        <?php echo $carpool['participate_btn']; ?>


        <?php echo $carpool['cancel_btn'];/*  if ($carpool->getDriverId() === $userId && $status === 'not started'): ?>
            <div class="btn action-btn" style="padding: 8px;">
                <a href="../back/user/user_space.php?action=cancel_carpool&id=<?= $carpool['id'] ?>">Annuler le
                    covoiturage</a>
            </div>
        <?php endif; */ ?>
    </div>

</section>