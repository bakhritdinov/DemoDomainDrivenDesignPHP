<?php

namespace App\Core\Domain\Country\Exception;

class CountryNotFoundException extends \Exception
{
    protected $code = 404; // 404 Not found

}