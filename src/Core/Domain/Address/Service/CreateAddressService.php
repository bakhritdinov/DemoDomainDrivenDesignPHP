<?php

namespace App\Core\Domain\Address\Service;

use App\Core\Domain\Address\Dto\AddressDto;
use App\Core\Domain\Address\Entity\Address;
use App\Core\Domain\Address\Exception\AddressAlreadyCreatedException;
use App\Core\Domain\Address\Exception\AddressDeactivatedException;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;
use App\Core\Domain\Address\ValueObject\Point;
use App\Core\Domain\City\Exception\CityNotFoundException;
use App\Core\Domain\City\Repository\CityRepositoryInterface;
use App\Core\Domain\Region\Exception\RegionNotFoundException;
use App\Core\Domain\Region\Repository\RegionRepositoryInterface;

readonly class CreateAddressService
{
    public function __construct(
        public AddressRepositoryInterface $repositoryAddress,
        public RegionRepositoryInterface  $regionRepository,
        public CityRepositoryInterface    $cityRepository
    )
    {
    }

    public function create(AddressDto $dto): Address
    {
        $region = $this->regionRepository->ofCode($dto->regionCode);

        if (is_null($region)) {
            $region = $this->regionRepository->ofCodeDeactivated($dto->regionCode);
            if (is_null($region)) {
                throw new RegionNotFoundException(
                    sprintf('Region with code: %s not found', $dto->regionCode)
                );
            }
        }

        $city = $this->cityRepository->ofRegionAndTypeAndName($region, $dto->cityType, $dto->city);

        if (is_null($city)) {
            $city = $this->cityRepository->ofRegionAndTypeAndNameDeactivated($region, $dto->cityType, $dto->city);

            if (is_null($city)) {
                throw new CityNotFoundException(sprintf('City with type %s and name %s not found', $dto->cityType, $dto->city));

            }
        }

        $address = $this->repositoryAddress->ofAddress($dto->address);
        if (!is_null($address)) {
            return $address;
        }

        $address = $this->repositoryAddress->ofAddressDeactivated($dto->address);
        if (!is_null($address)) {
            throw new AddressDeactivatedException(sprintf('Address %s deactivated', $dto->address));
        }

        $point = new Point($dto->latitude, $dto->longitude);
        $address = new Address(
            $city,
            $dto->address,
            $dto->postalCode,
            $dto->street ?? $dto->settlement,
            $dto->house,
            $dto->flat,
            $dto->entrance,
            $dto->floor,
            $point
        );

        return $this->repositoryAddress->create($address);
    }
}