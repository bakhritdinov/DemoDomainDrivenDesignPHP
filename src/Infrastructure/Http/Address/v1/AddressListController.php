<?php

namespace App\Infrastructure\Http\Address\v1;

use App\Application\Address\Query\FindAddressByQuery;
use App\Application\Address\Query\FindAddressesByCityPaginateQuery;
use App\Application\Address\Query\FindAddressesByPaginateQuery;
use App\Application\City\Query\FindCityByIdDeactivatedQuery;
use App\Application\City\Query\FindCityByIdQuery;
use App\Application\QueryBus;
use App\Core\Domain\Address\Entity\Address;
use App\Core\Domain\Country\Exception\CountryNotFoundException;
use App\Core\Domain\Region\Entity\Region;
use App\Infrastructure\Http\Address\v1\Response\Normalizer\AddressListNormalizer;
use App\Infrastructure\Http\Address\v1\Response\Normalizer\AddressPaginateNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;


#[Route('/api/v1/address', name: 'address_')]
class AddressListController extends AbstractController
{
    public function __construct(
        public readonly QueryBus $queryBus,
        public readonly AddressPaginateNormalizer $addressPaginateNormalizer,
        public readonly AddressListNormalizer $addressListNormalizer
    )
    {
    }

    #[Route('/paginate', name: 'paginate', methods: 'GET')]
    #[OA\Tag(name: 'address')]
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
                content: new OA\JsonContent(ref: new Model(type: Address::class))
            )
        ]
    )]
    public function paginate(#[MapQueryParameter] int $page, #[MapQueryParameter] int $offset): JsonResponse
    {
        $addresses = $this->queryBus->handle(new FindAddressesByPaginateQuery($page, $offset, []));

        return $this->json($this->addressPaginateNormalizer->normalize($addresses));
    }

    #[Route('/search', name: 'search', methods: 'GET')]
    #[OA\Tag(name: 'address')]
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
        $addresses = $this->queryBus->handle(new FindAddressByQuery($query, []));

        return $this->json($this->addressListNormalizer->normalize($addresses));
    }

    #[Route('/find-by-city-id-paginate', name: 'find_by_city_id_paginate', methods: 'GET')]
    #[OA\Tag(name: 'address')]
    #[OA\Parameter(
        name: 'cityId',
        description: 'City Id(Uuid)',
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
        content: new OA\JsonContent(ref: new Model(type: Address::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findByCityIdPaginate(#[MapQueryParameter] string $cityId, #[MapQueryParameter] int $page, #[MapQueryParameter] int $offset): JsonResponse
    {
        $city = $this->queryBus->handle(new FindCityByIdQuery(Uuid::fromString($cityId)));

        if (is_null($city)) {
            $city = $this->queryBus->handle(new FindCityByIdDeactivatedQuery(Uuid::fromString($cityId)));

            if (is_null($city)) {
                throw new CountryNotFoundException(sprintf('City with id: %s not found', $cityId));
            }
        }

        $addresses = $this->queryBus->handle(new FindAddressesByCityPaginateQuery($city, $page, $offset,[]));

        if (is_null($addresses)) {
            return new JsonResponse(null, 404);
        }

        return $this->json($this->addressPaginateNormalizer->normalize($addresses));
    }
}