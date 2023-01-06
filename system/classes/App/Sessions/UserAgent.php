<?php

declare(strict_types=1);

namespace App\Sessions;

class UserAgent
{
    readonly public string $value;
    protected ?OperationSystem $operation_system = null;
    protected ?WebBot $web_bot = null;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function searchOperationSystem(string $user_agent): ?OperationSystem
    {
        if (preg_match('|^[^\(]+\(([^)]+)\)|u', $user_agent, $match)) {
            switch (true) {
                case str_starts_with($match[1], 'iPhone'):
                case str_starts_with($match[1], 'iPad'):
                    return OperationSystem::IOS;

                case str_starts_with($match[1], 'Macintosh'):
                    return OperationSystem::MacOS;

                case str_starts_with($match[1], 'Windows'):
                    return OperationSystem::Windows;

                case str_contains($match[1], 'Android'):
                    return OperationSystem::Android;

                case str_starts_with($match[1], 'X11; Ubuntu'):
                case str_starts_with($match[1], 'X11; Linux'):
                    return OperationSystem::Linux;
            }
        }

        return null;
    }

    public function getOperationSystem(): ?OperationSystem
    {
        return $this->operation_system ??= self::searchOperationSystem($this->value);
    }

    public function getWebBot(): ?WebBot
    {
        return $this->web_bot ??= WebBot::makeByUserAgent($this->value);
    }

    public function isWebBot(): bool
    {
        return $this->getWebBot() !== null;
    }
}