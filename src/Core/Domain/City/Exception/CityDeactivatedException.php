<?php

namespace App\Core\Domain\City\Exception;

class CityDeactivatedException extends \Exception
{
    protected $code = 422; // 422 Unprocessable Entity

}