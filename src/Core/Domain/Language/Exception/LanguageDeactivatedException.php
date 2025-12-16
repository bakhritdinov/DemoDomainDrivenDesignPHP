<?php

namespace App\Core\Domain\Language\Exception;

class LanguageDeactivatedException extends \Exception
{
    protected $code = 422; // 422 Unprocessable Entity

}