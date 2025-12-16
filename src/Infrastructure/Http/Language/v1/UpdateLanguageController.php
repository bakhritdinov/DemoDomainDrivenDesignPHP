<?php

namespace App\Infrastructure\Http\Language\v1;

use App\Application\CommandBus;
use App\Application\Language\Command\UpdateLanguageCommand;
use App\Core\Domain\Language\Dto\UpdateLanguageDto;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[AsController]
class UpdateLanguageController extends AbstractController
{
    public function __construct(public readonly CommandBus $commandBus)
    {
    }

    #[Route('/api/v1/language/{languageId}', name: 'language_update', methods: ['PUT'])]
    #[OA\Tag(name: 'language')]
    #[OA\Post(requestBody: new OA\RequestBody(attachables: [new Model(type: UpdateLanguageDto::class)]))]
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
    public function __invoke(Uuid $languageId, #[MapRequestPayload] UpdateLanguageDto $updateLanguageDto): Response
    {
        $this->commandBus->dispatch(new UpdateLanguageCommand($languageId, $updateLanguageDto));

        return new Response(null, 200);
    }
}