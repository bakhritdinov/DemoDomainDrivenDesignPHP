<?php

namespace App\Core\CommonService\Sluggable\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Sluggable
{
    private string $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }
}