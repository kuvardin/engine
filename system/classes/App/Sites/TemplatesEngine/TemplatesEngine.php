<?php

namespace App\Sites\TemplatesEngine;

use Throwable;

class TemplatesEngine
{
    public static function render(string $template_path, array $data): string
    {
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        try {
            ob_start();
            require TEMPLATES_DIR . "/$template_path.php";
            return ob_get_clean();
        } catch (Throwable $exception) {
            ob_get_clean();
            throw $exception;
        }
    }
}