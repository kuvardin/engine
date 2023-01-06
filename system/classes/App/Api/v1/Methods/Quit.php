<?php

declare(strict_types=1);

namespace App\Api\v1\Methods;

use App\Api\v1\ApiMethodMutable;
use App\Api\v1\Exceptions\ApiException;
use App\Api\v1\Input\ApiInput;
use App\Api\v1\Models\SessionInfoApiModel;
use App\Api\v1\Output\ApiField;
use App\Sessions\Authorization;
use App\Sessions\Session;

class Quit extends ApiMethodMutable
{
    public static function getDescription(): ?string
    {
        return 'Выход из аккаунта';
    }

    public static function getResultField(): ?ApiField
    {
        return ApiField::object(SessionInfoApiModel::class, false);
    }

    public static function handle(ApiInput $input, Session $session): SessionInfoApiModel
    {
        $session->isAuthorized() ?: throw new ApiException(2002);
        
        $authorizations = Authorization::getSelection(
            null,
            Authorization::getFilters(false, $session->getId(), $session->getUserId()),
        );
        
        $session->setUser(null);
        $session->save();

        foreach ($authorizations as $authorization) {
            $authorization->delete($session);
        }

        return new SessionInfoApiModel($session);

    }
}