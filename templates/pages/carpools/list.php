<!-- Find a carpool -->
<div class="flex-column gap-24 block-light-grey">
    <h2 class="text-green text-bold">Rechercher un covoiturage</h2>
    <form class="block-search flex-column-ms" action="<?= BASE_URL ?>/covoiturages" method="POST">
        <input type="hidden" name="action" value="search"> <!--identify request-->

        <div class="flex-row search-field">
            <img class="img-width-20" src="<?= ASSETS_PATH ?>/icons/Localisation(2).png" alt="lieu de départ">
            <input type="text" id="departure-city-search" name="departure" class="font-size-small text-breakable  "
                placeholder="Ville de départ"
                value="<?= htmlspecialchars($filters['departure'] ?? '')  ?>"
                required>
            <div id="departure-suggestions" class="suggestions-list"></div>
        </div>
        <span class="flex-row">→</span>
        <div class="flex-row search-field">
            <img class="img-width-20" src="<?= ASSETS_PATH ?>/icons/Localisation(2).png" alt="">
            <input type="text" id="arrival-city-search" name="arrival" class="font-size-small text-breakable  "
                placeholder="Ville d'arrivée"
                value="<?= htmlspecialchars($filters['arrival'] ?? '') ?>"
                required>
            <div id="arrival-suggestions" class="suggestions-list"></div>
        </div>
        <div class="flex-row search-field">
            <img class="img-pointer" src="<?= ASSETS_PATH ?>/icons/Calendrier2.png" alt="Calendrier">
            <input type="date" id="departure-date-search" name="date"
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
        <form class="block-column-g20" action="<?= BASE_URL ?>/covoiturages" method="POST">
            <input type="hidden" name="action" value="filters"> <!--identify filters-->

            <div class="flex-row">
                <input id="eco" type="checkbox" name="eco" value="1" <?= !empty($filters['eco']) ? 'checked' : '' ?>>
                <label for="eco">Voyage écologique</label>
            </div>
            <div class="flex-row">
                <label for="max-price">Prix (max)</label>
                <input type="number" id="max-price" name="maxPrice" class="short-field" min="1"
                    value="<?= $filters['maxPrice'] ?>">
            </div>
            <div class="flex-row">
                <label for="max-duration">Durée (max)</label>
                <input type="number" id="max-duration" name="maxDuration" class="short-field" min="1"
                    value="<?= $filters['maxDuration'] ?>">
                <label for="max-duration">h</label>
            </div>
            <div class="flex-row">
                <label for="driver-rating-list">Note chauffeur (min) </label>

                <select id="driver-rating-list" name="driverRating" class="short-field">
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
        <!--CARPOOL'S SEARCHED BLOCK-->
        <div class="flex-column gap-12 pad-20 pad-10-ss grid-auto-columns">

            <?php if (!empty($carpools)): ?>
                <?php foreach ($carpools as $c): ?>
                    <div class="travel flex-column-ms"
                        onclick="window.location.href='<?= $c['detail_url'] ?>'"
                        style="<?= $c['owner_style'] ?>">

                        <?php if ($c['completed']): ?>
                            <span class="watermark-complet">Complet</span>
                        <?php endif; ?>

                        <div class="user-header-mobile">
                            <div class="photo-user-container" style="justify-self:center;">
                                <img src="<?= htmlspecialchars($c['driver_photo'] ?? '') ?>"
                                    alt="Photo de l'utilisateur"
                                    class="photo-user">
                            </div>
                            <div class="user-info-mobile">
                                <span class="pseudo-user"><?= htmlspecialchars($c['driver_pseudo']) ?></span>
                                <div class="driver-rating">
                                    <div class="flex-row font-size-very-small">
                                        <?= $c['driver_rating']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <span class="date-travel">Départ à <?= htmlspecialchars($c['departure_time']) ?></span>
                        <span class="hours-travel">Arrivée à <?= htmlspecialchars($c['arrival_time']) ?></span>

                        <?php if (!empty($c['seats_label'])): ?>
                            <span class="seats-available" id="seats-bs">Encore <?= htmlspecialchars($c['seats_label']) ?></span>
                        <?php endif; ?>

                        <?php if (!empty($c['eco_label'])): ?>
                            <div class="criteria-eco-div">
                                <img src="<?= ASSETS_PATH ?>icons/Arbre1.png" alt="Arbre" width="20px">
                                <span class="criteria-eco"><?= htmlspecialchars($c['eco_label']) ?></span>
                            </div>
                        <?php endif; ?>

                        <span class="travel-price text-bold"><?= $c['price_label'] ?></span>
                    </div>
                <?php endforeach; ?>
            <?php elseif (!empty($showNoResults)): ?>
                Oups.. Aucun covoiturage n'est proposé pour cette recherche.
            <?php endif; ?>


            <!--Display of the date of the next carpool that matches the search criteria-->
            <?php if (!empty($nextCarpool)): ?>
                <form method="POST" action="<?= BASE_URL ?>/covoiturages">
                    <input type="hidden" name="action" value="search">

                    <input type="hidden" name="departure" value="<?= htmlspecialchars((string)($nextCarpool['filters']['departure'] ?? '')) ?>">
                    <input type="hidden" name="arrival" value="<?= htmlspecialchars((string)($nextCarpool['filters']['arrival']   ?? '')) ?>">
                    <input type="hidden" name="date" value="<?= htmlspecialchars((string)($nextCarpool['date_db'] ?? '')) ?>">
                    <input type="hidden" name="eco" value="1">
                    <input type="hidden" name="maxPrice" value="<?= (int)$nextCarpool['filters']['maxPrice'] ?>">
                    <input type="hidden" name="maxDuration" value="<?= (int)$nextCarpool['filters']['maxDuration'] ?>">
                    <input type="hidden" name="driverRating" value="<?= htmlspecialchars((string)$nextCarpool['filters']['driverRating']) ?>">

                    <button type="submit" class="btn bg-very-light-green" style="padding:10px;">
                        Prochain itinéraire pour cette recherche le <?= htmlspecialchars((string)$nextCarpool['date_ui']) ?>
                    </button>
                </form>
            <?php endif; ?>

        </div>
    </div>

    <script src="<?= ASSETS_PATH ?>js/carpool_list.js" defer></script>