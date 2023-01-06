<?php

declare(strict_types=1);

namespace App\Sites\Input;

class SiteField
{
    readonly public SiteFieldType $type;
    readonly public bool $from_post;
    readonly public ?string $description;

    public function __construct(SiteFieldType $type, bool $from_post = false, string $description = null)
    {
        $this->type = $type;
        $this->from_post = $from_post;
        $this->description = $description;
    }
}