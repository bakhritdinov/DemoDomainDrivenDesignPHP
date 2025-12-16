<?php

namespace App\Infrastructure\Http\Region\v1;

use App\Application\CommandBus;
use App\Application\Region\Command\CreateRegionCommand;
use App\Core\Domain\Region\Dto\CreateRegionDto;
use App\Infrastructure\Http\Region\v1\Response\Normalizer\RegionNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class CreateRegionController extends AbstractController
{
    public function __construct(
        public readonly CommandBus       $commandBus,
        public readonly RegionNormalizer $regionNormalizer
    )
    {
    }

    #[Route('/api/v1/region', name: 'region_create', methods: ['POST'])]
    #[OA\Tag(name: 'region')]
    #[OA\Post(requestBody: new OA\RequestBody(attachables: [new Model(type: CreateRegionDto::class)]))]
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
    public function __invoke(#[MapRequestPayload] CreateRegionDto $createRegionDto): Response
    {
        $envelope = $this->commandBus->dispatch(new CreateRegionCommand($createRegionDto));

        return $this->json($this->regionNormalizer->normalize(
            $envelope
                ->last(HandledStamp::class)
                ->getResult()
        ), 201);
    }
}