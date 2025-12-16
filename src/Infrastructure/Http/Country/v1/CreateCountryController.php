<?php

namespace App\Infrastructure\Http\Country\v1;

use App\Application\CommandBus;
use App\Application\Country\Command\CreateCountryCommand;
use App\Core\Domain\Country\Dto\CreateCountryDto;
use App\Core\Domain\Country\Entity\Country;
use App\Infrastructure\Http\Country\v1\Response\Normalizer\CountryNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
final class CreateCountryController extends AbstractController
{
    public function __construct(
        public readonly CommandBus        $commandBus,
        public readonly CountryNormalizer $countryNormalizer
    )
    {
    }

    #[Route('/api/v1/country', name: 'country_create', methods: ['POST'])]
    #[OA\Tag(name: 'country')]
    #[OA\Post(requestBody: new OA\RequestBody(attachables: [new Model(type: CreateCountryDto::class)]))]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Country::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function __invoke(#[MapRequestPayload] CreateCountryDto $createCountryDto): Response
    {
        $envelope = $this->commandBus->dispatch(new CreateCountryCommand($createCountryDto));

        return $this->json($this->countryNormalizer->normalize(
            $envelope
                ->last(HandledStamp::class)
                ->getResult()
        ), 201);
    }
}