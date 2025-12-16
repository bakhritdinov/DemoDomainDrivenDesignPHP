<?php

namespace App\Application\Address\Query;

use App\Application\Address\Query\External\FindExternalAddressQuery;
use App\Application\QueryBus;
use App\Application\QueryHandler;
use App\Core\Domain\Address\Entity\Address;
use App\Core\Domain\Address\Exception\AddressNotFoundException;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;

readonly class FindAddressByAddressDeactivatedQueryHandler implements QueryHandler
{
    public function __construct(
        public AddressRepositoryInterface $addressRepository,
        public QueryBus                   $queryBus
    )
    {
    }

    public function __invoke(FindAddressByAddressDeactivatedQuery $query): ?Address
    {
        $address = $this->addressRepository->ofAddressDeactivated($query->getAddress());
        if (is_null($address)) {

            if (is_null($address)) {
                $dto = $this->queryBus->handle(new FindExternalAddressQuery($query->getAddress()));
                if (is_null($dto)) {
                    throw new AddressNotFoundException(sprintf(
                        'The external source did not find the transmitted address %s',
                        $query->getAddress()
                    ));
                }
            }
        }

        return $address;
    }
}