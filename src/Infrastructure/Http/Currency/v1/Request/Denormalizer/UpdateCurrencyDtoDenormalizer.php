<?php

namespace App\Infrastructure\Http\Currency\v1\Request\Denormalizer;

use App\Core\Domain\Currency\Dto\UpdateCurrencyDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UpdateCurrencyDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): UpdateCurrencyDto
    {
        return new UpdateCurrencyDto(
            $data['code'] ?? null,
            $data['num'] ?? null,
            $data['name'] ?? null,
            $data['isActive'] ?? null
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === UpdateCurrencyDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}