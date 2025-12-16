<?php

namespace App\Core\Domain\Address\Exception;

class AddressDeactivatedException extends \Exception
{
    protected $code = 422; // 422 Unprocessable Entity

}