<?php foreach ($driver->getOtherPref() as $preference): ?>
  <?php if ($preference !== null) : ?>
    <hr>
    <span style='display:flex; gap:4px; align-items:center;'><?php echo $preference['custom_preference'] ?>
      <form method="POST" action="<?= BASE_URL ?>/preference/delete" class="hidden delete-pref-form">
        <input type="hidden" name="id" value="<?= (string) $preference['_id'] ?>">
        <button type="submit">
          <img src="<?= ASSETS_PATH ?>/icons/Supprimer.png" class="img-width-20" style="cursor:pointer;">
        </button>
      </form>


      <button type="button" id="edit-pref-button" class="hidden edit-custom-pref-img">
        <img src="<?= ASSETS_PATH ?>/icons/Modifier.png" class="img-width-20" style="cursor:pointer;">
      </button>

      <div class="popup" id="edit-custom-pref" class="hidden update-pref-form">
        <h3 class="m-tb-12">Modifier la préférence</h3>
        <div class="block-column-g20">

          <form method="POST" action="<?= BASE_URL ?>/preference/update" class="block-column-g20">
            <input type="hidden" name="id" value="<?= (string) $preference['_id'] ?>"> <!--custom_preference -->
            <span>Renommer : <?= $preference['custom_preference'] ?></span>
            <input type="text" name="newCustomPref" id="newCustomPref">
            <div class="btn bg-light-green">
              <button type="submit">Enregistrer la préférence</button>
            </div>
          </form>
        </div>
      </div>



    </span>
  <?php endif; ?>
<?php endforeach; ?>