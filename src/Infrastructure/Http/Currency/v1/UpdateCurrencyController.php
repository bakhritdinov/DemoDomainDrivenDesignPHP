<?php

namespace App\Infrastructure\Http\Currency\v1;

use App\Application\CommandBus;
use App\Application\Currency\Command\UpdateCurrencyCommand;
use App\Core\Domain\Currency\Dto\UpdateCurrencyDto;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[AsController]
class UpdateCurrencyController extends AbstractController
{
    public function __construct(public readonly CommandBus $commandBus)
    {
    }

    #[Route('/api/v1/currency/{currencyId}', name: 'currency_update', methods: ['PUT'])]
    #[OA\Tag(name: 'currency')]
    #[OA\Put(requestBody: new OA\RequestBody(attachables: [new Model(type: UpdateCurrencyDto::class)]))]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: null
    )]
    public function __invoke(Uuid $currencyId, #[MapRequestPayload] UpdateCurrencyDto $updateCurrencyDto): Response
    {
        $this->commandBus->dispatch(new UpdateCurrencyCommand($currencyId, $updateCurrencyDto));

        return new Response(null, 200);
    }
}