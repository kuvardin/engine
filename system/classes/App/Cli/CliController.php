<?php

namespace App\Cli;

use App;
use App\Cli\Input\CliInput;
use App\Cli\Output\CliOutput;
use App\Logger;
use App\Telegram\Notifier;
use Throwable;

class CliController
{
    private function __construct()
    {
    }

    public static function handle(array $argv): int
    {
        array_shift($argv);

        $command_name = array_shift($argv);
        $command_class = self::getCommandClass($command_name);
        if (!class_exists($command_class)) {
            CliOutput::error("Command \"$command_name\" not found");
            return 1;
        }

        if ($command_class::requireMysqliConnection()) {
            App::connectMysqli();
        }

        try {
            $input = new CliInput($argv, $command_class::getParameters());
            return $command_class::execute($input);
        } catch (Throwable $exception) {
            CliOutput::exception($exception, true);
            Logger::writeException($exception);
            Notifier::tryToSendException($exception);
        }

        return 1;
    }

    private static function getCommandClass(string $command_name): string|CliCommand
    {
        $result_parts = [];
        $command_route = explode('/', $command_name);
        foreach ($command_route as $command_route_part) {
            $words = explode('-', $command_route_part);
            $result_parts[] = implode('', array_map(static fn($word) => ucfirst($word), $words));
        }

        return "App\\Cli\\Commands\\" . implode('\\', $result_parts);
    }
}