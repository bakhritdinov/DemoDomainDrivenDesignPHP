<?php

namespace App\Infrastructure\Http\City\v1;

use App\Application\City\Command\CreateCityCommand;
use App\Application\CommandBus;
use App\Core\Domain\City\Dto\CreateCityDto;
use App\Infrastructure\Http\City\v1\Response\Normalizer\CityNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class CreateCityController extends AbstractController
{
    public function __construct(
        public readonly CommandBus     $commandBus,
        public readonly CityNormalizer $cityNormalizer
    )
    {
    }

    #[Route('/api/v1/city', name: 'city_create', methods: ['POST'])]
    #[OA\Tag(name: 'city')]
    #[OA\Post(requestBody: new OA\RequestBody(attachables: [new Model(type: CreateCityDto::class)]))]
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
    public function __invoke(#[MapRequestPayload] CreateCityDto $createCityDto): Response
    {
        $envelope = $this->commandBus->dispatch(new CreateCityCommand($createCityDto));

        return $this->json($this->cityNormalizer->normalize(
            $envelope
                ->last(HandledStamp::class)
                ->getResult()
        ), 201);
    }
}