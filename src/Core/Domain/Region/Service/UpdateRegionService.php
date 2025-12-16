<?php

namespace App\Core\Domain\Region\Service;

use App\Core\Domain\Country\Exception\CountryNotFoundException;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Dto\UpdateRegionDto;
use App\Core\Domain\Region\Exception\RegionNotFoundException;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class UpdateRegionService
{
    public function __construct(
        public RegionRepositoryInterface  $regionRepository,
        public CountryRepositoryInterface $countryRepository
    )
    {
    }

    public function update(Uuid $regionId, UpdateRegionDto $updateRegionDto): void
    {

        $region = $this->regionRepository->ofId($regionId);

        if (is_null($region)) {
            $region = $this->regionRepository->ofIdDeactivated($regionId);

            if (is_null($region)) {
                throw new RegionNotFoundException(sprintf('Region with ID: %s not found', $regionId->toRfc4122()));
            }
        }

        if (!is_null($updateRegionDto->countryId)) {
            $country = $this->countryRepository->ofId($updateRegionDto->countryId);

            if (is_null($country)) {
                $country = $this->countryRepository->ofIdDeactivated($updateRegionDto->countryId);

                if (is_null($country)) {
                    throw new CountryNotFoundException(sprintf('Country with ID: %s not found', $updateRegionDto->countryId->toRfc4122()));
                }
            }

            $region->changeCountry($country);
        }

        if (!is_null($updateRegionDto->name)) {
            $region->changeName($updateRegionDto->name);
        }

        if (!is_null($updateRegionDto->code)) {
            $region->changeCode($updateRegionDto->code);
        }

        if (!is_null($updateRegionDto->isActive) && !$region->equalsIsActive($updateRegionDto->isActive)) {
            $region->changeIsActive($updateRegionDto->isActive);
        }


        $this->regionRepository->update($region);
    }
}