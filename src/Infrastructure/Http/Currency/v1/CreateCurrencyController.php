<?php

namespace App\Infrastructure\Http\Currency\v1;

use App\Application\CommandBus;
use App\Application\Currency\Command\CreateCurrencyCommand;
use App\Core\Domain\Currency\Dto\CreateCurrencyDto;
use App\Infrastructure\Http\Currency\v1\Response\Normalizer\CurrencyNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class CreateCurrencyController extends AbstractController
{
    public function __construct(
        public readonly CommandBus         $commandBus,
        public readonly CurrencyNormalizer $currencyNormalizer
    )
    {
    }

    #[Route('/api/v1/currency', name: 'currency_create', methods: ['POST'])]
    #[OA\Tag(name: 'currency')]
    #[OA\Post(requestBody: new OA\RequestBody(attachables: [new Model(type: CreateCurrencyDto::class)]))]
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
    public function __invoke(#[MapRequestPayload] CreateCurrencyDto $createCurrencyDto): Response
    {
        $envelope = $this->commandBus->dispatch(new CreateCurrencyCommand($createCurrencyDto));


        return $this->json($this->currencyNormalizer->normalize(
            $envelope
                ->last(HandledStamp::class)
                ->getResult()
        ), 201);
    }
}