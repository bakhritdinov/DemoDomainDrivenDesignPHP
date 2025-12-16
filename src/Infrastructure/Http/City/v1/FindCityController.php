<?php

namespace App\Infrastructure\Http\City\v1;

use App\Application\City\Query\FindCityByIdQuery;
use App\Application\QueryBus;
use App\Core\Domain\City\Entity\City;
use App\Infrastructure\Http\City\v1\Response\Normalizer\CityNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/v1/city', name: 'city_')]
class FindCityController extends AbstractController
{
    public function __construct(
        public readonly QueryBus $queryBus,
        public readonly CityNormalizer $cityNormalizer
    )
    {
    }

    #[Route('/find-by-id/{cityId}', name: 'find_by_id', methods: 'GET')]
    #[OA\Tag(name: 'city')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: City::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findById(Uuid $cityId): JsonResponse
    {
        $city = $this->queryBus->handle(new FindCityByIdQuery($cityId));

        if (is_null($city)) {
            $city = $this->queryBus->handle(new FindCityByIdQuery($cityId));

            if (is_null($city)) {
                return new JsonResponse(null, 404);
            }
        }

        return $this->json($this->cityNormalizer->normalize($city));
    }

}