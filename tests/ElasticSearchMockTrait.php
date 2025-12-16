<?php

namespace App\Tests;

use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\Paginator\FantaPaginatorAdapter;
use FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface;
use FOS\ElasticaBundle\Paginator\PartialResultsInterface;
use FOS\ElasticaBundle\Persister\ObjectPersister;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\MockObject\MockObject;

trait ElasticSearchMockTrait
{
    public function getMockFinder(array $records): PaginatedFinderInterface|MockObject
    {
        $finderMock = $this->createMock(PaginatedFinderInterface::class);
        $finderMock
            ->method('find')
            ->willReturn($records);

        return $finderMock;
    }

    public function getMockPaginate(array $records): PaginatedFinderInterface
    {
        $partialResultsMock = $this->createMock(PartialResultsInterface::class);
        $partialResultsMock
            ->method('toArray')
            ->willReturn($records);

        $paginatorAdapterMock = $this->createMock(PaginatorAdapterInterface::class);
        $paginatorAdapterMock
            ->method('getTotalHits')
            ->willReturn(count($records));

        $paginatorAdapterMock
            ->method('getResults')
            ->willReturn($partialResultsMock);

        $adapter = new FantaPaginatorAdapter($paginatorAdapterMock);
        $pagerfanta = new Pagerfanta($adapter);

        $paginateMock = $this->createMock(PaginatedFinderInterface::class);
        $paginateMock
            ->method('findPaginated')
            ->willReturn($pagerfanta);

        return $paginateMock;
    }

    public function getMockPersister(): ObjectPersister|MockObject
    {
        return $this->createMock(ObjectPersister::class);
    }
}