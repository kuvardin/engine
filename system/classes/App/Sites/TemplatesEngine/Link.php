<?php

declare(strict_types=1);

namespace App\Sites\TemplatesEngine;

class Link
{
    public string $title;
    public string $path;
    public ?BootstrapColor $bootstrap_color = null;
    public ?int $counter;
    public ?string $icon;

    public function __construct(
        string $title,
        string $path,
        BootstrapColor $bootstrap_color = null,
        int $counter = null,
        string $icon = null,
    )
    {
        $this->title = $title;
        $this->path = $path;
        $this->bootstrap_color = $bootstrap_color;
        $this->counter = $counter;
        $this->icon = $icon;
    }
}