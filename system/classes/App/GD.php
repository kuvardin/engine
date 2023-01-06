<?php

declare(strict_types=1);

namespace App;

use GdImage;
use RuntimeException;

/**
 * Class GD
 *
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class GD
{
    /**
     * @param GdImage|resource $image
     * @param int $size
     * @param float $angle
     * @param int $x
     * @param int $y
     * @param int $color
     * @param string $font_filename
     * @param string $text
     * @param int $line_spacing
     * @return void
     */
    public static function addText(mixed $image, int $size, float $angle, int $x, int $y, int $color,
        string $font_filename, string $text, int $line_spacing = 0): void
    {
        $text_rows = explode("\n", $text);
        $font_height = null;

        foreach ($text_rows as $text_row) {
            if ($font_height === null) {
                $ttfbox = imagettfbbox($size, $angle, $font_filename, 'TEST');
                $font_height = abs($ttfbox[5]);
            }

            imagettftext($image, $size, $angle, $x, $y, $color, $font_filename, $text_row);
            $y += $font_height + $line_spacing;
        }
    }

    /**
     * @param GdImage|resource $image
     * @param string $source_image_path
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param bool $add_rectangle
     * @return void
     */
    public static function addImage(mixed $image, string $source_image_path, int $x1, int $y1, int $x2, int $y2,
        bool $add_rectangle = false): void
    {
        if ($add_rectangle) {
            imagerectangle($image, $x1, $y1, $x2, $y2, imagecolorallocate($image, 0, 0, 0));
        }

        $source_image_size = getimagesize($source_image_path);
        $source_image_width = $source_image_size[0];
        $source_image_height = $source_image_size[1];

        $max_width = $x2 - $x1 + 1;
        $max_height = $y2 - $y1 + 1;

        $xr = $max_width / $source_image_width;
        $yr = $max_height / $source_image_height;
        $r = min($xr, $yr, 1);

        $new_width = (int)round($source_image_width * $r);
        $new_height = (int)round($source_image_height * $r);

        $x = $x1 + (int)(($max_width - $new_width) / 2);
        $y = $y1 + (int)(($max_height - $new_height) / 2);

        $source_image = self::imageCreateFrom($source_image_path);

        imagecopyresampled($image, $source_image, $x, $y, 0, 0, $new_width, $new_height, $source_image_width,
            $source_image_height);
    }

    /**
     * @param string $text
     * @param float $font_size
     * @param string $font_filename
     * @param int $max_width
     * @return string
     */
    public static function getTextRows(string $text, float $font_size, string $font_filename, int $max_width): string
    {
        $result = '';

        $arr = explode(' ', $text);
        foreach ($arr as $word) {
            $tmp_string = $result . ' ' . $word;
            $textbox = imagettfbbox($font_size, 0, $font_filename, $tmp_string);
            if ($textbox[2] > $max_width) {
                $result .= ($result === '' ? '' : "\n") . $word;
            } else {
                $result .= ($result === '' ? '' : ' ') . $word;
            }
        }

        return $result;
    }

    /**
     * @param string $image_path
     * @return GdImage|resource|false
     */
    public static function imageCreateFrom(string $image_path): mixed
    {
        $mime = mime_content_type($image_path);
        switch ($mime) {
            case 'image/jpg':
            case 'image/jpeg':
                return imagecreatefromjpeg($image_path);

            case 'image/png':
                return imagecreatefrompng($image_path);

            case 'image/bmp':
            case 'image/x-ms-bmp':
                return imagecreatefrombmp($image_path);

            case 'image/gif':
                return imagecreatefromgif($image_path);

            case 'image/webp':
                return imagecreatefromwebp($image_path);

            default:
                throw new RuntimeException("Unknown MIME-type: $mime ($image_path)");
        }
    }
}