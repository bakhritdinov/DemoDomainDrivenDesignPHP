<?php

namespace App\Infrastructure\Http\Address\v1;

use App\Application\Address\Query\FindAddressByAddressDeactivatedQuery;
use App\Application\Address\Query\FindAddressByAddressQuery;
use App\Application\Address\Query\FindAddressByIdDeactivatedQuery;
use App\Application\Address\Query\FindAddressByIdQuery;
use App\Application\QueryBus;
use App\Core\Domain\Address\Entity\Address;
use App\Infrastructure\Http\Address\v1\Response\Normalizer\AddressNormalizer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/v1/address', name: 'address_')]
class FindAddressController extends AbstractController
{
    public function __construct(
        public readonly QueryBus          $queryBus,
        public readonly AddressNormalizer $addressNormalizer
    )
    {
    }

    #[Route('/find-by-id/{addressId}', name: 'find_by_id', methods: 'GET')]
    #[OA\Tag(name: 'address')]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Address::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findById(Uuid $addressId): JsonResponse
    {
        $address = $this->queryBus->handle(new FindAddressByIdQuery($addressId));

        if (is_null($address)) {
            $address = $this->queryBus->handle(new FindAddressByIdDeactivatedQuery($addressId));

            if (is_null($address)) {
                return new JsonResponse(null, 404);
            }
        }

        return $this->json($this->addressNormalizer->normalize($address));
    }

    #[Route('/find-by-address/{address}', name: 'address_find_by_address', methods: ['GET'])]
    #[OA\Tag(name: 'address')]
    #[ParamConverter('address', options: ['mapping' => ['address' => 'string']])]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(ref: new Model(type: Address::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Entity was not found',
        content: null
    )]
    public function findByAddress(string $address): JsonResponse
    {
        $address = $this->queryBus->handle(new FindAddressByAddressQuery($address));

        if (is_null($address)) {
            $address = $this->queryBus->handle(new FindAddressByAddressDeactivatedQuery($address));

            if (is_null($address)) {
                return new JsonResponse(null, 404);
            }
        }

        return $this->json($this->addressNormalizer->normalize($address));
    }
}