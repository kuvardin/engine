<?php

declare(strict_types=1);

namespace App\Api;

use App;

class ApiController
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
        $version = array_shift($route_parts);
        if ($version === null || $version === '') {
            http_response_code(404);
            return;
        }

        $version_controller_class = self::getVersionControllerClass($version);
        if (!class_exists($version_controller_class)) {
            http_response_code(404);
            return;
        }

        $version_controller_class::handle($route_parts, $get, $post, $cookies, $files, $input_string, $ip, $user_agent);
    }

    private static function getVersionControllerClass(string $version): string|ApiVersionController
    {
        return "App\\Api\\$version\\ApiVersionController";
    }
}