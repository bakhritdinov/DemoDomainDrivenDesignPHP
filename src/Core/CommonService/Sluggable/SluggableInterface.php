<?php

namespace App\Core\CommonService\Sluggable;

interface SluggableInterface
{
    public function make(string $field): string;
}