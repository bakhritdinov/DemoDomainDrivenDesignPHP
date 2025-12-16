<?php

namespace App\Infrastructure\Http\Country\v1\Request\Denormalizer;

use App\Core\Domain\Country\Dto\UpdateCountryDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UpdateCountryDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): UpdateCountryDto
    {
        return new UpdateCountryDto(
            name: $data['name'] ?? null,
            numericCode: $data['numericCode'] ?? null,
            alpha2: $data['alpha2'] ?? null,
            alpha3: $data['alpha3'] ?? null,
            isActive: $data['isActive'] ?? null
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === UpdateCountryDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}