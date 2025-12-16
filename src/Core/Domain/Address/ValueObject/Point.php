<?php

namespace App\Core\Domain\Address\ValueObject;

readonly class Point
{
    public function __construct(
        private float $latitude,
        private float $longitude,
    )
    {
    }

    /**
     * Широта
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * Долгота
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }
}