<?php

namespace App\Driver\Service;

use App\Driver\Repository\DriverRepository;

final class DriverService
{
    public function __construct(private DriverRepository $repo) {}

    /**
     * Calculates the average rating for the driver based on validated ratings
     * @param string $driverId The UUID of the user
     * @return float|null The average rating (e.g., 4.2), or null if no rating is available
     */
    public function getAverageRatings($driverId): ?float
    {
        $allInfoRatings = $this->repo->findValidatedRatings($driverId);
        if (empty($allInfoRatings)) {
            return null; // if the driver has no rating
        }
        $average = array_sum(array_column($allInfoRatings, 'rating')) / count($allInfoRatings);
        return round($average, 1);
    }

    /**
     * Formats the display of preferences
     * @param mixed $preference //can be : food, music, pets, smoker or speaker
     * @param mixed $result //if the driver is OK , Not OK or null
     * @return array{image: string, preference: mixed, result: bool, text: string|null}
     */
    public function formatPreference($preference, $result): array|null
    {
        //search the preference
        switch ($preference) {
            case 'pets':
                $imageOK = 'AnimauxOK.png';
                $imageNotOK = 'AnimauxPasOK.png';
                $textOK = "J'aime la compagnie des animaux";
                $textNotOK = "Je préfère ne pas voyager avec des animaux";
                break;
            case 'food':
                $imageOK = 'foodOk.png';
                $imageNotOK = 'foodNotOk.png';
                $textOK = "La nourriture est autorisée dans la voiture";
                $textNotOK = "Pas de nourriture dans la voiture s'il vous plait";
                break;
            case 'music':
                $imageOK = 'MusiqueOk.png';
                $imageNotOK = 'MusiquePasOk.png';
                $textOK = "J'aime conduire en écoutant de la musique";
                $textNotOK = "Je préfère ne pas écouter de musique pendant que je conduis";
                break;
            case 'smoker':
                $imageOK = 'FumerOk.png';
                $imageNotOK = 'FumerPasOk.png';
                $textOK = "La fumée ne me dérange pas";
                $textNotOK = "Je préfère ne pas voyager avec des fumeurs";
                break;
            case 'speaker':
                $imageOK = 'speakOk.png';
                $imageNotOK = 'speakNotOk.png';
                $textOK = "Je discute volontiers avec mes passagers";
                $textNotOK = "Je préfère me concentrer sur la route";
                break;
            default:
                return null;
        }
        //if yes
        if ($result === true) {
            return array(
                'result' => true,
                'preference' => $preference,
                'image' => $imageOK,
                'text' => $textOK
            );
            //if no
        } elseif ($result === false) {
            return array(
                'result' => false,
                'preference' => $preference,
                'image' => $imageNotOK,
                'text' => $textNotOK
            );
        } else {
            return null;
        }
    }

}
