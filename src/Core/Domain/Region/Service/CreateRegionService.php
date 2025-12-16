<?php

namespace App\Core\Domain\Region\Service;

use App\Core\Domain\Country\Exception\CountryNotFoundException;
use App\Core\Domain\Country\Repository\CountryRepositoryInterface;
use App\Core\Domain\Region\Dto\CreateRegionDto;
use App\Core\Domain\Region\Entity\Region;
use App\Core\Domain\Region\Exception\RegionAlreadyCreatedException;
use App\Core\Domain\Region\Exception\RegionDeactivatedException;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;

readonly class CreateRegionService
{
    public function __construct(
        public RegionRepositoryInterface  $repositoryRegion,
        public CountryRepositoryInterface $repositoryCountry
    )
    {
    }

    public function create(CreateRegionDto $createRegionDto): Region
    {
        $country = $this->repositoryCountry->ofAlpha2($createRegionDto->countryAlpha2);

        if (is_null($country)) {
            $country = $this->repositoryCountry->ofAlpha2Deactivated($createRegionDto->countryAlpha2);

            if (is_null($country)) {
                throw new CountryNotFoundException(sprintf('Country with alpha2 %s not found', $createRegionDto->countryAlpha2));
            }
        }

        $region = $this->repositoryRegion->ofCode($createRegionDto->code);
        if (!is_null($region)) {
            return $region;
        }

        $region = $this->repositoryRegion->ofCodeDeactivated($createRegionDto->code);
        if (!is_null($region)) {
            throw new RegionDeactivatedException(sprintf('Region with code %s deactivated', $createRegionDto->code));
        }


        $region = $this->repositoryRegion->ofName($createRegionDto->name);
        if (!is_null($region)) {
            return $region;
        }

        $region = $this->repositoryRegion->ofNameDeactivated($createRegionDto->name);
        if (!is_null($region)) {
            throw new RegionDeactivatedException(sprintf('Region with name %s deactivated', $createRegionDto->name));
        }

        $region = new Region($country, $createRegionDto->name, $createRegionDto->code);

        return $this->repositoryRegion->create($region);
    }
}