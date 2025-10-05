<?php

namespace App\User\Service;

use App\Driver\Repository\DriverRepository;
use App\Driver\Service\DriverService;
use App\User\Entity\User;


use App\User\Repository\UserRepository;

use App\Utils\Formatting\OtherFormatter;

final class UserService
{
    public function __construct(private UserRepository $repo) {}

    public function displayProfil(User $user)
    {
        $userId = $user->getId();
        $photo = OtherFormatter::displayPhoto($user->getPhoto());

        if (in_array($user->getIdRole(), [2, 3])) {
            $driRepo = new DriverRepository($this->repo);
            $driSer = new DriverService($driRepo);

            $avg     = $driSer->getAverageRatings($userId);
            $rating  = $avg !== null
                ? '<img src="' . ASSETS_PATH . '/icons/EtoileJaune.png" class="img-width-20" alt="Icône étoile"> ' . number_format((float)$avg, 1, ',', '')
                : '<span class="italic">0 avis</span>';
        }



        return [
            'photo' => $photo,
            'rating' => $rating,
        ];
    }
}
