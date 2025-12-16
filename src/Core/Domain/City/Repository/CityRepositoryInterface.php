<?php

namespace App\Core\Domain\City\Repository;

use App\Core\Domain\City\Entity\City;
use App\Core\Domain\Region\Entity\Region;
use Symfony\Component\Uid\Uuid;

interface CityRepositoryInterface
{
    public function create(City $city): City;

    public function update(City $city): void;

    public function all(): array;

    public function paginate(int $page, int $offset, array $filters = ['isActive' => true]): array;

    public function search(string $query, array $filters = ['isActive' => true]): array;

    public function ofId(Uuid $cityId): ?City;

    public function ofRegionPaginate(Region $region, int $page, int $offset, array $filters = ['isActive' => true]): array;

    public function ofIdDeactivated(Uuid $cityId): ?City;

    public function ofRegionAndTypeAndName(Region $region, string $type, string $name): ?City;

    public function ofRegionAndTypeAndNameDeactivated(Region $region, string $type, string $name): ?City;

}