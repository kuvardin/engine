<?php

declare(strict_types=1);

namespace App\Sites;

use App;
use App\Sessions\Session;
use App\Sites\Sections\Store\StoreSectionController;
use Kuvardin\FastMysqli\Exceptions\AlreadyExists;

abstract class SiteController
{
    private function __construct()
    {
    }

    public static function handle(
        array $route_parts,
        array $get,
        array $post,
        array $cookies,
        array $files,
        ?string $input_string,
        string $ip,
        ?string $user_agent
    ): void
    {
        // Пытаемся получить код языка из URL PATH
        $lang_code = !empty($route_parts[0]) && in_array($route_parts[0], App::settings('languages'), true)
            ? $route_parts[0]
            : null;

        $lang_code_cookie_name = App::settings('cookies.names.lang_code');

        // Если URL не содержит код языка, перенаправляем
        if ($lang_code === null) {
            $lang_code = !empty($cookies[$lang_code_cookie_name]) &&
                in_array($cookies[$lang_code_cookie_name], App::settings('languages'), true)
                ? $cookies[$lang_code_cookie_name]
                : App::settings('language.default');

            $redirect_url = '/' . $lang_code . '/'. implode('/', $route_parts) .
                ($get === [] ? '' : ('?' . http_build_query($get)));

            header('Status: 302');
            header('Location: ' . $redirect_url);
            return;
        }

        if (empty($cookies[$lang_code_cookie_name]) || $cookies[$lang_code_cookie_name] !== $lang_code) {
            App::setCookie($lang_code_cookie_name, $lang_code);
        }

        array_shift($route_parts);

        Sections\Main\MainSectionController::handle($lang_code, $route_parts, $get, $post, $cookies,
            $files, $input_string, $ip, $user_agent);
    }
}