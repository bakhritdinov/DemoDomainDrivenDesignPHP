<?php

namespace App\Infrastructure\Http\Region\v1\Response\Normalizer;

use App\Core\Domain\Region\Entity\Region;
use App\Infrastructure\Http\Country\v1\Response\Normalizer\CountryNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RegionNormalizer implements NormalizerInterface
{
    public function __construct(public CountryNormalizer $countryNormalizer)
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId()->toRfc4122(),
            'country' => $this->countryNormalizer->normalize($object->getCountry()),
            'name' => $object->getName(),
            'code' => $object->getCode(),
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
            Region::class
        ];
    }
}