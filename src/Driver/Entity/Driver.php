<?php

namespace App\Driver\Entity;

use App\User\Entity\User;

final class Driver extends User
{
    public function __construct(
        //Champs pour Driver
        private ?bool $food,
        private ?bool $music,
        private ?bool $pets,
        private ?bool $smoker,
        private ?bool $speaker,
        private ?array $otherPref,
        // Champs pour User
        string $id,
        string $pseudo,
        string $mail,
        string $password,
        ?int $credit,
        ?string $photo,
        int $idRole,
        bool $isActivated

    ) {
        parent::__construct($id, $pseudo, $mail, $password, $credit, $photo, $idRole, $isActivated);
    }

    public function getFood(): ?bool
    {
        return $this->food;
    }

    public function getMusic(): ?bool
    {
        return $this->music;
    }
    public function getPets(): ?bool
    {
        return $this->pets;
    }
    public function getSmoker(): ?bool
    {
        return $this->smoker;
    }
    public function getSpeaker(): ?bool
    {
        return $this->speaker;
    }
    public function getOtherPref(): ?array
    {
        return $this->otherPref;
    }
}
