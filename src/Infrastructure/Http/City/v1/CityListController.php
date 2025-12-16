<?php

namespace App\Infrastructure\Http\City\v1;

use App\Application\City\Query\FindCitiesByPaginateQuery;
use App\Application\City\Query\FindCitiesByRegionPaginateQuery;
use App\Application\City\Query\FindCityByQuery;
use App\Application\QueryBus;
use App\Application\Region\Query\FindRegionByIdDeactivatedQuery;
use App\Application\Region\Query\FindRegionByIdQuery;
use App\Core\Domain\City\Entity\City;
use App\Core\Domain\Region\Exception\RegionNotFoundException;
use App\Infrastructure\Http\City\v1\Response\Normalizer\CityListNormalizer;
use App\Infrastructure\Http\City\v1\Response\Normalizer\CityPaginateNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;


#[Route('/api/v1/city', name: 'city_')]
class CityListController extends AbstractController
{
    public function __construct(
        public readonly QueryBus               $queryBus,
        public readonly CityPaginateNormalizer $cityPaginateNormalizer,
        public readonly CityListNormalizer     $cityListNormalizer
    )
    {
    }

    #[Route('/paginate', name: 'paginate', methods: 'GET')]
    #[OA\Tag(name: 'city')]
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
                content: new OA\JsonContent(ref: new Model(type: City::class))
            )
        ]
    )]
    public function paginate(#[MapQueryParameter] int $page, #[MapQueryParameter] int $offset): JsonResponse
    {
        $cities = $this->queryBus->handle(new FindCitiesByPaginateQuery($page, $offset, []));

        return $this->json($this->cityPaginateNormalizer->normalize($cities));
    }

    #[Route('/search', name: 'search', methods: 'GET')]
    #[OA\Tag(name: 'country')]
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
                content: new OA\JsonContent(ref: new Model(type: City::class))
            )
        ]
    )]
    public function search(#[MapQueryParameter] string $query): JsonResponse
    {
        $countries = $this->queryBus->handle(new FindCityByQuery($query, []));

        return $this->json($this->cityListNormalizer->normalize($countries));
    }

    #[Route('/find-by-region-id-paginate', name: 'find_by_region_id_paginate', methods: 'GET')]
    #[OA\Tag(name: 'region')]
    #[OA\Parameter(
        name: 'regionId',
        description: 'Region Id(Uuid)',
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
        content: new OA\JsonContent(ref: new Model(type: City::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findByRegionIdPaginate(#[MapQueryParameter] string $regionId, #[MapQueryParameter] int $page, #[MapQueryParameter] int $offset): JsonResponse
    {
        $region = $this->queryBus->handle(new FindRegionByIdQuery(Uuid::fromString($regionId)));

        if (is_null($region)) {
            $region = $this->queryBus->handle(new FindRegionByIdDeactivatedQuery(Uuid::fromString($regionId)));

            if (is_null($region)) {
                throw new RegionNotFoundException(sprintf('Region with id: %s not found', $regionId));
            }
        }

        $cities = $this->queryBus->handle(new FindCitiesByRegionPaginateQuery($region, $page, $offset, []));

        if (is_null($cities)) {
            return new JsonResponse(null, 404);
        }

        return $this->json($this->cityPaginateNormalizer->normalize($cities));
    }
}