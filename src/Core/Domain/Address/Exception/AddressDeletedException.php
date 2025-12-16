<?php

namespace App\Core\Domain\Address\Exception;

class AddressDeletedException extends \Exception
{
    protected $code = 404; // 404 Not found

}