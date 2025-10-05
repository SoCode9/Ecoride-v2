<!--Travel's details and booking block-->

<h2 class="text-green text-bold">
    <?= htmlspecialchars($dateLong)  ?>
</h2>

<section class="flex-row flex-between block-light-grey flex-column-ss no-background-ss">
    <div class="flex-row flex-between block-white" id="travel-details" style="width:65%;box-sizing: border-box;">

        <div class="course">

            <div class="time-location-ellipse">
                <div class="flex-column gap-8 time-location">
                    <span><?= htmlspecialchars($carpoolFormatted['departure_time'])/* formatTime(htmlspecialchars($travel->getDepartureTime())) */ ?></span>
                    <span><?= htmlspecialchars($carpool->getDepartureCity()) ?></span>
                </div>

                <div id="dot"></div>
            </div>

            <div class="line-container">
                <div class="line"></div>
                <div class="duration text-green">
                    <?= htmlspecialchars($carpoolFormatted['duration']) ?>
                </div>
                <div class="line"></div>
            </div>

            <div class="time-location-ellipse">
                <div id="dot"></div>
                <div class="flex-column gap-8 time-location">
                    <span><?= htmlspecialchars($carpoolFormatted['arrival_time']) ?></span>
                    <span><?= htmlspecialchars($carpool->getArrivalCity()) ?></span>
                </div>

            </div>

        </div>

        <div class="flex-column gap-8" id="travel-extra" style="align-items: end; min-width:max-content;">
            <?php if (!empty($carpoolFormatted['seats_label'])): ?>
                <div>
                    <span class="seats-available" id="seats-bs">Encore <?= htmlspecialchars($carpoolFormatted['seats_label']) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($carpoolFormatted['eco_label'])): ?>
                <div class="criteria-eco-div">
                    <img src="<?= ASSETS_PATH ?>icons/Arbre1.png" alt="Arbre" width="20px">
                    <span class="criteria-eco"><?= htmlspecialchars($carpoolFormatted['eco_label']) ?></span>
                </div>
            <?php endif; ?>
        </div>

    </div>
    <div class="flex-column gap-12" id="passenger-credits-btn" style="width: 30%;">
        <div class="flex-row flex-between block-white">
            <div>1 passager</div>
            <div class="text-bold">
                <?= htmlspecialchars($carpoolFormatted['price_label']); ?>
            </div>
        </div>
        <?php echo $carpoolFormatted['participate_btn']; ?>


        <?php echo $carpoolFormatted['cancel_btn']; ?>
    </div>

</section>

<div style="display: flex; justify-content: space-between;" class="gap-24 flex-column-ms flex-column-ss">

    <!--Driver's details-->

    <section class="block-driver-info block-light-grey flex-column-ss">
        <div class="flex-column gap-24 flex-row-ss">
            <img src="<?= htmlspecialchars($carpoolFormatted['driver_photo']) ?>" class="photo-100" alt="photo de l'utilisateur">
            <div class="flex-column gap-12 flex-center item-center">
                <span><?= htmlspecialchars($driver->getPseudo()) ?></span>
                <div class="text-icon" style="padding-left: 0px;">
                    <?= $carpoolFormatted['driver_rating'] ?>
                </div>
            </div>
        </div>

        <div class="flex-column gap-24">


            <?= htmlspecialchars($carpool->getDescription()) ?>

            <!-- car-->

            <div class="flex-column gap-12">
                <div class="text-bold">Véhicule</div>
                <div class="text-icon">
                    <img src="<?= ASSETS_PATH ?>icons/Voiture.png" class="img-width-20" alt="">
                    <span>
                        <?= htmlspecialchars($car->getBrand() . " " . $car->getModel() . " - " . $car->getColor() . $isElectric); ?>
                    </span>
                </div>
            </div>
            <!-- preferences-->

            <div class="flex-column gap-12">
                <div class="text-bold">Préférences</div>
                <?php foreach ($preferencesData as $preference): ?>
                    <div class="text-icon">
                        <img src='<?= ASSETS_PATH ?>/icons/<?= $preference['image'] ?>' class='img-width-20' alt=''>
                        <span><?= $preference['text'] ?></span>
                    </div>
                <?php endforeach; ?>


                <!--Others preferences-->
                <?php
                /* $customPreferences = $driver->loadCustomPreferences();
                foreach ($customPreferences as $pref): ?>
                    <div class="text-icon">
                        <img src='<?= BASE_URL ?>/icons/addPref.png' class='img-width-20' alt=''>
                        <span><?= htmlspecialchars($pref['custom_preference']) ?></span>
                    </div>
                <?php endforeach;*/ ?>

            </div>

    </section>


    <!--RATING'S DRIVER BLOCK-->


    <section class="flex-column block-light-grey gap-12 block-driver-ratings w-100-ss">

        <div class="flex-column gap-8 item-center">
            <h3 class="text-green">Avis du chauffeur</h3>
            <div class="flex-row item-center gap-4" style="padding-left: 0px;">
                <img src="<?= BASE_URL ?>/icons/EtoileJaune.png" class="img-width-20" alt="">
                <span class="flex-row">
                    <?= $carpoolFormatted['driver_rating'];
                    if ($carpoolFormatted['driver_rating'] !== null) {
                        echo  " / 5";
                    }
                    ?>
                </span>
                <span class="font-size-very-small"><?php /* "(" . htmlspecialchars($driver->countRatings()) . " avis)" */ ?></span>
            </div>
        </div>


        <!--ratings list-->
        <?php foreach ($ratings as $rating): ?>
            <?php $userId = $rating->getUserId();
            $rf = $ratingsFormatted[$userId];
            ?>

            <div class="flex-column gap-8">
                <div class="flex-row flex-between">
                    <div class="flex-row item-center gap-4">
                        <img src="<?= htmlspecialchars($rf['userPhoto']) ?>" alt="Photo de l'utilisateur" class="photo-50">
                        <span><?= htmlspecialchars($rf['userPseudo']) ?></span>
                    </div>
                    <div class="flex-row item-center gap-4" style="padding-left: 0px;">
                        <img src="<?= BASE_URL ?>/icons/EtoileJaune.png" class="img-width-20" alt="">
                        <span class="flex-row"><?= $rf['rating'] ?></span>
                    </div>
                </div>
                <p><?= htmlspecialchars(($rating->getDescription())) ?></p>
                <span class="font-size-very-small italic"><?= htmlspecialchars($rf['createdAt']) ?></span>
                <hr>
            </div>
        <?php endforeach ?>

    </section>



</div>