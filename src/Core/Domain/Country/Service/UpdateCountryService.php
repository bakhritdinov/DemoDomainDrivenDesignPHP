<?php

namespace App\Core\Domain\Country\Service;

use App\Core\Domain\Country\Dto\UpdateCountryDto;
use App\Core\Domain\Country\Exception\CountryAlreadyCreatedException;
use App\Core\Domain\Country\Exception\CountryDeactivatedException;
use App\Core\Domain\Country\Exception\CountryNotFoundException;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class UpdateCountryService
{
    public function __construct(public CountryRepositoryInterface $repository)
    {
    }

    public function update(Uuid $countryId, UpdateCountryDto $updateCountryDto): void
    {
        if (!is_null($updateCountryDto->name)) {
            $country = $this->repository->ofName($updateCountryDto->name);
            if (!is_null($country)) {
                throw new CountryAlreadyCreatedException(sprintf('Country with name %s already created', $updateCountryDto->name));
            }

            $country = $this->repository->ofNameDeactivated($updateCountryDto->name);
            if (!is_null($country)) {
                throw new CountryDeactivatedException(sprintf('Country with name %s deactivated', $updateCountryDto->name));
            }
        }

        if (!is_null($updateCountryDto->numericCode)) {
            if (strlen((string)$updateCountryDto->numericCode) != 3) {
                throw new \RuntimeException('The numeric code field is required and must be 3 characters long');
            }

            $country = $this->repository->ofNumericCode($updateCountryDto->numericCode);
            if (!is_null($country)) {
                throw new CountryAlreadyCreatedException(sprintf('Country with numeric code %s already created', $updateCountryDto->numericCode));
            }

            $country = $this->repository->ofNumericCodeDeactivated($updateCountryDto->numericCode);
            if (!is_null($country)) {
                throw new CountryDeactivatedException(sprintf('Country with numeric code %s deactivated', $updateCountryDto->numericCode));
            }
        }

        if (!is_null($updateCountryDto->alpha2)) {
            $country = $this->repository->ofAlpha2($updateCountryDto->alpha2);
            if (!is_null($country)) {
                throw new CountryAlreadyCreatedException(sprintf('Country with alpha2 %s already created', $updateCountryDto->alpha2));
            }

            $country = $this->repository->ofAlpha2Deactivated($updateCountryDto->alpha2);
            if (!is_null($country)) {
                throw new CountryDeactivatedException(sprintf('Country with alpha2 %s deactivated', $updateCountryDto->alpha2));
            }
        }

        if (!is_null($updateCountryDto->alpha3)) {
            $country = $this->repository->ofAlpha3($updateCountryDto->alpha3);
            if (!is_null($country)) {
                throw new CountryAlreadyCreatedException(sprintf('Country with alpha3 %s already created', $updateCountryDto->alpha3));
            }

            $country = $this->repository->ofAlpha3Deactivated($updateCountryDto->alpha3);
            if (!is_null($country)) {
                throw new CountryDeactivatedException(sprintf('Country with alpha3 %s deactivated', $updateCountryDto->alpha3));
            }
        }

        $country = $this->repository->ofId($countryId);

        if (is_null($country)) {
            $country = $this->repository->ofIdDeactivated($countryId);

            if (is_null($country)) {
                throw new CountryNotFoundException(sprintf('Country with ID: %s not found', $countryId->toRfc4122()));
            }
        }

        if (!is_null($updateCountryDto->isActive) && !$country->equalsIsActive($updateCountryDto->isActive)) {
            $country->changeIsActive($updateCountryDto->isActive);
        }

        if (!is_null($updateCountryDto->name)) {
            $country->changeName($updateCountryDto->name);
        }

        $this->repository->update($country);
    }
}