<?php

declare(strict_types=1);

namespace App\Sites;

use App\Sessions\Session;
use App\Sites\Exceptions\SiteException;
use App\Sites\Input\SiteField;
use App\Sites\Input\SiteFieldType;
use App\Sites\Input\SiteInput;
use App\Sites\TemplatesEngine\Page;

abstract class SiteHandler
{
    private function __construct()
    {
    }

    public static function hasPagination(): bool
    {
        return false;
    }

    /**
     * @return SiteField[]
     */
    public static function getInputFields(): array
    {
        return [];
    }

    final public static function getAllInputFields(): array
    {
        $input_fields = static::getInputFields();

        if (static::hasPagination()) {
            $input_fields['page'] = new SiteField(SiteFieldType::Integer, false, description: 'Номер страницы');
            $input_fields['ord'] = new SiteField(SiteFieldType::String, false, description: 'Поле сортировки');
            $input_fields['sort'] = new SiteField(SiteFieldType::String, false, description: 'Направление сортировки');
        }

        return $input_fields;

    }

    public static function getRequiredPermissions(): array
    {
        return [];
    }

    /**
     * @return string[][]
     */
    public static function getPhrases(): array
    {
        return [];
    }

    /**
     * @throws SiteException
     */
    abstract public static function handleRequest(SiteInput $input, Session $session): ?Page;
}