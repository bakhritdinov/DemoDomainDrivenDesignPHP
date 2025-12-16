<?php

namespace App\Core\Domain\Currency\Exception;

class CurrencyNotFoundException extends \Exception
{
    protected $code = 404; // 404 Not found

}