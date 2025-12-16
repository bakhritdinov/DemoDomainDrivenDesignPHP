<?php

namespace App\Application\Address\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;

readonly class FindAddressByQueryHandler implements QueryHandler
{
    public function __construct(public AddressRepositoryInterface $addressRepository)
    {
    }

    public function __invoke(FindAddressByQuery $query): array
    {
        return $this->addressRepository->search($query->getQuery(), $query->getFilters());
    }
}