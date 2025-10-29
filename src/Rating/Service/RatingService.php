<?php

namespace App\Rating\Service;

use App\Rating\Repository\RatingRepository;
use App\User\Repository\UserRepository;

use App\Routing\Router;
use App\Utils\Formatting\DateFormatter;
use App\Utils\Formatting\OtherFormatter;

final class RatingService
{
    public function __construct(private RatingRepository $repo, private UserRepository $userRepo) {}

    /**
     * Formats driver ratings for display.
     * Retrieves each user's information (pseudo, photo),
     * formats the creation date, and builds the rating string with a star icon.
     *
     * @param array $ratings List of Rating objects
     * @return array Formatted associative array ready for display
     */
    public function formatRatings(array $ratings): array
    {
        $ratingsFormatted = [];
        foreach ($ratings as $rating) {
            $userId = $rating->getUserId();
            $dataUser = $this->userRepo->findById($rating->getUserId());
            $ratingNote  = $rating->getRating()
                ? '<img src="' . ASSETS_PATH . '/icons/EtoileJaune.png" class="img-width-20" alt="Icône étoile"> ' . number_format((float)$rating->getRating(), 1, ',', '')
                : '<span class="italic">0 avis</span>';
            $ratingsFormatted[$userId] = [
                'userPseudo' => $dataUser->getPseudo(),
                'userPhoto' => OtherFormatter::displayPhoto($dataUser->getPhoto()),
                'createdAt'   => DateFormatter::monthYear($rating->getCreatedAt()),
                'rating' => $ratingNote
            ];
        }

        return $ratingsFormatted;
    }
}
