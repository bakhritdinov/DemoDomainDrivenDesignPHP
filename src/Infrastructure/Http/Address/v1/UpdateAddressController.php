<?php

namespace App\Infrastructure\Http\Address\v1;

use App\Application\Address\Command\UpdateAddressCommand;
use App\Application\CommandBus;
use App\Core\Domain\Address\Dto\UpdateAddressDto;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[AsController]
class UpdateAddressController extends AbstractController
{
    public function __construct(public readonly CommandBus $commandBus)
    {
    }

    #[Route('/api/v1/address/{addressId}', name: 'address_update', methods: 'PUT')]
    #[OA\Tag(name: 'address')]
    #[OA\Put(requestBody: new OA\RequestBody(attachables: [new Model(type: UpdateAddressDto::class)]))]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: null
    )]
    public function __invoke(Uuid $addressId, #[MapRequestPayload] UpdateAddressDto $updateAddressDto): Response
    {
        $this->commandBus->dispatch(new UpdateAddressCommand($addressId, $updateAddressDto->isActive));

        return new Response(null, 200);
    }
}