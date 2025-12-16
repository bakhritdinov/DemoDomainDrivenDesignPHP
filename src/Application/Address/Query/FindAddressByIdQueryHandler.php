<?php

namespace App\Application\Address\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Address\Entity\Address;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;

readonly class FindAddressByIdQueryHandler implements QueryHandler
{
    public function __construct(public AddressRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindAddressByIdQuery $query): ?Address
    {
        return $this->regionRepository->ofId($query->getAddressId());
    }
}