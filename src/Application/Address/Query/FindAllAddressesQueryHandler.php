<?php

namespace App\Application\Address\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;

readonly class FindAllAddressesQueryHandler implements QueryHandler
{
    public function __construct(public AddressRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindAllAddressesQuery $query): array
    {
        return $this->regionRepository->all();
    }
}