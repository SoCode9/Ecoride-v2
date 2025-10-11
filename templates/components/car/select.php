<div class="form-group" id="car-field">
    <label for="carSelected">Voiture</label>
    <select id="car-selected" name="carSelected" required>
        <?php foreach ($cars as $car): ?>
            <option value="<?= htmlspecialchars($car['car_id']) ?>">
                <?= htmlspecialchars($car['name'] . " " . $car['model']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>