<?php

namespace App\Infrastructure\Http\Region\v1\Request\Denormalizer;

use App\Core\Domain\Region\Dto\UpdateRegionDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Uid\Uuid;

class UpdateRegionDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): UpdateRegionDto
    {
        return new UpdateRegionDto(
            countryId: array_key_exists('countryId', $data) && $data['countryId'] ? Uuid::fromString($data['countryId']) : null,
            name: $data['name'] ?? null,
            code: $data['code'] ?? null,
            isActive: $data['isActive'] ?? null
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === UpdateRegionDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}