<?php

declare(strict_types=1);

namespace App\Cli\Commands;

use App\Cli\CliCommand;
use App\Cli\Input\CliInput;
use App\Cli\Output\CliOutput;

class Ping extends CliCommand
{
    public static function execute(CliInput $input): int
    {
        CliOutput::message('Pong', true);
        return 0;
    }
}