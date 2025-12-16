<?php

namespace App\Infrastructure\Http\Currency\v1;

use App\Application\Currency\Query\FindCurrencyByCodeDeactivatedQuery;
use App\Application\Currency\Query\FindCurrencyByCodeQuery;
use App\Application\Currency\Query\FindCurrencyByIdDeactivatedQuery;
use App\Application\Currency\Query\FindCurrencyByIdQuery;
use App\Application\Currency\Query\FindCurrencyByNumDeactivatedQuery;
use App\Application\Currency\Query\FindCurrencyByNumQuery;
use App\Application\QueryBus;
use App\Core\Domain\Currency\Entity\Currency;
use App\Infrastructure\Http\Currency\v1\Response\Normalizer\CurrencyNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[AsController]
#[Route('/api/v1/currency', name: 'currency_')]
class FindCurrencyController extends AbstractController
{
    public function __construct(
        public readonly QueryBus           $queryBus,
        public readonly CurrencyNormalizer $currencyNormalizer
    )
    {
    }

    #[Route('/find-by-id/{currencyId}', name: 'find_by_id', methods: 'GET')]
    #[OA\Tag(name: 'currency')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Currency::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findById(Uuid $currencyId): JsonResponse
    {
        $currency = $this->queryBus->handle(new FindCurrencyByIdQuery($currencyId));

        if (is_null($currency)) {
            $currency = $this->queryBus->handle(new FindCurrencyByIdDeactivatedQuery($currencyId));

            if (is_null($currency)) {
                return new JsonResponse(null, 404);
            }
        }

        return $this->json(
            $this->currencyNormalizer->normalize($currency)
        );
    }

    #[Route('/find-by-code/{code}', name: 'find_by_code', methods: 'GET')]
    #[OA\Tag(name: 'currency')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Currency::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findByCode(string $code): JsonResponse
    {
        $currency = $this->queryBus->handle(new FindCurrencyByCodeQuery($code));

        if (is_null($currency)) {
            $currency = $this->queryBus->handle(new FindCurrencyByCodeDeactivatedQuery($code));

            if (is_null($currency)) {
                return new JsonResponse(null, 404);
            }
        }

        return $this->json(
            $this->currencyNormalizer->normalize($currency)
        );
    }

    #[Route('/find-by-num/{num}', name: 'find_by_num', methods: 'GET')]
    #[OA\Tag(name: 'currency')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Currency::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findByNum(int $num): JsonResponse
    {
        $currency = $this->queryBus->handle(new FindCurrencyByNumQuery($num));

        if (is_null($currency)) {
            $currency = $this->queryBus->handle(new FindCurrencyByNumDeactivatedQuery($num));

            if (is_null($currency)) {
                return new JsonResponse(null, 404);
            }
        }

        return $this->json(
            $this->currencyNormalizer->normalize($currency)
        );
    }
}