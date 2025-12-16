<?php

namespace App\Infrastructure\DaData\Service;

use App\Application\Address\Query\External\FindExternalAddressInterface;
use App\Core\Domain\Address\Dto\AddressDto;
use Dadata\DadataClient;
use Symfony\Component\Serializer\SerializerInterface;

class FindByCleanAddressService implements FindExternalAddressInterface
{
    public function __construct(public DadataClient $dadataClient, public SerializerInterface $serializer)
    {
    }

    public function find(string $address): ?AddressDto
    {
        $response = $this->dadataClient->clean('address', $address);

        return $this->serializer->denormalize($response, AddressDto::class);
    }
}