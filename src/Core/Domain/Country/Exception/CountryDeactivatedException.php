<?php

namespace App\Core\Domain\Country\Exception;

class CountryDeactivatedException extends \Exception
{
    protected $code = 422; // 422 Unprocessable Entity

}