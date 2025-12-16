<?php

namespace App\Core\Domain\City\Service;

use App\Core\Domain\City\Dto\UpdateCityDto;
use App\Core\Domain\City\Exception\CityNotFoundException;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Region\Exception\RegionNotFoundException;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class UpdateCityService
{
    public function __construct(
        public CityRepositoryInterface   $repository,
        public RegionRepositoryInterface $regionRepository
    )
    {
    }

    public function update(Uuid $cityId, UpdateCityDto $updateCityDto): void
    {
        $city = $this->repository->ofId($cityId);

        if (is_null($city)) {
            $city = $this->repository->ofIdDeactivated($cityId);

            if (is_null($city)) {
                throw new CityNotFoundException(sprintf('City with ID: %s not found', $cityId->toRfc4122()));
            }
        }

        if (!is_null($updateCityDto->regionId)) {

            $region = $this->regionRepository->ofId($updateCityDto->regionId);

            if (is_null($region)) {
                $region = $this->regionRepository->ofIdDeactivated($updateCityDto->regionId);
                if (is_null($region)) {
                    throw new RegionNotFoundException(
                        sprintf('Region with id: %s not found', $updateCityDto->regionId)
                    );
                }
            }
            $city->changeRegion($region);
        }

        if (!is_null($updateCityDto->isActive) && !$city->equalsIsActive($updateCityDto->isActive)) {
            $city->changeIsActive($updateCityDto->isActive);
        }

        if (!is_null($updateCityDto->name)) {
            $city->changeName($updateCityDto->name);
        }

        if (!is_null($updateCityDto->type)) {
            $city->changeType($updateCityDto->type);
        }

        $this->repository->update($city);
    }
}