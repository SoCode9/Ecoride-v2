<h2 class="text-green text-bold">Formulaire de contact</h2>
<form class="block-column-g20" action="<?= BASE_URL ?>/contact/send" method="post">
    <div>
        <label for="firstname">Votre prénom : </label>
        <input type="text" id="firstname" name="firstname" required>
    </div>

    <div>
        <label for="lastname">Votre nom : </label>
        <input type="text" id="lastname" name="lastname" required>
    </div>

    <div>
        <label for="email">Votre email : </label>
        <input type="email" id="email" name="email" required>
    </div>

    <div>
        <label for="phone">Votre téléphone : </label>
        <input type="tel" id="phone" name="phone" required>
    </div>

    <div>
        <label for="message">Votre message : </label>
        <textarea id="message" name="message" rows="15" required></textarea>
    </div>

    <button type="submit" class="btn bg-light-green">Envoyer</button>
</form>