<?php

namespace App\Application\Address\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;

readonly class FindAddressesByCityPaginateQueryHandler implements QueryHandler
{
    public function __construct(public AddressRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindAddressesByCityPaginateQuery $query): array
    {
        return $this->regionRepository->ofCityPaginate($query->getCity(), $query->getPage(), $query->getOffset(), $query->getFilters());
    }
}