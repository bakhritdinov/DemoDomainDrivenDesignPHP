<?php

namespace App\Infrastructure\DaData\Response\Denormalizer;

use App\Core\Domain\Address\Dto\AddressDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DaDataSuggestAddressDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): ?AddressDto
    {
        $address = reset($data);

        return new AddressDto(
            $address['unrestricted_value'],
            $address['data']['postal_code'],
            $address['data']['country'],
            $address['data']['country_iso_code'],
            $address['data']['region'],
            $address['data']['region_iso_code'],
            $address['data']['city'] ?? $address['data']['area'],
            $address['data']['city_type_full'] ?? $address['data']['area_type_full'],
            $address['data']['street_with_type'],
            $address['data']['settlement_with_type'],
            $address['data']['house'],
            $address['data']['entrance'],
            $address['data']['floor'],
            $address['data']['flat'],
            $address['data']['geo_lat'],
            $address['data']['geo_lon']
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === AddressDto::class
            && !empty($data)
            && is_array($data)
            && array_key_exists('unrestricted_value', reset($data));
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}