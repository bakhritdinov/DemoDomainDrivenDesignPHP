<?php

namespace App\Infrastructure\Http\Address\v1\Response\Normalizer;

use App\Core\Domain\Address\Entity\Address;
use App\Infrastructure\Http\City\v1\Response\Normalizer\CityNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AddressNormalizer implements NormalizerInterface
{
    public function __construct(public CityNormalizer $cityNormalizer)
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId()->toRfc4122(),
            'city' => $this->cityNormalizer->normalize($object->getCity()),
            'postalCode' => $object->getPostalCode(),
            'street' => $object->getStreet(),
            'house' => $object->getHouse(),
            'flat' => $object->getFlat(),
            'entrance' => $object->getEntrance(),
            'floor' => $object->getFloor(),
            'point' => [
                'latitude' => $object->getPoint()->getLatitude(),
                'longitude' => $object->getPoint()->getLongitude()
            ],
            'isActive' => $object->isActive(),
            'createdAt' => $object->getCreatedAt(),
            'updatedAt' => $object->getUpdatedAt()
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return false;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Address::class
        ];
    }
}