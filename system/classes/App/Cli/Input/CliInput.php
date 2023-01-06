<?php

declare(strict_types=1);

namespace App\Cli\Input;

class CliInput
{
    readonly public array $argv;

    /**
     * @var CliParameter[]
     */
    readonly protected array $parameters;

    protected array $input_data;

    /**
     * @param CliParameter[] $parameters
     */
    public function __construct(array $argv, array $parameters)
    {
        $this->argv = $argv;
        $this->parameters = $parameters;

        $index = 0;
        foreach ($this->argv as $arg_value) {
            if (preg_match('|^\-(\w+)=(.+)$|sui', $arg_value, $match)) {
                $parameter_name = $match[1];
                $parameter_value = $match[2];

            } else {

                $index++;
            }
        }
    }

    public function getArgument(int $index): ?string
    {
        return $this->argv[$index] ?? null;
    }
}