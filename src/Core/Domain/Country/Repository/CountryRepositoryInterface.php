<?php

namespace App\Core\Domain\Country\Repository;

use App\Core\Domain\Country\Entity\Country;
use Symfony\Component\Uid\Uuid;

interface CountryRepositoryInterface
{
    public function create(Country $country): Country;

    public function update(Country $country): void;

    public function all(): array;

    public function paginate(int $page, int $offset, array $filters = ['isActive' => true]): array;

    public function search(string $query, array $filters = ['isActive' => true]): array;

    public function ofId(Uuid $countryId): ?Country;

    public function ofIdDeactivated(Uuid $countryId): ?Country;

    public function ofNumericCode(int $numericCode): ?Country;

    public function ofNumericCodeDeactivated(int $numericCode): ?Country;

    public function ofAlpha2(string $alpha2): ?Country;

    public function ofAlpha2Deactivated(string $alpha2): ?Country;

    public function ofAlpha3(string $alpha3): ?Country;

    public function ofAlpha3Deactivated(string $alpha3): ?Country;

    public function ofName(string $name): ?Country;

    public function ofNameDeactivated(string $name): ?Country;
}