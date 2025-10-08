<?php foreach ($driver->getOtherPref() as $preference): ?>
  <?php if ($preference !== null) : ?>
    <hr>
    <span style='display:flex; gap:4px; align-items:center;'><?php echo $preference['custom_preference'] ?>
      <form method="POST" action="<?= BASE_URL ?>/preference/delete" class="hidden delete-pref-form">
        <input type="hidden" name="id" value="<?= (int)$preference['custom_preference'] ?>">
        <button type="submit">
          <img src="<?= ASSETS_PATH ?>/icons/Supprimer.png" class="img-width-20" style="cursor:pointer;">
        </button>
      </form>
    </span>
  <?php endif; ?>
<?php endforeach; ?>