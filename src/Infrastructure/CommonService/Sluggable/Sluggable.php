<?php

namespace App\Infrastructure\CommonService\Sluggable;

use App\Core\CommonService\Sluggable\SluggableInterface;
use Cocur\Slugify\SlugifyInterface;

class Sluggable implements SluggableInterface
{
    public function __construct(public SlugifyInterface $slugify)
    {
    }

    public function make(string $field): string
    {
        return $this->slugify->slugify($field);
    }
}