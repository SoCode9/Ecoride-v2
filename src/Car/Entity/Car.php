<?php

namespace App\Car\Entity;

use DateTime;

final class Car
{
    public function __construct(
        private int $id,
        private int $brandId,
        private string $brand,
        private string $driverid,
        private string $licencePlate,
        private string $firstRegistrationDate,
        private int $seatsOffered,
        private string $model,
        private string $color,
        private bool $isElectric,
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getBrandId(): int
    {
        return $this->brandId;
    }
    public function getBrand(): string
    {
        return $this->brand;
    }
    public function getDriverid(): string
    {
        return $this->driverid;
    }
    public function getLicencePlate(): string
    {
        return $this->licencePlate;
    }
    public function getFirstRegistrationDate(): string|null
    {
        return $this->firstRegistrationDate;
    }

    public function getSeatsOffered(): int
    {
        return $this->seatsOffered;
    }
    public function getModel(): string
    {
        return $this->model;
    }

    public function getColor(): string
    {
        return $this->color;
    }
    public function isElectric(): bool
    {
        return $this->isElectric;
    }
}
