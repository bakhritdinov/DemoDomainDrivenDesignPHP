<?php

namespace App\Application\Address\Command;

use App\Application\Address\Query\External\FindExternalAddressQuery;
use App\Application\City\Command\CreateCityCommand;
use App\Application\City\Query\FindCitiesByRegionPaginateQuery;
use App\Application\City\Query\FindCityByRegionAndTypeAndNameQuery;
use App\Application\CommandBus;
use App\Application\CommandHandler;
use App\Application\Country\Query\FindCountryByAlpha2Query;
use App\Application\QueryBus;
use App\Application\Region\Command\CreateRegionCommand;
use App\Application\Region\Query\FindRegionByCodeQuery;
use App\Core\Domain\Address\Entity\Address;
use App\Core\Domain\Address\Exception\AddressNotFoundException;
use App\Core\Domain\Address\Service\CreateAddressService;
use App\Core\Domain\City\Dto\CreateCityDto;
use App\Core\Domain\Country\Exception\CountryNotAllowedException;
use App\Core\Domain\Region\Dto\CreateRegionDto;

readonly class CreateAddressCommandHandler implements CommandHandler
{
    public function __construct(
        public CreateAddressService $createAddressService,
        public CommandBus           $commandBus,
        public QueryBus             $queryBus
    )
    {
    }

    public function __invoke(CreateAddressCommand $command): Address
    {
        $dto = $this->queryBus->handle(new FindExternalAddressQuery($command->getAddress()));
        if (is_null($dto)) {
            throw new AddressNotFoundException(sprintf(
                'The external source did not find the transmitted address %s',
                $command->getAddress()
            ));
        }

        $country = $this->queryBus->handle(new FindCountryByAlpha2Query($dto->alpha2));
        if (is_null($country)) {
            throw new CountryNotAllowedException(sprintf('Country %s not allowed', $dto->country));
        }

        $region = $this->queryBus->handle(new FindRegionByCodeQuery($dto->regionCode));
        if (is_null($region)) {
            $this->commandBus->dispatch(new CreateRegionCommand(new CreateRegionDto($dto->alpha2, $dto->region, $dto->regionCode)));
            $this->commandBus->dispatch(new CreateCityCommand(new CreateCityDto($dto->regionCode, $dto->cityType, $dto->city)));
        } else {
            $city = $this->queryBus->handle(new FindCityByRegionAndTypeAndNameQuery($region, $dto->cityType, $dto->city));
            if (is_null($city)) {
                $this->commandBus->dispatch(new CreateCityCommand(new CreateCityDto($dto->regionCode, $dto->cityType, $dto->city)));
            }

        }

        return $this->createAddressService->create($dto);
    }
}