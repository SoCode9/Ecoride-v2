 <?php
    $currentTab = $_GET['tab'] ?? 'profil';
    ?>
 <div class="tabs" style="justify-content: space-between;">
     <button class="btn main-tab-btn <?= $currentTab === 'profil' ? 'active' : '' ?>" style=" width:100%;"
         data-target="profil" onclick="window.location.href='<?= BASE_URL ?>/mon-profil'">Mon profil</button>
     <button class="btn main-tab-btn <?= $currentTab === 'carpools' ? 'active' : '' ?>" style=" width:100%;"
         data-target="carpools" onclick="window.location.href='<?= BASE_URL ?>/mes-covoiturages'">Mes
         covoiturages</button>
 </div>


 <!-- User's informations -->
 <section class="flex-column gap-24">
     <!--header of this section-->
     <div class="main-header">
         <div class="flex-row item-center">
             <img src="<?= htmlspecialchars($formattedUser['photo']) ?>" alt="Photo de l'utilisateur"
                 class="photo-100">
             <div class="flex-column gap-8">
                 <span
                     class="pseudo-user text-breakable"><?= htmlspecialchars($user->getPseudo()) ?></span>
                 <div class="flex-row" style="padding-left: 10px;"><?= $formattedUser['rating'] ?></div>
             </div>
         </div>

         <button class="btn action-btn content-btn active" id="edit-button">Modifier</button>
         <button class="btn action-btn content-btn " style="background-color : var(--col-light-green); "
             id="save-button">Sauvegarder</button>

     </div>
     <div class="flex-row item-center hidden" id="edit-photo-icon">
         <button onclick="showPopup('new-photo')"
             style="width: 30px; background-color: var(--col-orange); padding:4px 4px;" class="btn"><img
                 src="<?= ASSETS_PATH ?>/icons/Modifier.png" alt="edit">
         </button>
         <span class="italic font-size-small ">Modifier la photo de profil</span>
     </div>

     <!-- popup edit photo-->
     <div class="popup" id="new-photo" style="display:none;">
         <h3 class=" m-tb-12">Modifier la photo de profil</h3>
         <div class="block-column-g20">

             <span>Sélectionner une photo de profil</span>
             <form action="<?= BASE_URL ?>/mon-profil/photo" method="POST" enctype="multipart/form-data"
                 onsubmit="console.log('Form submitted!')" class="flex-column gap-24">
                 <input type="hidden" name="action" value="edit-photo-user">
                 <input type="file" name="new_photo" id="photo" required>
                 <div class="btn bg-light-green">
                     <button type="submit">Enregistrer la photo</button>
                 </div>
             </form>

         </div>
         <button type="button" class="col-back-grey-btn btn" style="justify-self:right;"
             onclick="closePopup('new-photo')">Annuler</button>
     </div>


     <div class="flex-row flex-between">
         <span><?= htmlspecialchars($user->getMail()) ?></span>
         <span><?= htmlspecialchars($user->getCredit()) ?> crédits</span>
     </div>
     <div class="flex-column gap-8">
         <h3 class="text-green">Type d'utilisateur</h3>
         <div class="flex-row gap-24">
             <div class="flex-row">
                 <label for="role-passenger" class="radio-not-edit">passager</label>
                 <input type="radio" name="user_role" class="radio-not-edit" id="role-passenger"
                     <?php if ($user->getIdRole() === 1) {
                            echo 'checked';
                        } ?>>
             </div>
             <div class="flex-row">
                 <label for="role-driver" class="radio-not-edit">chauffeur</label>
                 <input type="radio" name="user_role" class="radio-not-edit" id="role-driver"
                     <?php if ($user->getIdRole() === 2) {
                            echo 'checked';
                        } ?>>
             </div>
             <div class="flex-row">
                 <label for="role-both" class="radio-not-edit">les deux</label>
                 <input type="radio" name="user_role" class="radio-not-edit" id="role-both"
                     <?php if ($user->getIdRole() === 3) {
                            echo 'checked';
                        } ?>>
             </div>
         </div>
     </div>

     <div class="scrollable-container half-separation">
         <!--Cars section-->
         <div class="flex-column gap-8 block-light-grey no-background-ss" style="padding: 16px;">
             <h3 class="text-green">Voitures</h3>

             <div id="car-container" class="flex-column gap-8 grid-auto-columns">
                 <?php include TEMPLATE_PATH . '/components/car/list.php'; ?>
             </div>


             <div class="carForm hidden">
                 <hr>
                 <form id="car-form" class="ajax-form block-column-g20 " style="gap: 10px;">
                     <input type="hidden" name="action" value="formCar">

                     <div class="flex-row">
                         <label for="licence-plate">Plaque immatriculation : </label>
                         <input type="text" id="licence-plate" name="licence_plate" class="text-field"
                             placeholder="AA-000-AA" required>
                     </div>

                     <div class="flex-row">
                         <label for="first-registration-date">Date première immatriculation : </label>
                         <input type="date" id="first-registration-date" name="first_registration_date"
                             class="text-field" required>
                     </div>
                     <div class="flex-row">
                         <label for="brand">Marque : </label>
                         <select id="brand" class="text-field" name="brand" required>
                             <option value="">Sélectionner</option>
                             <?php foreach ($brands as $brand): ?>
                                 <option value="<?= htmlspecialchars($brand['id']);  ?>">
                                     <?= htmlspecialchars($brand['name']); ?>
                                 </option>
                             <?php endforeach; ?>
                         </select>
                     </div>
                     <div class="flex-row">
                         <label for="model">Modèle : </label>
                         <input type="text" id="model" name="model" class="text-field" required>
                     </div>
                     <div class="flex-row">
                         <label for="electric">Electrique : </label>
                         <input type="radio" name="electric" value="yes" id="electric-yes" required>
                         <label for="electric_yes">oui</label>

                         <input type="radio" name="electric" value="no" id="electric-no" required>
                         <label for="electric_no">non</label>

                     </div>
                     <div class="flex-row">
                         <label for="color">Couleur : </label>
                         <input type="text" id="color" name="color" class="text-field" required>
                     </div>
                     <div class="flex-row">
                         <label for="nb-passengers">Nombre de passagers possible : </label>
                         <input type="number" id="nb-passengers" name="nb_passengers"
                             class="text-field" style="width: 40px;" required>
                     </div>
                     <div class="btn bg-light-green" style="width:100px; align-self:self-end;">
                         <input type="submit" value="Enregistrer"></input>
                     </div>

                 </form>
                 <hr>
             </div>
             <button class="btn action-btn hidden" id="add-car-button">Ajouter une voiture</button>

         </div>

         <!--preferences section-->
         <div class="flex-column gap-8 block-light-grey no-background-ss" style="padding: 16px;">
             <h3 class="text-green">Préférences en tant que chauffeur</h3>
             <div class="flex-column gap-8 grid-auto-columns">
                 <div class="flex-column gap-8 ">
                     <span>Voyager avec des fumeurs ne me dérange pas</span>
                     <div class="flex-row flex-between">
                         <div class="flex-row">
                             <label for="smoke-yes" class="radio-not-edit">Oui</label>
                             <input type="radio" class="radio-not-edit" name="smoke_pref" id="smoke-yes"
                                 <?php if (isset($driver) && ($driver->getSmoker() === true)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                         <div class="flex-row">
                             <label for="smoke-no" class="radio-not-edit">Non</label>
                             <input type="radio" class="radio-not-edit" name="smoke_pref" id="smoke-no"
                                 <?php if (isset($driver) && ($driver->getSmoker() === false)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                         <div class="flex-row">
                             <label for="smoke-undefined" class="radio-not-edit">Pas de préférence</label>
                             <input type="radio" class="radio-not-edit" name="smoke_pref" id="smoke-undefined"
                                 <?php if (!isset($driver) || ($driver->getSmoker() === null)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                     </div>

                     <hr>
                 </div>
                 <div class="flex-column gap-8 ">
                     <span>J'aime la compagnie des animaux</span>
                     <div class="flex-row flex-between">
                         <div class="flex-row">
                             <label for="pet-yes" class="radio-not-edit">Oui</label>
                             <input type="radio" class="radio-not-edit" name="pet_pref" id="pet-yes"
                                 <?php if (isset($driver) && ($driver->getPets() === true)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                         <div class="flex-row">
                             <label for="pet-no" class="radio-not-edit">Non</label>
                             <input type="radio" class="radio-not-edit" name="pet_pref" id="pet-no"
                                 <?php if (isset($driver) && ($driver->getPets() === false)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                         <div class="flex-row">
                             <label for="pet-undefined" class="radio-not-edit">Pas de préférence</label>
                             <input type="radio" class="radio-not-edit" name="pet_pref" id="pet-undefined"
                                 <?php if (isset($driver) && ($driver->getPets() === null)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                     </div>
                     <hr>
                 </div>
                 <div class="flex-column gap-8 ">
                     <span>La nourriture est autorisée dans la voiture</span>
                     <div class="flex-row flex-between">
                         <div class="flex-row">
                             <label for="food-yes" class="radio-not-edit">Oui</label>
                             <input type="radio" class="radio-not-edit" name="food_pref" id="food-yes"
                                 <?php if (isset($driver) && ($driver->getFood() === true)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                         <div class="flex-row">
                             <label for="food-no" class="radio-not-edit">Non</label>
                             <input type="radio" class="radio-not-edit" name="food_pref" id="food-no"
                                 <?php if (isset($driver) && ($driver->getFood() === false)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                         <div class="flex-row">
                             <label for="food-undefined" class="radio-not-edit">Pas de préférence</label>
                             <input type="radio" class="radio-not-edit" name="food_pref" id="food-undefined"
                                 <?php if (isset($driver) && ($driver->getFood() === null)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                     </div>
                     <hr>
                 </div>
                 <div class="flex-column gap-8 ">
                     <span>Je discute volontiers avec mes passagers</span>
                     <div class="flex-row flex-between">
                         <div class="flex-row">
                             <label for="speak-yes" class="radio-not-edit">Oui</label>
                             <input type="radio" class="radio-not-edit" name="speak_pref" id="speak-yes"
                                 <?php if (isset($driver) && ($driver->getSpeaker() === true)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                         <div class="flex-row">
                             <label for="speak-no" class="radio-not-edit">Non</label>
                             <input type="radio" class="radio-not-edit" name="speak_pref" id="speak-no"
                                 <?php if (isset($driver) && ($driver->getSpeaker() === false)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                         <div class="flex-row">
                             <label for="speak_undefined" class="radio-not-edit">Pas de préférence</label>
                             <input type="radio" class="radio-not-edit" name="speak_pref" id="speak-undefined"
                                 <?php if (isset($driver) && ($driver->getSpeaker() === null)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                     </div>
                     <hr>
                 </div>
                 <div class="flex-column gap-8 ">
                     <span>J'aime conduire en écoutant de la musique</span>
                     <div class="flex-row flex-between">
                         <div class="flex-row">
                             <label for="music-yes" class="radio-not-edit">Oui</label>
                             <input type="radio" class="radio-not-edit" name="music_pref" id="music-yes"
                                 <?php if (isset($driver) && ($driver->getMusic() === true)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                         <div class="flex-row">
                             <label for="music-no" class="radio-not-edit">Non</label>
                             <input type="radio" class="radio-not-edit" name="music_pref" id="music-no"
                                 <?php if (isset($driver) && ($driver->getMusic() === false)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                         <div class="flex-row">
                             <label for="music-undefined" class="radio-not-edit">Pas de préférence</label>
                             <input type="radio" class="radio-not-edit" name="music_pref" id="music-undefined"
                                 <?php if (isset($driver) && ($driver->getMusic() === null)) {
                                        echo 'checked';
                                    } ?>>
                         </div>
                     </div>
                 </div>
             </div>
             <div id="pref-container">
                  <?php include TEMPLATE_PATH . '/components/otherPreferences/list.php'; ?>
             </div>



             <div class="pref-form hidden">
                 <form action="" method="POST" id="pref-form" class="ajax-form block-column-g20"
                     style="gap: 10px;">
                     <input type="hidden" name="action" value="formPref">

                     <hr>
                     <input type="text" placeholder="Entrez la préférence" name="new_pref" id="new-pref"
                         class="text-field" style="width:auto" required>

                     <div class="btn bg-light-green" style="width:100px; align-self:self-end;">
                         <input type="submit" value="Enregistrer">
                     </div>
                 </form>
                 <hr>
             </div>
             <button class="btn action-btn hidden" id="add-pref-button">Ajouter une préférence</button>
         </div>
     </div>
 </section>

 <script>
     window.BASE_URL = "<?= rtrim(BASE_URL, '/') ?>";
 </script>
 <script src="<?= ASSETS_PATH ?>js/user_space.js" defer></script>