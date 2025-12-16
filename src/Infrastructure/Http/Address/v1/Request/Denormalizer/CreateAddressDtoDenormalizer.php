<?php

namespace App\Infrastructure\Http\Address\v1\Request\Denormalizer;

use App\Core\Domain\Address\Dto\CreateAddressDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CreateAddressDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): CreateAddressDto
    {
        return new CreateAddressDto(
            $data['address']
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === CreateAddressDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}