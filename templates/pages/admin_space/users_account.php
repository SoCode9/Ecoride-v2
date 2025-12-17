<nav class="tabs" style="justify-content: space-between;">
    <button class="btn main-tab-btn" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/espace-admin/employes'">Comptes employés</button>
    <button class="btn main-tab-btn active" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/espace-admin/utilisateurs'">Comptes utilisateurs</button>
    <button class="btn main-tab-btn" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/espace-admin/statistiques'">Statistiques</button>
</nav>

<section>
    <div class="main-header m-tb-20">
        <h2 class="text-green text-bold">Gérer les comptes des utilisateurs
            (<?= count($passengersList) + count($driversList) + count($passengersAndDriversList) ?>)
        </h2>
    </div>

    <h3 style="color: black;">Passagers</h3>
    <div class="half-separation m-tb-20 gap-12">
        <?php
        foreach ($passengersList as $user): ?>
            <div class="block-light-grey">
                <div class="flex-row flex-between">
                    <div class="flex-column">
                        <span class="text-bold">
                            <?= htmlspecialchars($user['pseudo']) ?>
                        </span>
                        <span class="italic">
                            <?= htmlspecialchars($user['mail']) ?>
                        </span>
                    </div>
                    <?php if ($user['is_activated'] === 1): ?>
                        <button class="btn bg-light-red suspend-user" data-id="<?= (string) $user['id'] ?>">Suspendre</button>
                    <?php elseif ($user['is_activated'] === 0): ?>
                        <button class="btn bg-light-green reactivate-user" data-id="<?= (string) $user['id'] ?>">Réactiver</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h3 style="color: black;">Chauffeurs</h3>
    <div class="half-separation m-tb-20 gap-12">
        <?php
        foreach ($driversList as $user): ?>
            <div class="block-light-grey">
                <div class="flex-row flex-between">
                    <div class="flex-column">
                        <span class="text-bold">
                            <?= htmlspecialchars($user['pseudo']) ?>
                        </span>
                        <span class="italic">
                            <?= htmlspecialchars($user['mail']) ?>
                        </span>
                    </div>
                    <?php if ($user['is_activated'] === 1): ?>
                        <button class="btn bg-light-red suspend-user" data-id="<?= (string) $user['id'] ?>">Suspendre</button>
                    <?php elseif ($user['is_activated'] === 0): ?>
                        <button class="btn bg-light-green reactivate-user" data-id="<?= (string) $user['id'] ?>">Réactiver</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h3 style="color: black;">Passagers-Chauffeurs</h3>
    <div class="half-separation m-tb-20 gap-12">
        <?php
        foreach ($passengersAndDriversList as $user): ?>
            <div class="block-light-grey">
                <div class="flex-row flex-between">
                    <div class="flex-column">
                        <span class="text-bold">
                            <?= htmlspecialchars($user['pseudo']) ?>
                        </span>
                        <span class="italic">
                            <?= htmlspecialchars($user['mail']) ?>
                        </span>
                    </div>
                    <?php if ($user['is_activated'] === 1): ?>
                        <button class="btn bg-light-red suspend-user" data-id="<?= (string) $user['id'] ?>">Suspendre</button>
                    <?php elseif ($user['is_activated'] === 0): ?>
                        <button class="btn bg-light-green reactivate-user" data-id="<?= (string) $user['id'] ?>">Réactiver</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script src="<?= ASSETS_PATH ?>js/admin_space.js" defer></script>