<main class="flex-column flex-center item-center">
    <div class="login-blocks flex-row-wrap">
        <div class="login-block">
            <h1 class="text-green">Se connecter</h1>
            <form action="<?= BASE_URL ?>/login" method="POST" class="login-form">
                <input type="email" id="mail" name="mail" placeholder="Email" class="field-connexion" required>
                <input type="password" name="password" placeholder="Mot de passe" class="field-connexion" required>
                <div class="btn bg-light-green font-size-very-big" style="color: white;">
                    <input type="submit" value="Connexion">
                </div>
            </form>
        </div>
        <div class="login-block" style=" background-color: rgba(150, 201, 171, 0.2);">
            <h1 class="text-green">Créer un compte</h1>
            <form action="<?= BASE_URL ?>/newAccount" method="POST" class="login-form">
                <div>
                    <input type="text" id="pseudo" name="pseudo" placeholder="Pseudo" class="field-connexion" required>
                    <span class="font-size-very-small italic text-green">Votre pseudo
                        sera visible par les
                        autres utilisateurs</span>
                </div>

                <input type="email" id="mail" name="mail" placeholder="Email" class="field-connexion" required>
                <div>
                    <input type="password" id="password" name="password" placeholder="Mot de passe"
                        class="field-connexion" required>
                    <p>
                        ✔ 8 caractères minimum <br>
                        ✔ 1 majuscule et 1 minuscule minimum<br>
                        ✔ 1 chiffre minimum<br>
                        ✔ 1 caractère spécial minimum
                    </p>
                </div>
                <div class="btn bg-light-green font-size-very-big" style="color: white;">
                    <input type="submit" value="Créer le compte">
                </div>
            </form>
        </div>
    </div>
</main>