<?php

namespace App\Infrastructure\Http\Region\v1;

use App\Application\CommandBus;
use App\Application\Region\Command\UpdateRegionCommand;
use App\Core\Domain\Region\Dto\UpdateRegionDto;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[AsController]
class UpdateRegionController extends AbstractController
{
    public function __construct(public readonly CommandBus $commandBus)
    {
    }

    #[Route('/api/v1/region/{regionId}', name: 'region_update', methods: 'PUT')]
    #[OA\Tag(name: 'region')]
    #[OA\Put(requestBody: new OA\RequestBody(attachables: [new Model(type: UpdateRegionDto::class)]))]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: null
    )]
    public function __invoke(Uuid $regionId, #[MapRequestPayload] UpdateRegionDto $updateRegionDto): Response
    {
        $this->commandBus->dispatch(new UpdateRegionCommand($regionId, $updateRegionDto));

        return new Response(null, 200);
    }
}