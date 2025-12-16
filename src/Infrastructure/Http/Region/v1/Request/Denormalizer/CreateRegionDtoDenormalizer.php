<?php

namespace App\Infrastructure\Http\Region\v1\Request\Denormalizer;

use App\Core\Domain\Region\Dto\CreateRegionDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CreateRegionDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): CreateRegionDto
    {
        return new CreateRegionDto(
            countryAlpha2: $data['countryAlpha2'],
            name: $data['name'],
            code: $data['code']
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === CreateRegionDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}