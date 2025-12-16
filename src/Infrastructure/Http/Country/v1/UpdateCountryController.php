<?php

namespace App\Infrastructure\Http\Country\v1;

use App\Application\CommandBus;
use App\Application\Country\Command\UpdateCountryCommand;
use App\Core\Domain\Country\Dto\UpdateCountryDto;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[AsController]
class UpdateCountryController extends AbstractController
{
    public function __construct(public readonly CommandBus $commandBus)
    {
    }

    #[Route('/api/v1/country/{countryId}', name: 'country_update', methods: 'PUT')]
    #[OA\Tag(name: 'country')]
    #[OA\Put(requestBody: new OA\RequestBody(attachables: [new Model(type: UpdateCountryDto::class)]))]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: null
    )]
    public function __invoke(Uuid $countryId, #[MapRequestPayload] UpdateCountryDto $updateCountryDto): Response
    {
        $this->commandBus->dispatch(new UpdateCountryCommand($countryId, $updateCountryDto));

        return new Response(null, 200);
    }
}