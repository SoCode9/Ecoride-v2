 <div class="popup" id="new-car" style="display:none;">
     <h3 class="m-tb-12">Ajouter une voiture</h3>
     <div class="block-column-g20">

         <form id="car-form-id" class="block-column-g20" style="gap: 10px;">

             <div class="flex-row flex-column-ss">
                 <label for="licence_plate">Plaque immatriculation : </label>
                 <input type="text" id="licence-plate" name="licence_plate" class="text-field" placeholder="AA-000-AA">
             </div>

             <div class="flex-row flex-column-ss">
                 <label for="first_registration_date">Date première immatriculation : </label>
                 <input type="date" id="first-registration-date" name="first_registration_date" class="text-field">
             </div>
             <div class="flex-row flex-column-ss">
                 <label for="brand">Marque : </label>
                 <select id="brand" class="text-field" name="brand">
                     <option value="">Sélectionner</option>
                     <?php foreach ($brands as $brand): ?>
                         <option value="<?= htmlspecialchars($brand['id']); ?>">
                             <?= htmlspecialchars($brand['name']); ?>
                         </option>
                     <?php endforeach; ?>
                 </select>
             </div>
             <div class="flex-row flex-column-ss">
                 <label for="model">Modèle : </label>
                 <input type="text" id="model" name="model" class="text-field">
             </div>
             <div class="flex-row">
                 <label for="electric">Electrique : </label>
                 <input type="radio" name="electric" value="yes" id="electric-yes">
                 <label for="electric_yes">oui</label>

                 <input type="radio" name="electric" value="no" id="electric-no">
                 <label for="electric_no">non</label>

             </div>
             <div class="flex-row flex-column-ss">
                 <label for="color">Couleur : </label>
                 <input type="text" id="color" name="color" class="text-field">
             </div>
             <div class="flex-row">
                 <label for="nb_passengers">Nombre de passagers possible : </label>
                 <input type="number" id="nb-passengers" name="nb_passengers" class="text-field"
                     style="width: 40px;">
             </div>
             <div class="btn bg-light-green">
                 <button type="submit">Enregistrer la voiture</button>
             </div>
             <button type="button" class="col-back-grey-btn btn" style="justify-self:right;"
                 onclick="closePopup('new-car')">Annuler</button>
         </form>

     </div>

 </div>