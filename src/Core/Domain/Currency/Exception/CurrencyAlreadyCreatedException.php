<?php

namespace App\Core\Domain\Currency\Exception;

class CurrencyAlreadyCreatedException extends \Exception
{
    protected $code = 409; // 409 Conflict

}