<?php

namespace App\Infrastructure\Http\Language\v1;

use App\Application\CommandBus;
use App\Application\Language\Command\CreateLanguageCommand;
use App\Core\Domain\Language\Dto\CreateLanguageDto;
use App\Infrastructure\Http\Language\v1\Response\Normalizer\LanguageNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class CreateLanguageController extends AbstractController
{
    public function __construct(
        public readonly CommandBus         $commandBus,
        public readonly LanguageNormalizer $languageNormalizer
    )
    {
    }

    #[Route('/api/v1/language', name: 'language_create', methods: ['POST'])]
    #[OA\Tag(name: 'language')]
    #[OA\Post(requestBody: new OA\RequestBody(attachables: [new Model(type: CreateLanguageDto::class)]))]
    #[OA\Response(
        response: 200,
        description: 'Successful created',
        content: null
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function __invoke(#[MapRequestPayload] CreateLanguageDto $createLanguageDto): Response
    {
        $envelope = $this->commandBus->dispatch(new CreateLanguageCommand($createLanguageDto));

        return $this->json($this->languageNormalizer->normalize(
            $envelope
                ->last(HandledStamp::class)
                ->getResult()
        ), 201);
    }
}