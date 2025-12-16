<?php

namespace App\Core\Domain\Country\Exception;

class CountryAlreadyCreatedException extends \Exception
{
    protected $code = 409; // 409 Conflict

}