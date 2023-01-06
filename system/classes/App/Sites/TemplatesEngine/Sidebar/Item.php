<?php

declare(strict_types=1);

namespace App\Sites\TemplatesEngine\Sidebar;

use RuntimeException;

class Item
{
    public string $name;
    public string $code;
    public string $icon;
    public bool $active;
    public ?string $path;

    /**
     * @var Subitem[]
     */
    public array $subitems = [];

    /**
     * @param Subitem[] $subitems
     */
    public function __construct(
        string $name,
        string $code,
        string $icon,
        bool $active,
        array $subitems = [],
        string $path = null,
    )
    {
        if ($subitems === [] && $path === '') {
            throw new RuntimeException('Empty sidebar item');
        }

        if (!(($subitems === []) xor ($path === null))) {
            throw new RuntimeException('Sidebar item must have subitems or path');
        }

        $this->name = $name;
        $this->code = $code;
        $this->icon = $icon;
        $this->active = $active;
        $this->subitems = $subitems;
        $this->path = $path;
    }
}