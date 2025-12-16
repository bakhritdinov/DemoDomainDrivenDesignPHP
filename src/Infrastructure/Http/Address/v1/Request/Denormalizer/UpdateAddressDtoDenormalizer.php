<?php

namespace App\Infrastructure\Http\Address\v1\Request\Denormalizer;

use App\Core\Domain\Address\Dto\UpdateAddressDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UpdateAddressDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): UpdateAddressDto
    {
        return new UpdateAddressDto(
            $data['isActive'] ?? null
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === UpdateAddressDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}