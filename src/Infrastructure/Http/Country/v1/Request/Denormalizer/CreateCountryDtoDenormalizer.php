<?php

namespace App\Infrastructure\Http\Country\v1\Request\Denormalizer;

use App\Core\Domain\Country\Dto\CreateCountryDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CreateCountryDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): CreateCountryDto
    {
        return new CreateCountryDto(
            name: $data['name'],
            numericCode: $data['numericCode'],
            alpha2: $data['alpha2'],
            alpha3: $data['alpha3']
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === CreateCountryDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}