<?php

namespace App\Infrastructure\Http\Country\v1;

use App\Application\Country\Query\FindCountriesByPaginateQuery;
use App\Application\Country\Query\FindCountryByQuery;
use App\Application\QueryBus;
use App\Core\Domain\Country\Entity\Country;
use App\Infrastructure\Http\Country\v1\Response\Normalizer\CountryListNormalizer;
use App\Infrastructure\Http\Country\v1\Response\Normalizer\CountryPaginateNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/country', name: 'country_')]
class CountryListController extends AbstractController
{
    public function __construct(
        public readonly QueryBus                  $queryBus,
        public readonly CountryPaginateNormalizer $countryPaginateNormalizer,
        public readonly CountryListNormalizer     $countryListNormalizer
    )
    {
    }

    #[Route('/paginate', name: 'paginate', methods: 'GET')]
    #[OA\Tag(name: 'country')]
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
                content: new OA\JsonContent(ref: new Model(type: Country::class))
            )
        ]
    )]
    public function paginate(#[MapQueryParameter] int $page, #[MapQueryParameter] int $offset): JsonResponse
    {
        $countries = $this->queryBus->handle(new FindCountriesByPaginateQuery($page, $offset, []));

        return $this->json($this->countryPaginateNormalizer->normalize($countries));
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
                content: new OA\JsonContent(ref: new Model(type: Country::class))
            )
        ]
    )]
    public function search(#[MapQueryParameter] string $query): JsonResponse
    {
        $countries = $this->queryBus->handle(new FindCountryByQuery($query));

        return $this->json($this->countryListNormalizer->normalize($countries));
    }

}