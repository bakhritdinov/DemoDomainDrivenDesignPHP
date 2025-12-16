<?php

namespace App\Core\Domain\Country\Service;

use App\Core\Domain\City\Entity\City;
use App\Core\Domain\Country\Dto\CreateCountryDto;
use App\Core\Domain\Country\Entity\Country;
use App\Core\Domain\Country\Exception\CountryAlreadyCreatedException;
use App\Core\Domain\Country\Exception\CountryDeactivatedException;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;

readonly class CreateCountryService
{
    public function __construct(public CountryRepositoryInterface $repository)
    {
    }

    public function create(CreateCountryDto $createCountryDto): Country
    {
        if (strlen((string)$createCountryDto->numericCode) != 3) {
            throw new \RuntimeException('The numeric code field is required and must be 3 characters long');
        }

        $country = $this->repository->ofName($createCountryDto->name);
        if (!is_null($country)) {
            return $country;
        }

        $country = $this->repository->ofNameDeactivated($createCountryDto->name);
        if (!is_null($country)) {
            throw new CountryDeactivatedException(sprintf('Country with name %s deactivated', $createCountryDto->name));
        }

        $country = $this->repository->ofNumericCode($createCountryDto->numericCode);
        if (!is_null($country)) {
            return $country;
        }

        $country = $this->repository->ofNumericCodeDeactivated($createCountryDto->numericCode);
        if (!is_null($country)) {
            throw new CountryDeactivatedException(sprintf('Country with numeric code %s deactivated', $createCountryDto->numericCode));
        }

        $country = $this->repository->ofAlpha2($createCountryDto->alpha2);
        if (!is_null($country)) {
            return $country;
        }

        $country = $this->repository->ofAlpha2Deactivated($createCountryDto->alpha2);
        if (!is_null($country)) {
            throw new CountryDeactivatedException(sprintf('Country with alpha2 %s deactivated', $createCountryDto->alpha2));
        }

        $country = $this->repository->ofAlpha3($createCountryDto->alpha3);
        if (!is_null($country)) {
            return $country;
        }

        $country = $this->repository->ofAlpha3Deactivated($createCountryDto->alpha3);
        if (!is_null($country)) {
            throw new CountryDeactivatedException(sprintf('Country with alpha3 %s deactivated', $createCountryDto->alpha3));
        }

        $country = new Country($createCountryDto->name, $createCountryDto->numericCode, $createCountryDto->alpha2, $createCountryDto->alpha3);

        if (!is_null($createCountryDto->isActive)) {
            $country->changeIsActive($createCountryDto->isActive);
        }

        return $this->repository->create($country);
    }
}