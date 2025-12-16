<?php

namespace App\Infrastructure\Http\Region\v1;

use App\Application\QueryBus;
use App\Application\Region\Query\FindRegionByCodeDeactivatedQuery;
use App\Application\Region\Query\FindRegionByCodeQuery;
use App\Application\Region\Query\FindRegionByIdDeactivatedQuery;
use App\Application\Region\Query\FindRegionByIdQuery;
use App\Application\Region\Query\FindRegionByNameDeactivatedQuery;
use App\Application\Region\Query\FindRegionByNameQuery;
use App\Core\Domain\Region\Entity\Region;
use App\Infrastructure\Http\Region\v1\Response\Normalizer\RegionNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/v1/region', name: 'region_')]
class FindRegionController extends AbstractController
{
    public function __construct(
        public readonly QueryBus         $queryBus,
        public readonly RegionNormalizer $regionNormalizer
    )
    {
    }

    #[Route('/find-by-id/{regionId}', name: 'find_by_id', methods: 'GET')]
    #[OA\Tag(name: 'region')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Region::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findById(Uuid $regionId): JsonResponse
    {
        $region = $this->queryBus->handle(new FindRegionByIdQuery($regionId));

        if (is_null($region)) {
            $region = $this->queryBus->handle(new FindRegionByIdDeactivatedQuery($regionId));

            if (is_null($region)) {
                return new JsonResponse(null, 404);
            }
        }

        return $this->json($this->regionNormalizer->normalize($region));
    }

    #[Route('/find-by-code/{code}', name: 'find_by_code', methods: 'GET')]
    #[IsGranted(attribute: 'FIND_REGION')]
    #[OA\Tag(name: 'region')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Region::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findByCode(string $code): JsonResponse
    {
        $region = $this->queryBus->handle(new FindRegionByCodeQuery($code));

        if (is_null($region)) {
            $region = $this->queryBus->handle(new FindRegionByCodeDeactivatedQuery($code));

            if (is_null($region)) {
                return new JsonResponse(null, 404);
            }
        }

        return $this->json($this->regionNormalizer->normalize($region));
    }

    #[Route('/find-by-name/{name}', name: 'find_by_name', methods: 'GET')]
    #[OA\Tag(name: 'region')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Region::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findByName(string $name): JsonResponse
    {
        $region = $this->queryBus->handle(new FindRegionByNameQuery($name));

        if (is_null($region)) {
            $region = $this->queryBus->handle(new FindRegionByNameDeactivatedQuery($name));

            if (is_null($region)) {
                return new JsonResponse(null, 404);
            }
        }

        return $this->json($this->regionNormalizer->normalize($region));
    }
}