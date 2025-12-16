<?php

namespace App\Application\Address\Query;

use App\Application\QueryHandler;
use App\Core\Domain\Address\Repository\AddressRepositoryInterface;

readonly class FindAddressesByPaginateQueryHandler implements QueryHandler
{
    public function __construct(public AddressRepositoryInterface $regionRepository)
    {
    }

    public function __invoke(FindAddressesByPaginateQuery $query): array
    {
        return $this->regionRepository->paginate($query->getPage(), $query->getOffset(), $query->getFilters());
    }
}