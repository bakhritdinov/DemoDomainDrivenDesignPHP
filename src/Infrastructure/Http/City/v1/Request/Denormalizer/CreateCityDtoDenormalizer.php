<?php

namespace App\Infrastructure\Http\City\v1\Request\Denormalizer;

use App\Core\Domain\City\Dto\CreateCityDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CreateCityDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): CreateCityDto
    {
        return new CreateCityDto(
            regionCode: $data['regionCode'],
            type: $data['type'],
            name: $data['name']
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === CreateCityDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}