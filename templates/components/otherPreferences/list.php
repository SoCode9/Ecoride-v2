<?php foreach ($driver->getOtherPref() as $preference): ?>
  <?php if ($preference !== null): ?>
    <hr>
    <span style='display:flex; gap:4px; align-items:center;'><?php echo $preference['custom_preference'] ?>
      <form method="POST" action="<?= BASE_URL ?>/preference/delete" class="hidden delete-pref-form">
        <input type="hidden" name="id" value="<?= (string) $preference['_id'] ?>">
        <button type="submit">
          <img src="<?= ASSETS_PATH ?>/icons/Supprimer.png" class="img-width-20" style="cursor:pointer;">
        </button>
      </form>


      <button type="button" class="edit-pref-button" data-id="<?= (string) $preference['_id'] ?>"
        data-label="<?= htmlspecialchars($preference['custom_preference'], ENT_QUOTES, 'UTF-8') ?>">
        <img src="<?= ASSETS_PATH ?>/icons/Modifier.png" class="img-width-20" style="cursor:pointer;">
      </button>


    </span>
  <?php endif; ?>
<?php endforeach; ?>

<div class="popup hidden" id="edit-custom-pref">
  <h3 class="m-tb-12">Modifier la préférence</h3>

  <form method="POST" action="<?= BASE_URL ?>/preference/update" class="block-column-g20">
    <input type="hidden" name="id" id="edit-pref-id">
    <input type="text" name="newCustomPref" id="newCustomPref">

    <div class="btn bg-light-green">
      <button type="submit">Enregistrer la préférence</button>
    </div>

    <button type="button" class="col-back-grey-btn btn" onclick="closePopup('edit-custom-pref')">
      Annuler
    </button>
  </form>
</div>