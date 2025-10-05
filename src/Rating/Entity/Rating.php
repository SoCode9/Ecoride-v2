<?php

namespace App\Rating\Entity;

final class Rating
{
    public function __construct(
        private int $id,
        private string $userId,
        private string $driverid,
        private float $rating,
        private string $description,
        private string $status,
        private string $createdAt
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
    public function getDriverid(): string
    {
        return $this->driverid;
    }
    public function getRating(): float
    {
        return $this->rating;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
