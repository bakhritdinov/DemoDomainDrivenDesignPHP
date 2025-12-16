<?php

namespace App\Core\Domain\Address\Exception;

class AddressAlreadyCreatedException extends \Exception
{
    protected $code = 409; // 409 Conflict

}