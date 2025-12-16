<?php

namespace App\Core\Domain\Region\Repository;

use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Region\Entity\Region;
use Symfony\Component\Uid\Uuid;

interface RegionRepositoryInterface
{
    public function create(Region $region): Region;

    public function update(Region $region): void;

    public function all(): array;

    public function paginate(int $page, int $offset, array $filters = ['isActive' => true]): array;

    public function search(string $query, array $filters = ['isActive' => true]): array;

    public function ofId(Uuid $regionId): ?Region;

    public function ofCountryPaginate(Country $country, int $page, int $offset, array $filters = ['isActive' => true]): array;

    public function ofIdDeactivated(Uuid $regionId): ?Region;

    public function ofCode(string $code): ?Region;

    public function ofCodeDeactivated(string $code): ?Region;

    public function ofName(string $name): ?Region;

    public function ofNameDeactivated(string $name): ?Region;
}