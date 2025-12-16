<?php

namespace App\Core\Domain\Region\Exception;

class RegionDeactivatedException extends \Exception
{
    protected $code = 422; // 422 Unprocessable Entity

}