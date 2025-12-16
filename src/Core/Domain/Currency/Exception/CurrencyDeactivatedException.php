<?php

namespace App\Core\Domain\Currency\Exception;

class CurrencyDeactivatedException extends \Exception
{
    protected $code = 422; // 422 Unprocessable Entity

}