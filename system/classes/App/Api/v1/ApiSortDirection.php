<?php

declare(strict_types=1);

namespace App\Api\v1;

use Kuvardin\FastMysqli\SelectionData;

enum ApiSortDirection: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';

    public static function make(string $sort_direction): ?self
    {
        return self::tryFrom(strtoupper($sort_direction));
    }
}