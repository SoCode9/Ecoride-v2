<?php

namespace App\User\Entity;

class User
{
    public function __construct(
        private string $id,
        private string $pseudo,
        private string $mail,
        private string $password,
        private ?int $credit,
        private ?string $photo,
        private int $idRole,
        private bool $isActivated,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    public function getMail(): string
    {
        return $this->mail;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCredit(): int|null
    {
        return $this->credit;
    }

    public function getPhoto(): string|null
    {
        return $this->photo;
    }

    public function getIdRole(): int
    {
        return $this->idRole;
    }
    public function IsActivated(): bool
    {
        return $this->isActivated;
    }
}
