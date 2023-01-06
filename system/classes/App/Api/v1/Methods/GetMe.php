<?php

declare(strict_types=1);

namespace App\Api\v1\Methods;

use App\Api\v1\ApiMethodMutable;
use App\Api\v1\Input\ApiInput;
use App\Api\v1\Models\SessionInfoApiModel;
use App\Api\v1\Output\ApiField;
use App\Sessions\Session;

class GetMe extends ApiMethodMutable
{
    public static function getDescription(): ?string
    {
        return 'Подробная информация о текущем посетителе';
    }

    public static function handle(ApiInput $input, Session $session): SessionInfoApiModel
    {
        return new SessionInfoApiModel($session);
    }

    public static function getResultField(): ?ApiField
    {
        return ApiField::object(SessionInfoApiModel::class, false);
    }
}