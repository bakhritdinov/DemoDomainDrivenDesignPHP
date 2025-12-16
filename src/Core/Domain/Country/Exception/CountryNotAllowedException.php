<?php

namespace App\Core\Domain\Country\Exception;

class CountryNotAllowedException extends \Exception
{
    protected $code = 422; // 422 Unprocessable Entity

}