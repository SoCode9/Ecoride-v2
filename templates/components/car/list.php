<?php

use App\Utils\Formatting\DateFormatter;

if (empty($cars)): ?>
    <span class="italic" style='color:red;'>Ajouter au moins une voiture</span>
<?php else: ?>
    <?php $totalCars = count($cars); ?>
    <?php foreach ($cars as $index => $car): ?>
        <div class="flex-column" id="car">
            <span>Plaque immatriculation : <?= htmlspecialchars($car['licence_plate']) ?></span>
            <span>Date première immatriculation :
                <?= DateFormatter::short(htmlspecialchars($car['first_registration_date'])) ?></span>
            <span>Marque : <?= htmlspecialchars($car['name']) ?></span>
            <span>Modèle : <?= htmlspecialchars($car['model']) ?></span>
            <span>Electrique :
                <?php
                $electric = (htmlspecialchars($car['electric']) == 1) ? 'Oui' : 'Non';
                echo $electric;
                ?>
            </span>
            <span>Couleur : <?= htmlspecialchars($car['color']) ?></span>
            <span>Nombre de passagers possible : <?= htmlspecialchars($car['seats_offered']) ?></span>
            <a class="hidden delete-car-icon"
                href="<?= BASE_URL ?>/back/car/delete.php?action=delete_car&id=<?= $car['car_id'] ?>">
                <img src="<?= ASSETS_PATH ?>/icons/Supprimer.png" class="img-width-20" style="cursor: pointer;">
            </a>
            <?php if ($index !== $totalCars - 1):
                echo '<hr>';
            endif; ?>
        </div>

    <?php endforeach; ?>
<?php endif; ?>