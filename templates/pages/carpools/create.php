<main class="gap-24">
    <div class="main-header">
        <h2 class="text-green">Proposer un covoiturage</h2>
        <a href="<?= BASE_URL ?>/mes-covoiturages" class="btn return-btn">Retour à l'espace
            utilisateur</a>
    </div>

    <form action="<?= BASE_URL ?>/carpool/new" method="POST" class="half-separation">
        <input type="hidden" name="csrf" value='<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>'>

        <div class="form-group full-width-grid">
            <label for="travel-date">Date du départ</label>
            <input type="date" id="travel-date" name="travel-date" value="<?= htmlspecialchars($_SESSION['form_old']['travel-date'] ?? '') ?>" />
            <?php if (!empty($_SESSION['form_errors']['travel-date'])): ?>
                <p class="text-error"><?= htmlspecialchars($_SESSION['form_errors']['travel-date']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group" style="position: relative;">
            <label for="departure-city-search">Ville de départ</label>
            <input type="text" id="departure-city-search" name="departure-city-search" value="<?= htmlspecialchars($_SESSION['form_old']['departure-city-search'] ?? '') ?>" />
            <?php if (!empty($_SESSION['form_errors']['departure-city-search'])): ?>
                <p class="text-error"><?= htmlspecialchars($_SESSION['form_errors']['departure-city-search']) ?></p>
            <?php endif; ?>
            <div id="departure-suggestions" class="suggestions-list"></div>
        </div>

        <div class="form-group">
            <label for="travel-departure-time">Heure de départ</label>
            <input type="time" id="travel-departure-time" name="travel-departure-time" value="<?= htmlspecialchars($_SESSION['form_old']['travel-departure-time'] ?? '') ?>" />
            <?php if (!empty($_SESSION['form_errors']['travel-departure-time'])): ?>
                <p class="text-error"><?= htmlspecialchars($_SESSION['form_errors']['travel-departure-time']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group" style="position: relative;">
            <label for="arrival-city-search">Ville d'arrivée</label>
            <input type="text" id="arrival-city-search" name="arrival-city-search" value="<?= htmlspecialchars($_SESSION['form_old']['arrival-city-search'] ?? '') ?>" />
            <?php if (!empty($_SESSION['form_errors']['arrival-city-search'])): ?>
                <p class="text-error"><?= htmlspecialchars($_SESSION['form_errors']['arrival-city-search']) ?></p>
            <?php endif; ?>
            <div id="arrival-suggestions" class="suggestions-list"></div>
        </div>

        <div class="form-group">
            <label for="travel-arrival-time">Heure d'arrivée</label>
            <input type="time" id="travel-arrival-time" name="travel-arrival-time" value="<?= htmlspecialchars($_SESSION['form_old']['travel-arrival-time'] ?? '') ?>" />
            <?php if (!empty($_SESSION['form_errors']['travel-arrival-time'])): ?>
                <p class="text-error"><?= htmlspecialchars($_SESSION['form_errors']['travel-arrival-time']) ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="travel-price">Prix pour une personne</label>
            <div class="flex-row">
                <input type="number" id="travel-price" name="travel-price" min="2" value="<?= htmlspecialchars($_SESSION['form_old']['travel-price'] ?? '') ?>" />
                <span>crédits</span>
                <?php if (!empty($_SESSION['form_errors']['travel-price'])): ?>
                    <p class="text-error"><?= htmlspecialchars($_SESSION['form_errors']['travel-price']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="flex-row">
            <img src="<?= ASSETS_PATH ?>/icons/addPref.png" alt="info" class="img-width-20" />
            <span class="italic text-green font-size-small">Rappel : 2 crédits sont pris par la plateforme
                EcoRide</span>
        </div>

        <?php include TEMPLATE_PATH . '/components/car/select.php' ?>

        <div class="form-group">
            <button type="button" onclick="showPopup(event)" class="btn action-btn"
                style="background-color:inherit; border: 1.5px solid black; width:fit-content;">Autre voiture</button>
        </div>

        <div class="form-group full-width-grid">
            <label for="comment">Ajouter un commentaire (facultatif)</label>
            <textarea id="comment" name="comment" rows="4"></textarea>
        </div>

        <div class="btn bg-light-green full-width-grid">
            <input type="submit" value="Proposer le trajet" />
        </div>
    </form>

    <?php include TEMPLATE_PATH . '/components/car/new.php'; ?>

</main>
<?php unset($_SESSION['form_errors'], $_SESSION['form_old'], $_SESSION['error_message']); ?>

<script src="<?= ASSETS_PATH ?>js/carpool_new.js" defer></script>