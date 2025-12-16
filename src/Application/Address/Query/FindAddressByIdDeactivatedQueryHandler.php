<?php

namespace App\Application\Address\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Address\Entity\Address;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;

readonly class FindAddressByIdDeactivatedQueryHandler implements QueryHandler
{
    public function __construct(public AddressRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindAddressByIdDeactivatedQuery $query): ?Address
    {
        return $this->regionRepository->ofIdDeactivated($query->getAddressId());
    }
}