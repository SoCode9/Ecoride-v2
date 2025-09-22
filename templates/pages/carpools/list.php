<!-- <table>
    <thead>
        <tr>
            <th>Titre</th>
        </tr>
    </thead>
    <tbody>
        <?php /* foreach ($carpools as $carpool):  */ ?>
            <tr>
                <td><?php /* echo $carpool->departure_city */ ?> </td>
            </tr>
        <?php /* endforeach; */ ?>
    </tbody>
</table> -->

<!-- Find a carpool -->
<div class="flex-column gap-24 block-light-grey">
    <h2 class="text-green text-bold">Rechercher un covoiturage</h2>
    <form class="block-search flex-column-ms" action="carpool_search.php" method="POST">
        <input type="hidden" name="action" value="search"> <!--identify request-->

        <div class="flex-row search-field">
            <img class="img-width-20" src="<?= ASSETS_PATH ?>/icons/Localisation(2).png" alt="lieu de départ">
            <input type="text" id="departure-city-search" name="departure-city-search" class="font-size-small text-breakable  "
                placeholder="Ville de départ"
                value="<?= htmlspecialchars($filters['departure']) ?>"
                required>
            <div id="departure-suggestions" class="suggestions-list"></div>
        </div>
        <span class="flex-row">→</span>
        <div class="flex-row search-field">
            <img class="img-width-20" src="<?= ASSETS_PATH ?>/icons/Localisation(2).png" alt="">
            <input type="text" id="arrival-city-search" name="arrival-city-search" class="font-size-small text-breakable  "
                placeholder="Ville d'arrivée"
                value="<?= htmlspecialchars($filters['arrival']) ?>"
                required>
            <div id="arrival-suggestions" class="suggestions-list"></div>
        </div>
        <div class="flex-row search-field">
            <img class="img-pointer" src="<?= ASSETS_PATH ?>/icons/Calendrier2.png" alt="Calendrier">
            <input type="date" id="departure-date-search" name="departure-date-search"
                class="date-field font-size-small  " style="width:110px;"
                value="<?= htmlspecialchars($dateInput) ?>"
                required>
        </div>
        <div class="flex-row" style="width:100%;">
            <div class="btn bg-light-green" id="search-btn">
                <img class="img-width-20" src="<?= ASSETS_PATH ?>/icons/LoupeRecherche.png" alt="">
                <input type="submit" value="Rechercher">
            </div>
            <div id="filter-icon" class="hidden">
                <button type="button" id="filter-toggle" class="btn">
                    <img class="img-width-20" src="<?= ASSETS_PATH ?>/icons/Filtre.png" alt="">
                </button>
            </div>
        </div>
    </form>
</div>

<div class="block-filter-details flex-column-ms">

    <!--Search filters-->

    <div class="flex-column block-light-grey" id="filter-block">
        <h3 class="text-green" style="padding-bottom: 24px;">Filtres de recherche</h3>
        <form class="block-column-g20" action="carpool_search.php" method="POST">
            <input type="hidden" name="action" value="filters"> <!--identify filters-->

            <div class="flex-row">
                <input id="eco" name="eco" type="radio" <?= !empty($filters['eco']) ? 'checked' : ''  ?>>
                <label for="eco">Voyage écologique</label>
            </div>
            <div class="flex-row">
                <label for="max-price">Prix (max)</label>
                <input type="number" id="max-price" name="max-price" class="short-field" min="1"
                    value="<?= $filters['maxPrice'] ?>">
            </div>
            <div class="flex-row">
                <label for="max-duration">Durée (max)</label>
                <input type="number" id="max-duration" name="max-duration" class="short-field" min="1"
                    value="<?= $filters['maxDuration'] ?>">
                <label for="max-duration">h</label>
            </div>
            <div class="flex-row">
                <label for="driver-rating-list">Note chauffeur (min) </label>

                <select id="driver-rating-list" name="driver-rating-list" class="short-field">
                    <optgroup>
                        <option value="none" <?= (isset($filters['driverRating']) && strval($filters['driverRating'] === "none")) ? 'selected' : ''; ?>></option>
                        <option value="5" <?= (isset($filters['driverRating']) && strval($filters['driverRating'] === "5")) ? 'selected' : ''; ?>>5</option>
                        <option value="4.5" <?= (isset($filters['driverRating']) && strval($filters['driverRating'] === "4.5")) ? 'selected' : ''; ?>>4.5</option>
                        <option value="4" <?= (isset($filters['driverRating']) && strval($filters['driverRating'] === "4")) ? 'selected' : ''; ?>>4</option>
                        <option value="3.5" <?= (isset($filters['driverRating']) && strval($filters['driverRating'] === "3.5")) ? 'selected' : ''; ?>>3.5</option>
                        <option value="3" <?= (isset($filters['driverRating']) && strval($filters['driverRating'] === "3")) ? 'selected' : ''; ?>>3</option>
                        <option value="2.5" <?= (isset($filters['driverRating']) && strval($filters['driverRating'] === "2.5")) ? 'selected' : ''; ?>>2.5</option>
                        <option value="2" <?= (isset($filters['driverRating']) && strval($filters['driverRating'] === "2")) ? 'selected' : ''; ?>>2</option>
                        <option value="1" <?= (isset($filters['driverRating']) && strval($filters['driverRating'] === "1")) ? 'selected' : ''; ?>>1</option>
                    </optgroup>
                </select>
                <label for="driver-rating-list"><img src="<?= ASSETS_PATH ?>/icons/EtoileJaune.png" alt="EtoileJaune"
                        class="img-width-20"></label>
            </div>
            <div class="btn bg-light-green">
                <input type="submit" value="Appliquer les filtres">

            </div>
            <button type="submit" name="action" value="reset_filters" class="btn col-back-grey-btn">Réinitialiser
                les
                filtres</button>
        </form>
    </div>
    <div class="flex-column">
        <span class="flex-row flex-center text-bold font-size-big">
            <?= htmlspecialchars($dateLong) ?>
        </span>
        <!--TRAVELS' SEARCHED BLOCK-->
        <div class="flex-column gap-12 pad-20 pad-10-ss grid-auto-columns">

            <?php
            if (!empty($carpools)) {
                foreach ($carpools as $t): ?>


                    <?= 'résultat : ' . $t['departure_city'] ?>
                <?php endforeach;
            } elseif (isset($_POST['action'])) {
                echo "Oups.. Aucun covoiturage n'est proposé pour cette recherche.";
            }

            if (!empty($nextTravelDate)) {
                // Take the first travel found 
                $firstTravel = $nextTravelDate[0];

                echo "<br><br>"; ?>

                <!-- Form to restart search with new date -->
                <form method="POST" action="carpool_search.php">
                    <input type="hidden" name="action" value="search">
                    <input type="hidden" name="departure-date-search"
                        value="<?= htmlspecialchars($firstTravel['travel_date']) ?>">
                    <input type="hidden" name="departure-city-search"
                        value="<?= htmlspecialchars($departureCitySearch) ?>">
                    <input type="hidden" name="arrival-city-search" value="<?= htmlspecialchars($arrivalCitySearch) ?>">
                    <input type="hidden" name="eco" value="<?= htmlspecialchars($eco) ?>">
                    <input type="hidden" name="max-price" value="<?= htmlspecialchars($maxPrice) ?>">
                    <input type="hidden" name="max-duration" value="<?= htmlspecialchars($maxDuration) ?>">
                    <input type="hidden" name="driver-rating-list" value="<?= htmlspecialchars($driverRating) ?>">

                    <button type="submit" class="btn bg-very-light-green" style="padding: 10px;">Prochain itinéraire
                        pour cette recherche le
                        <?= htmlspecialchars(formatDateLong($firstTravel['travel_date'])) ?></button>
                </form>
            <?php } ?>

        </div>
    </div>
</div>

<script src="<?= ASSETS_PATH ?>js/carpool_list.js" defer></script>