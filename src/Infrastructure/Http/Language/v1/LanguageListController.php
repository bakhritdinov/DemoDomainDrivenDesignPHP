<?php

namespace App\Infrastructure\Http\Language\v1;

use App\Application\Language\Query\FindLanguageByPaginateQuery;
use App\Application\QueryBus;
use App\Core\Domain\Language\Entity\Language;
use App\Infrastructure\Http\Language\v1\Response\Normalizer\LanguagePaginateNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/language', name: 'language_')]
class LanguageListController extends AbstractController
{
    public function __construct(
        public readonly QueryBus                   $queryBus,
        public readonly LanguagePaginateNormalizer $languagePaginateNormalizer
    )
    {
    }

    #[Route('/paginate', name: 'paginate', methods: 'GET')]
    #[OA\Tag(name: 'language')]
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
                content: new OA\JsonContent(ref: new Model(type: Language::class))
            )
        ]
    )]
    public function paginate(#[MapQueryParameter] int $page, #[MapQueryParameter] int $offset): JsonResponse
    {
        $languages = $this->queryBus->handle(new FindLanguageByPaginateQuery($page, $offset, []));

        return $this->json(
            $this->languagePaginateNormalizer->normalize($languages)
        );
    }
}