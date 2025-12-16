<?php

namespace App\Infrastructure\Http\Region\v1;

use App\Application\Country\Query\FindCountryByIdQuery;
use App\Application\QueryBus;
use App\Application\Region\Query\FindRegionByQuery;
use App\Application\Region\Query\FindRegionsByCountryPaginateQuery;
use App\Application\Region\Query\FindRegionsByPaginateQuery;
use App\Core\Domain\Country\Exception\CountryNotFoundException;
use App\Core\Domain\Region\Entity\Region;
use App\Infrastructure\Http\Region\v1\Response\Normalizer\RegionListNormalizer;
use App\Infrastructure\Http\Region\v1\Response\Normalizer\RegionPaginateNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/v1/region', name: 'region_')]
class RegionListController extends AbstractController
{
    public function __construct(
        public readonly QueryBus                 $queryBus,
        public readonly RegionPaginateNormalizer $regionPaginateNormalizer,
        public readonly RegionListNormalizer     $regionListNormalizer

    )
    {
    }

    #[Route('/paginate', name: 'paginate', methods: 'GET')]
    #[OA\Tag(name: 'region')]
    #[OA\Parameter(
        name: 'page',
        description: 'Specify which page you want to receive. Pages start at 1',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'offset',
        description: 'Specify the limit, how many results you want to get on one page',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Get(
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful response',
                content: new OA\JsonContent(ref: new Model(type: Region::class))
            )
        ]
    )]
    public function paginate(#[MapQueryParameter] int $page, #[MapQueryParameter] int $offset): JsonResponse
    {
        $regions = $this->queryBus->handle(new FindRegionsByPaginateQuery($page, $offset, []));

        return $this->json($this->regionPaginateNormalizer->normalize($regions));
    }

    #[Route('/search', name: 'search', methods: 'GET')]
    #[OA\Tag(name: 'region')]
    #[OA\Parameter(
        name: 'query',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Get(
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful response',
                content: new OA\JsonContent(ref: new Model(type: Region::class))
            )
        ]
    )]
    public function search(#[MapQueryParameter] string $query): JsonResponse
    {
        $countries = $this->queryBus->handle(new FindRegionByQuery($query, []));

        return $this->json($this->regionListNormalizer->normalize($countries));
    }

    #[Route('/find-by-country-id-paginate', name: 'find_by_country_id_paginate', methods: 'GET')]
    #[OA\Tag(name: 'region')]
    #[OA\Parameter(
        name: 'countryId',
        description: 'Country Id(Uuid)',
        in: 'query',
        schema: new OA\Schema(type: 'string')
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'Specify which page you want to receive. Pages start at 1',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Parameter(
        name: 'offset',
        description: 'Specify the limit, how many results you want to get on one page',
        in: 'query',
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Region::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findByCountryId(#[MapQueryParameter] string $countryId, #[MapQueryParameter] int $page, #[MapQueryParameter] int $offset): JsonResponse
    {
        $country = $this->queryBus->handle(new FindCountryByIdQuery(Uuid::fromString($countryId)));

        if (is_null($country)) {
            $country = $this->queryBus->handle(new FindCountryByIdQuery(Uuid::fromString($countryId)));

            if (is_null($country)) {
                throw new CountryNotFoundException(sprintf('Country with id: %s not found', $countryId));
            }
        }

        $regions = $this->queryBus->handle(new FindRegionsByCountryPaginateQuery($country, $page, $offset, []));

        if (is_null($regions)) {
            return new JsonResponse(null, 404);
        }

        return $this->json($this->regionPaginateNormalizer->normalize($regions));
    }

}