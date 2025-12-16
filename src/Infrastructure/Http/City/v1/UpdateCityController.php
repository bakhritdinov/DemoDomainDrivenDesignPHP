<?php

namespace App\Infrastructure\Http\City\v1;

use App\Application\City\Command\UpdateCityCommand;
use App\Application\CommandBus;
use App\Core\Domain\City\Dto\UpdateCityDto;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[AsController]
class UpdateCityController extends AbstractController
{
    public function __construct(public readonly CommandBus $commandBus)
    {
    }

    #[Route('/api/v1/city/{cityId}', name: 'city_update', methods: 'PUT')]
    #[OA\Tag(name: 'city')]
    #[OA\Put(requestBody: new OA\RequestBody(attachables: [new Model(type: UpdateCityDto::class)]))]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: null
    )]
    public function __invoke(Uuid $cityId, #[MapRequestPayload] UpdateCityDto $updateCityDto): Response
    {
        $this->commandBus->dispatch(new UpdateCityCommand($cityId, $updateCityDto));

        return new Response(null, 200);
    }
}