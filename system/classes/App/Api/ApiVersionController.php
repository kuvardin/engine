<?php

declare(strict_types=1);

namespace App\Api;

abstract class ApiVersionController
{
    private function __construct()
    {
    }

    abstract public static function handle(
        array $route_parts,
        array $get,
        array $post,
        array $cookies,
        array $files,
        ?string $input_string,
        string $ip,
        ?string $user_agent,
    ): void;
}