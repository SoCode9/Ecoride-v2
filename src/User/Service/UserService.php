<?php

namespace App\User\Service;

use App\Database\DbConnection;
use Exception;
use Throwable;
use InvalidArgumentException;

use App\Driver\Repository\DriverRepository;
use App\Driver\Service\DriverService;
use App\User\Entity\User;
use App\User\Repository\UserRepository;
use App\Carpool\Repository\CarpoolRepository;
use App\Rating\Repository\RatingRepository;
use App\Reservation\Repository\ReservationRepository;
use App\Utils\Formatting\OtherFormatter;

final class UserService
{
    public function __construct(private UserRepository $repo)
    {
    }

    public function displayProfil(User $user)
    {
        $userId = $user->getId();
        $photo = OtherFormatter::displayPhoto($user->getPhoto());

        if (in_array($this->repo->getRole($userId), [2, 3])) {
            $driRepo = new DriverRepository($this->repo);
            $driSer = new DriverService($driRepo);

            $avg = $driSer->getAverageRatings($userId);
            $rating = $avg !== null
                ? '<img src="' . ASSETS_PATH . '/icons/EtoileJaune.png" class="img-width-20" alt="Icône étoile"> ' . number_format((float) $avg, 1, ',', '')
                : '<span class="italic">0 avis</span>';
        }

        return [
            'photo' => $photo,
            'rating' => $rating ?? null,
        ];
    }

    /**
     * Processes and saves a new profile photo for the user.
     * - Validates file size (max 8 MB) and allowed extensions (jpg, jpeg, gif, png)
     * - Generates a unique filename prefixed with the user ID
     * - Deletes any previous photo for this user (files starting with "{$userId}_")
     * - Moves the uploaded file to PHOTOS_DIR
     *
     * @param string $userId   The user's UUID used to namespace the file
     * @param array  $newPhoto The uploaded file array (e.g. from $_FILES['photo'])
     * @throws \Exception If the file is too large, has an invalid extension, or cannot be saved
     * @return string The generated filename to store in the database
     */
    public function editPhoto(string $userId, array $newPhoto): string
    {

        $file = $newPhoto;
        $maxFileSize = 8000000; // 8 MB
        $allowedExtensions = ['jpg', 'jpeg', 'gif', 'png'];

        // Check if the file size is acceptable
        if ($file['size'] > $maxFileSize) {
            throw new Exception("Le fichier est trop volumineux. Taille maximale autorisée : 8 Mo");
        }

        // Retrieve and validate the file extension
        $fileInfo = pathinfo($file['name']);
        $extension = strtolower($fileInfo['extension'] ?? '');

        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("Seules les extensions suivantes sont autorisées : .jpg, .jpeg, .gif, .png");
        }

        // Generate a unique filename to avoid conflicts
        $uniqueName = uniqid($userId . '_', true) . '.' . $extension;
        $uploadDir = PHOTOS_DIR . '/';
        $destination = $uploadDir . $uniqueName;

        // Delete the user's old photos from the folder
        $photoFolder = scandir($uploadDir);
        foreach ($photoFolder as $photo) {
            $photoSearched = strstr($photo, $userId . "_");
            if ($photoSearched !== false) {
                unlink($uploadDir . $photoSearched);
            }
        }

        // Move the uploaded file to the destination folder
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Erreur lors de l'enregistrement du fichier");
        }

        return $uniqueName;
    }

    public function checkResolveBadComment(int $reservationId)
    {
        if ($reservationId <= 0) {
            throw new InvalidArgumentException("ID d'avis invalide");
        }

        $pdo = DbConnection::getPdo();
        $ratingRepo = new RatingRepository();
        $resRepo = new ReservationRepository();

        $carpoolRepo = new CarpoolRepository();
        try {
            $pdo->beginTransaction();

            // 1) marquer le commentaire litigieux comme validé
            $ratingRepo->markBadCommentAsValidated($reservationId);

            // 2) rendre les crédits au conducteur (creditSpent)
            $driverId = $resRepo->getDriverIdFromReservation($reservationId);
            $creditSpent = $resRepo->getCreditSpent($reservationId);
            $this->repo->setCredit($driverId, $creditSpent);

            // 3) si plus aucune réservation en attente => covoiturage "ended" + malus -2
            $carpoolId = $resRepo->getCarpoolIdFromReservation($reservationId);
            $pending = $resRepo->getReservationsNotValidatedOfACarpool($carpoolId);

            if (empty($pending)) {
                $carpoolRepo->setCarpoolStatus('ended', $carpoolId);
                $this->repo->setCredit($driverId, -2);
            }

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
