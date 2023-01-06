<?php

declare(strict_types=1);

namespace App\Api\v1;

use App\Api\v1\Input\ApiParameter;
use App\Api\v1\Output\ApiField;
use App\Languages\Language;

abstract class ApiMethod
{
    private function __construct()
    {
    }

    /**
     * @return ApiParameter[]
     */
    protected static function getParameters(): array
    {
        return [];
    }

    final static function getAllParameters(Language $language): array
    {
        $parameters = static::getParameters();

        if (static::getSelectionOptions($language) !== null) {
            $parameters = array_merge($parameters, ApiSelectionOptions::getApiParameters());
        }

        return $parameters;
    }

    /**
     * @return ApiSelectionOptions|null Только если в методе используется пагинация
     */
    public static function getSelectionOptions(Language $language): ?ApiSelectionOptions
    {
        return null;
    }

    public static function getDescription(): ?string
    {
        return null;
    }

    abstract public static function getResultField(): ?ApiField;

    abstract public static function isMutable(): bool;
}