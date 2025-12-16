<?php

namespace App\Core\Domain\Region\Exception;

class RegionNotFoundException extends \Exception
{
    protected $code = 404; // 404 Not found

}