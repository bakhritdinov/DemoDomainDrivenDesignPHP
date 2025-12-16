<?php

namespace App\Core\Domain\City\Exception;

class CityAlreadyCreatedException extends \Exception
{
    protected $code = 409; // 409 Conflict

}