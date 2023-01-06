<?php

declare(strict_types=1);

namespace App\Api\v1\Methods\Users;

use App\Api\v1\ApiMethodMutable;
use App\Api\v1\Exceptions\ApiException;
use App\Api\v1\Input\ApiInput;
use App\Api\v1\Input\ApiParameter;
use App\Api\v1\Input\ApiParameterType;
use App\Api\v1\Models\UserApiModel;
use App\Api\v1\Output\ApiField;
use App\Sessions\Session;
use App\Users\User;

class GetUser extends ApiMethodMutable
{
    public static function getDescription(): ?string
    {
        return 'Возвращает данные о пользователе';
    }

    public static function getParameters(): array
    {
        return [
            'id' => ApiParameter::scalar(ApiParameterType::Integer, 2004, 'ID пользователя'),
        ];
    }

    public static function getResultField(): ?ApiField
    {
        return ApiField::object(UserApiModel::class, false, 'Пользователь');
    }

    public static function handle(ApiInput $input, Session $session): UserApiModel
    {
        $user = User::makeById($input->requireInt('id'));
        $user !== null ?: throw new ApiException(2004);
        return new UserApiModel($user);
    }
}