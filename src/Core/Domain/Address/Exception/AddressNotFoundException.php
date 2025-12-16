<?php

namespace App\Core\Domain\Address\Exception;

class AddressNotFoundException extends \Exception
{
    protected $code = 404; // 404 Not found

}