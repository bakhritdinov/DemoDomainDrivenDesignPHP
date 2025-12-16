<?php

namespace App\Core\Domain\City\Service;


use App\Core\Domain\City\Dto\CreateCityDto;
use App\Core\Domain\City\Entity\City;
use App\Core\Domain\City\Exception\CityAlreadyCreatedException;
use App\Core\Domain\City\Exception\CityDeactivatedException;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Region\Exception\RegionNotFoundException;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;

readonly class CreateCityService
{
    public function __construct(
        public CityRepositoryInterface   $cityRepository,
        public RegionRepositoryInterface $regionRepository
    )
    {
    }

    public function create(CreateCityDto $createCityDto): City
    {
        $region = $this->regionRepository->ofCode($createCityDto->regionCode);

        if (is_null($region)) {
            $region = $this->regionRepository->ofCodeDeactivated($createCityDto->regionCode);
            if (is_null($region)) {
                throw new RegionNotFoundException(
                    sprintf('Region with code: %s not found', $createCityDto->regionCode)
                );
            }
        }

        $city = $this->cityRepository->ofRegionAndTypeAndName($region, $createCityDto->type, $createCityDto->name);
        if (!is_null($city)) {
            return $city;
        }

        $city = $this->cityRepository->ofRegionAndTypeAndNameDeactivated($region, $createCityDto->type, $createCityDto->name);
        if (!is_null($city)) {
            throw new CityDeactivatedException(
                sprintf('City with type %s and name %s deactivated', $createCityDto->type, $createCityDto->name)
            );
        }

        $city = new City($region, $createCityDto->type, $createCityDto->name);

        return $this->cityRepository->create($city);
    }
}