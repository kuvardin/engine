<?php

declare(strict_types=1);

namespace App\Sites\TemplatesEngine;

class Alert
{
    readonly public string $text;
    readonly public ?BootstrapColor $bootstrap_color;

    /**
     * @var bool Нужно ли пропускать текст через htmlspecialchars() перед выводом?
     */
    readonly public bool $filter_text;

    public function __construct(string $text, ?BootstrapColor $bootstrap_color, bool $filter_text = true)
    {
        $this->text = $text;
        $this->bootstrap_color = $bootstrap_color;
        $this->filter_text = $filter_text;
    }
}