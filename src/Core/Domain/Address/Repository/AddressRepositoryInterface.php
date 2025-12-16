<?php

namespace App\Core\Domain\Address\Repository;

use App\Core\Domain\Address\Entity\Address;
use App\Core\Domain\City\Entity\City;
use Symfony\Component\Uid\Uuid;

interface AddressRepositoryInterface
{
    public function create(Address $address): Address;

    public function update(Address $address): void;

    public function all(): array;

    public function paginate(int $page, int $offset, array $filters = ['isActive' => true]): array;

    public function search(string $query, array $filters = ['isActive' => true]): array;

    public function ofId(Uuid $addressId): ?Address;

    public function ofIdDeactivated(Uuid $addressId): ?Address;

    public function ofCityPaginate(City $city, int $page, int $offset, array $filters = ['isActive' => true]): array;

    public function ofAddress(string $address): ?Address;

    public function ofAddressDeactivated(string $address): ?Address;

}