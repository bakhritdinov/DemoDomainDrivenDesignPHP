<?php

namespace App\Infrastructure\Http\Currency\v1;

use App\Application\Currency\Query\GetAllCurrenciesQuery;
use App\Application\Currency\Query\GetCurrenciesByPaginateQuery;
use App\Application\QueryBus;
use App\Core\Domain\Currency\Entity\Currency;
use App\Infrastructure\Http\Currency\v1\Response\Normalizer\CurrencyListNormalizer;
use App\Infrastructure\Http\Currency\v1\Response\Normalizer\CurrencyPaginateNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/api/v1/currency', name: 'currency_')]
class CurrencyListController extends AbstractController
{
    public function __construct(
        public readonly QueryBus                   $queryBus,
        public readonly CurrencyListNormalizer     $currencyListNormalizer,
        public readonly CurrencyPaginateNormalizer $currencyPaginateNormalizer
    )
    {
    }

    #[Route(name: 'get_all', methods: 'GET')]
    #[OA\Tag(name: 'currency')]
    #[OA\Get(
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful response',
                content: new OA\JsonContent(ref: new Model(type: Currency::class))
            )
        ]
    )]
    public function all(): JsonResponse
    {
        $currencies = $this->queryBus->handle(new GetAllCurrenciesQuery());

        return $this->json(
            $this->currencyListNormalizer->normalize($currencies)
        );
    }

    #[Route('/paginate', name: 'paginate', methods: 'GET')]
    #[OA\Tag(name: 'currency')]
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
                content: new OA\JsonContent(ref: new Model(type: Currency::class))
            )
        ]
    )]
    public function paginate(#[MapQueryParameter] int $page, #[MapQueryParameter] int $offset): JsonResponse
    {
        $currencies = $this->queryBus->handle(new GetCurrenciesByPaginateQuery($page, $offset, []));

        return $this->json(
            $this->currencyPaginateNormalizer->normalize($currencies));
    }
}