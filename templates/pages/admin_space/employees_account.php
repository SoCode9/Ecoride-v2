<nav class="tabs" style="justify-content: space-between;">
    <button class="btn main-tab-btn active" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/espace-admin/employes'">Comptes employés</button>
    <button class="btn main-tab-btn" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/espace-admin/utilisateurs'">Comptes utilisateurs</button>
    <button class="btn main-tab-btn" style="width:100%;"
        onclick="window.location.href='<?= BASE_URL ?>/espace-admin/statistiques'">Statistiques</button>
</nav>

<section>
    <div class="main-header m-tb-20">
        <h2 class="text-green text-bold">Gérer les comptes des employés (<?= count($employeeList) ?>)</h2>
        <a class="btn action-btn" id="newEmployeeBtn">Créer un compte employé</a>
    </div>

    <div class="half-separation m-tb-20 gap-12">
        <?php
        foreach ($employeeList as $employee): ?>
            <div class="block-light-grey">
                <div class="flex-row flex-between">
                    <div class="flex-column">
                        <span class="text-bold">
                            <?= htmlspecialchars($employee['pseudo']) ?>
                        </span>
                        <span class="italic">
                            <?= htmlspecialchars($employee['mail']) ?>
                        </span>
                    </div>
                    <?php if ($employee['is_activated'] === 1): ?>
                        <button class="btn bg-light-red suspend-employee"
                            data-id="<?= (string) $employee['id'] ?>">Suspendre</button>
                    <?php elseif ($employee['is_activated'] === 0): ?>
                        <button class="btn bg-light-green reactivate-employee"
                            data-id="<?= (string) $employee['id'] ?>">Réactiver</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script src="<?= ASSETS_PATH ?>js/admin_space.js" defer></script>