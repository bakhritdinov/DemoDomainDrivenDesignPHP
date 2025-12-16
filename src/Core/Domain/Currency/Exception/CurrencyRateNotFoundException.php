<?php

namespace App\Core\Domain\Currency\Exception;

class CurrencyRateNotFoundException extends \Exception
{
    protected $code = 404; // 404 Not found

}