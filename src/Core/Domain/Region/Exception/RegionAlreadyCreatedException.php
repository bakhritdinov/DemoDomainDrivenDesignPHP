<?php

namespace App\Core\Domain\Region\Exception;

class RegionAlreadyCreatedException extends \Exception
{
    protected $code = 409; // 409 Conflict

}