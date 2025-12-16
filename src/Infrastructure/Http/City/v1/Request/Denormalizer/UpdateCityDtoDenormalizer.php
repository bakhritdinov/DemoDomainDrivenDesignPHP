<?php

namespace App\Infrastructure\Http\City\v1\Request\Denormalizer;

use App\Core\Domain\City\Dto\UpdateCityDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UpdateCityDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): UpdateCityDto
    {
        return new UpdateCityDto(
            regionId: $data['redionId'] ?? null,
            type: $data['type'] ?? null,
            name: $data['name'] ?? null,
            isActive: $data['isActive'] ?? null
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === UpdateCityDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}