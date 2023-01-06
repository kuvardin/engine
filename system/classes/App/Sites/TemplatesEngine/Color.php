<?php

declare(strict_types=1);

namespace App\Sites\TemplatesEngine;

use RuntimeException;

/**
 * Class Color
 *
 * @package App\TemplatesEngine
 */
class Color
{
    public int $red;
    public int $green;
    public int $blue;

    protected const READY_COLORS = [
        '#3D8EB9',
        '#92F22A',
        '#F04903',
        '#83D6DE',
        '#EE543A',
        '#8870FF',
    ];

    public function __construct(int $red, int $green, int $blue)
    {
        if ($red < 0 || $red > 255 || $green < 0 || $green > 255 || $blue < 0 || $blue > 255) {
            throw new RuntimeException("Incorrect rgb color: $red, $green, $blue");
        }

        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
    }

    public static function getReady(int $index = null): self
    {
        $index ??= 0;
        $ready_colors_count = count(self::READY_COLORS);
        $index = abs($index) % $ready_colors_count;
        return self::fromHtml(self::READY_COLORS[$index]);
    }

    public static function fromHtml(string $html_color): self
    {
        if (!preg_match('|^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$|u', $html_color, $match)) {
            throw new RuntimeException("Incorrect HTML-color: $html_color");
        }

        return new self(hexdec($match[1]), hexdec($match[2]), hexdec($match[3]));
    }

    public function getHtmlString(): string
    {
        return sprintf('#%02x%02x%02x', $this->red, $this->green, $this->blue);
    }

    public function getRgbString(): string
    {
        return "rgb({$this->red},{$this->green},{$this->blue})";
    }

    public function getGradientTo(self $another_color, float $percents): self
    {
        return new self(
            (int)($this->red + ($another_color->red - $this->red) * $percents),
            (int)($this->green + ($another_color->green - $this->green) * $percents),
            (int)($this->blue + ($another_color->blue - $this->blue) * $percents)
        );
    }
}