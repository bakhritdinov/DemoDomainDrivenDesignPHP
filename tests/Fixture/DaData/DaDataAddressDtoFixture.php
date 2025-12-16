<?php

namespace App\Tests\Fixture\DaData;

use App\Core\Domain\Address\Dto\AddressDto;

class DaDataAddressDtoFixture
{
    public static function getOne(): AddressDto
    {
        return new AddressDto(
            '309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1',
            '309850',
            'Россия',
            'RU',
            'Белгородская',
            'RU-BEL',
            'Алексеевка',
            'город',
            'ул Слободская',
            '',
            '1/1',
            null,
            null,
            null,
            12.122133,
            32.333333
        );
    }
}