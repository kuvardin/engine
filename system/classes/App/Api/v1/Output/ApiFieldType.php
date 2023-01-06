<?php

declare(strict_types=1);

namespace App\Api\v1\Output;

enum ApiFieldType: string
{
    case String = 'string';
    case Integer = 'int';
    case Float = 'float';
    case Boolean = 'bool';
    case Object = 'object';
    case Timestamp = 'timestamp';
    case Phrase = 'phrase';
    case Array = 'array';

    public function isScalar(): bool
    {
        return $this !== self::Array && $this !== self::Object;
    }
}