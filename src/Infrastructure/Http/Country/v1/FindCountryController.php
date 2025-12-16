<?php

namespace App\Infrastructure\Http\Country\v1;

use App\Application\Country\Query\FindCountryByIdDeactivatedQuery;
use App\Application\Country\Query\FindCountryByIdQuery;
use App\Application\Country\Query\FindCountryByNameDeactivatedQuery;
use App\Application\Country\Query\FindCountryByNameQuery;
use App\Application\Country\Query\FindCountryByNumericCodeDeactivatedQuery;
use App\Application\Country\Query\FindCountryByNumericCodeQuery;
use App\Application\QueryBus;
use App\Core\Domain\Country\Entity\Country;
use App\Infrastructure\Http\Country\v1\Response\Normalizer\CountryNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/v1/country', name: 'country_')]
class FindCountryController extends AbstractController
{
    public function __construct(
        public readonly QueryBus          $queryBus,
        public readonly CountryNormalizer $countryNormalizer
    )
    {
    }

    #[Route('/find-by-id/{countryId}', name: 'find_by_id', methods: 'GET')]
    #[OA\Tag(name: 'country')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Country::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findById(Uuid $countryId): JsonResponse
    {
        $country = $this->queryBus->handle(new FindCountryByIdQuery($countryId));

        if (is_null($country)) {
            $country = $this->queryBus->handle(new FindCountryByIdDeactivatedQuery($countryId));
            if (is_null($country)) {
                return new JsonResponse(null, 404);
            }
        }

        return $this->json($this->countryNormalizer->normalize($country));
    }

    #[Route('/find-by-numeric-code/{numericCode}', name: 'find_by_numeric-code', methods: 'GET')]
    #[OA\Tag(name: 'country')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Country::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findByNumericCode(int $numericCode): JsonResponse
    {
        $country = $this->queryBus->handle(new FindCountryByNumericCodeQuery($numericCode));

        if (is_null($country)) {
            $country = $this->queryBus->handle(new FindCountryByNumericCodeDeactivatedQuery($numericCode));
            if (is_null($country)) {
                return new JsonResponse(null, 404);
            }
        }

        return $this->json($this->countryNormalizer->normalize($country));
    }

    #[Route('/find-by-name/{name}', name: 'find_by_name', methods: 'GET')]
    #[OA\Tag(name: 'country')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Country::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findByName(string $name): JsonResponse
    {
        $country = $this->queryBus->handle(new FindCountryByNameQuery($name));

        if (is_null($country)) {
            $country = $this->queryBus->handle(new FindCountryByNameDeactivatedQuery($name));
            if (is_null($country)) {
                return new JsonResponse(null, 404);
            }
        }

        return $this->json($this->countryNormalizer->normalize($country));
    }
}