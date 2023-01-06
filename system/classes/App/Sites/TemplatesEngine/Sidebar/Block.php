<?php

declare(strict_types=1);

namespace App\Sites\TemplatesEngine\Sidebar;

/**
 * Блок ссылок сайдбара
 */
class Block
{
    /**
     * @var string Наименование
     */
    public string $name;

    /**
     * @var Item[] Ссылки
     */
    public array $items = [];

    /**
     * @param Item[] $items
     */
    public function __construct(string $name, array $items)
    {
        $this->name = $name;
        $this->items = $items;
    }
}