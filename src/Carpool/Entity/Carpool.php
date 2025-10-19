<?php

namespace App\Carpool\Entity;

final class Carpool
{
    public function __construct(
        private string $id,
        private string $driverId,
        private ?string $date,
        private ?string $departureCity,
        private ?string $arrivalCity,
        private ?string $departureTime,
        private ?string $arrivalTime,
        private ?int $price,
        private ?int $carId,
        private ?int $availableSeats,
        private ?string $description,
        private ?string $status
    ) {}

    public function getIdCarpool()
    {
        return $this->id;
    }

    public function getIdDriver()
    {
        return $this->driverId;
    }

    public function getCarId()
    {
        return $this->carId;
    }
    public function getDate(): ?string
    {
        return $this->date;
    }
    public function getDepartureCity()
    {
        return $this->departureCity;
    }

    public function getArrivalCity()
    {
        return $this->arrivalCity;
    }

    public function getDepartureTime()
    {
        return $this->departureTime;
    }

    public function getArrivalTime()
    {
        return $this->arrivalTime;
    }
    public function getPrice()
    {
        return $this->price;
    }

    public function getAvailableSeats()
    {
        return $this->availableSeats;
    }
    public function getDescription(): string|null
    {
        return $this->description;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
