<?php

namespace App\Infrastructure\Http\Language\v1;

use App\Application\Language\Query\FindLanguageByCodeDeactivatedQuery;
use App\Application\Language\Query\FindLanguageByCodeQuery;
use App\Application\Language\Query\FindLanguageByIdDeactivatedQuery;
use App\Application\Language\Query\FindLanguageByIdQuery;
use App\Application\QueryBus;
use App\Core\Domain\Language\Entity\Language;
use App\Infrastructure\Http\Language\v1\Response\Normalizer\LanguageNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/v1/language', name: 'language_')]
class FindLanguageController extends AbstractController
{
    public function __construct(
        public readonly QueryBus           $queryBus,
        public readonly LanguageNormalizer $languageNormalizer
    )
    {
    }

    #[Route('/find-by-id/{languageId}', name: 'find_by_id', methods: 'GET')]
    #[OA\Tag(name: 'language')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Language::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findById(Uuid $languageId): JsonResponse
    {
        $language = $this->queryBus->handle(new FindLanguageByIdQuery($languageId));

        if (is_null($language)) {
            $language = $this->queryBus->handle(new FindLanguageByIdDeactivatedQuery($languageId));

            if (is_null($language)) {
                return new JsonResponse(null, 404);
            }
        }

        return $this->json(
            $this->languageNormalizer->normalize($language)
        );
    }

    #[Route('/find-by-code/{code}', name: 'find_by_code', methods: 'GET')]
    #[OA\Tag(name: 'language')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Language::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findByCode(string $code): JsonResponse
    {
        $language = $this->queryBus->handle(new FindLanguageByCodeQuery($code));

        if (is_null($language)) {
            $language = $this->queryBus->handle(new FindLanguageByCodeDeactivatedQuery($code));

            if (is_null($language)) {
                return new JsonResponse(null, 404);
            }
        }

        return $this->json(
            $this->languageNormalizer->normalize($language)
        );
    }
}