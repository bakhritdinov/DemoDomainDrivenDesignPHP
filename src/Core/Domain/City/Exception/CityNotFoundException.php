<?php

namespace App\Core\Domain\City\Exception;

class CityNotFoundException extends \Exception
{
    protected $code = 404; // 404 Not found

}