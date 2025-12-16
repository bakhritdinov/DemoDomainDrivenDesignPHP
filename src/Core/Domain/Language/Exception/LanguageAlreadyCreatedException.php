<?php

namespace App\Core\Domain\Language\Exception;

class LanguageAlreadyCreatedException extends \Exception
{
    protected $code = 409; // 409 Conflict

}