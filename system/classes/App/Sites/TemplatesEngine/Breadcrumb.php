<?php

declare(strict_types=1);

namespace App\Sites\TemplatesEngine;

class Breadcrumb
{
    readonly public string $name;
    readonly public string $path;
    readonly public ?array $get;

    public function __construct(string $name, string $path, array $get = null)
    {
        $this->name = $name;
        $this->path = $path;
        $this->get = $get;
    }
}