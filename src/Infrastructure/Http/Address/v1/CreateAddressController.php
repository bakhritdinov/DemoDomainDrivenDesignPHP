<?php

namespace App\Infrastructure\Http\Address\v1;

use App\Application\Address\Command\CreateAddressCommand;
use App\Application\CommandBus;
use App\Core\Domain\Address\Dto\CreateAddressDto;
use App\Infrastructure\Http\Address\v1\Response\Normalizer\AddressNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class CreateAddressController extends AbstractController
{
    public function __construct(
        public readonly CommandBus $commandBus,
        public readonly AddressNormalizer $addressNormalizer,
    )
    {
    }

    #[Route('/api/v1/address', name: 'address_create', methods: ['POST'])]
    #[OA\Tag(name: 'address')]
    #[OA\Post(requestBody: new OA\RequestBody(attachables: [new Model(type: CreateAddressDto::class)]))]
    #[OA\Response(
        response: 201,
        description: 'Successful created',
        content: null
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function __invoke(#[MapRequestPayload] CreateAddressDto $createAddressDto): Response
    {
        $envelope = $this->commandBus->dispatch(new CreateAddressCommand($createAddressDto->address));

        return $this->json($this->addressNormalizer->normalize(
            $envelope
                ->last(HandledStamp::class)
                ->getResult()
        ), 201);
    }
}