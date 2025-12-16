<?php

namespace App\Tests\Infrastructure\DaData\Service;

use App\Core\Domain\Address\Dto\AddressDto;
use App\Infrastructure\DaData\Service\FindBySuggestAddressService;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FindByAddressServiceTest extends KernelTestCase
{
    public function testFindByAddress()
    {
        $service = $this->createMock(FindBySuggestAddressService::class);
        $service->method('find')->willReturn(DaDataAddressDtoFixture::getOne());

        $response = $service->find('Белгородская обл, г Алексеевка, ул Слободская, д 1/1');

        $this->assertNotNull($response);
        $this->assertInstanceOf(AddressDto::class, $response);
    }
}