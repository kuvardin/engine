<?php

declare(strict_types=1);

namespace App\Api\v1\Input;

enum ApiParameterType: string
{
    case String = 'string';
    case Integer = 'int';
    case Float = 'float';
    case Boolean = 'bool';
    case Phrase = 'phrase';
    case DateTime = 'date_time';
    case Date = 'date';
    case Array = 'array';

    public function isScalar(): bool
    {
        return $this !== self::Array;
    }
}