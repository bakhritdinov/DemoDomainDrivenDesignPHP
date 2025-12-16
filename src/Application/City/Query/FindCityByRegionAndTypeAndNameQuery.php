<?php

namespace App\Application\City\Query;

use App\Application\Query;
use App\Core\Domain\Region\Entity\Region;

readonly class FindCityByRegionAndTypeAndNameQuery implements Query
{
    public function __construct(private Region $region, private string $type, private string $name)
    {
    }

    public function getRegion(): Region
    {
        return $this->region;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }
}