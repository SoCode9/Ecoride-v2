<div class="popup" id="new-employee" style="display:none;">
    <h3 class="m-tb-12">Ajouter un compte employé</h3>
    <div class="block-column-g20"></div>

    <form id="employee-form-id" class="block-column-g20" style="gap: 10px;">
        <div class="flex-row gap-8">
            <label for="pseudo-employee">Pseudo : </label>
            <input type="text" id="pseudo-employee" name="pseudo-employee" class="text-field" style="flex:1;" required>
        </div>
        <div class="flex-row gap-8">
            <label for="mail-employee">Email : </label>
            <input type="email" id="mail-employee" name="mail-employee" autocomplete="username" class="text-field"
                style="flex:1;" required>
        </div>
        <div class="flex-column">
            <div class="flex-row gap-8">
                <label for="password-employee">Mot de passe : </label>
                <input type="password" id="password-employee" name="password-employee" autocomplete="new-password"
                    class="text-field" style="flex:1;" required>
            </div>
            <p>
                ✔ 8 caractères minimum <br>
                ✔ 1 majuscule et 1 minuscule minimum<br>
                ✔ 1 chiffre minimum<br>
                ✔ 1 caractère spécial minimum
            </p>
        </div>
        <div class="btn bg-light-green">
            <button type="submit">Enregistrer le compte employé</button>
        </div>
        <button type="button" class="col-back-grey-btn btn" style="justify-self:right;"
            id="close-employee-popup">Annuler</button>
    </form>

</div>