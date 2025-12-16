<?php

namespace App\Infrastructure\Http\City\v1\Response\Normalizer;

use App\Core\Domain\City\Entity\City;
use App\Infrastructure\Http\Region\v1\Response\Normalizer\RegionNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CityNormalizer implements NormalizerInterface
{
    public function __construct(public RegionNormalizer $regionNormalizer)
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId()->toRfc4122(),
            'region' => $this->regionNormalizer->normalize($object->getRegion()),
            'type' => $object->getType(),
            'name' => $object->getName(),
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
            City::class
        ];
    }
}