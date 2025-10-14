<?php if (empty($carpoolListToValidate)): ?>
    <span class="italic font-size-small"> Aucun covoiturage à valider </span>
<?php endif ?>

<div class="flex-column gap-8 grid-auto-columns">
    <?php foreach ($carpoolListToValidate as $carpool): ?>
        <div class="travel" onclick="window.location.href='<?= $carpool['detail_url'] ?>'" style="<?= $carpool['owner_style'] ?>">
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

    <div class="popup" id="validate-carpool" style="display:none;">
        <h3 class=" m-tb-12">Valider le trajet</h3>
        <div class="block-column-g20">

            <span>Est-ce que tout s'est bien passé ?</span>
            <div class="gap-4">
                <button id="yes-button" class="yes-no-btn" onclick="handleValidation(true)">Oui</button>
                <button id="no-button" class="yes-no-btn" onclick="handleValidation(false)">Non</button>
            </div>

            <!-- Step 2A : If Yes -->
            <div id="feedback-positive" style="display:none">
                <h4 class="m-tb-12">Souhaitez-vous laisser un avis ?</h4>
                <form class="block-column-g20" action="<?= BASE_URL ?>/carpool/approved" method="POST">
                    <input type="hidden" name="idReservation" id="idReservation-positive">

                    <div class="flex-row">
                        <label for="driver-rating-list">Note laissée au chauffeur : </label>
                        <select id="driver-rating-list" name="rating" class="short-field">
                            <optgroup>
                                <option value=""></option>
                                <option value="5">5</option>
                                <option value="4.5">4.5</option>
                                <option value="4">4</option>
                                <option value="3.5">3.5</option>
                                <option value="3">3</option>
                                <option value="2.5">2.5</option>
                                <option value="2">2</option>
                                <option value="1.5">1.5</option>
                                <option value="1">1</option>
                            </optgroup>
                        </select>
                        <label for="driver-rating-list"><img src="<?= ASSETS_PATH ?>/icons/EtoileJaune.png" alt="EtoileJaune"
                                class="img-width-20"></label>
                    </div>

                    <label for="comment-positive">Laissez un commentaire :</label>
                    <textarea name="comment" id="comment-positive"></textarea>

                    <div class="btn bg-light-green">
                        <button type="submit"><strong>Valider le
                                covoiturage</strong><br>(avec ou sans avis)</button>
                    </div>
                </form>
            </div>

            <!-- Step 2B : If No -->
            <div id="feedback-negative" style="display:none">
                <form class="block-column-g20" action="<?= BASE_URL ?>/carpool/rejected" method="POST">
                    <input type="hidden" name="idReservation" id="idReservation-negative">

                    <label for="comment-negative">Décrivez le problème :</label>
                    <textarea name="comment" id="comment-negative" required></textarea>
                    <div class="btn bg-light-green">
                        <button type="submit">Soumettre</button>
                    </div>
                </form>
            </div>

            <button type="button" class="col-back-grey-btn btn" style="justify-self:right;"
                onclick="closePopupValidate()">Annuler</button>
        </div>
    </div>
</div>
<script src="<?= ASSETS_PATH ?>js/carpool_to_validate.js" defer></script>