<?php

namespace App\Infrastructure\Http\Currency\v1\Request\Denormalizer;

use App\Core\Domain\Currency\Dto\CreateCurrencyDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CreateCurrencyDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): CreateCurrencyDto
    {
        return new CreateCurrencyDto(
            $data['code'],
            $data['num'],
            $data['name'],
            $data['isActive'] ?? null
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === CreateCurrencyDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}