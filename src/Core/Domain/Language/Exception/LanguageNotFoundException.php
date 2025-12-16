<?php

namespace App\Core\Domain\Language\Exception;

class LanguageNotFoundException extends \Exception
{
    protected $code = 404; // 404 Not found

}