<?php

declare(strict_types=1);

namespace App\Api\v1\Methods;

use App\Api\v1\ApiMethodMutable;
use App\Api\v1\Exceptions\ApiException;
use App\Api\v1\Input\ApiInput;
use App\Api\v1\Input\ApiParameter;
use App\Api\v1\Models\AuthorizationInfoApiModel;
use App\Api\v1\Output\ApiField;
use App\Sessions\Authorization as AuthorizatioBaseModel;
use App\Sessions\JwtPayload;
use App\Sessions\Session;
use App\Users\User;

class Authorization extends ApiMethodMutable
{
    public static function getDescription(): ?string
    {
        return 'Авторизация';
    }

    protected static function getParameters(): array
    {
        return [
            'email' => ApiParameter::string(3002, 'Email'),
            'password' => ApiParameter::string(3004),
        ];
    }

    public static function getResultField(): ?ApiField
    {
        return ApiField::object(AuthorizationInfoApiModel::class, false);
    }

    public static function handle(ApiInput $input, Session $session): AuthorizationInfoApiModel
    {
        if ($session->isAuthorized()) {
            throw new ApiException(2003);
        }

        $email = $input->requireString('email');
        $password = $input->requireString('password');

        if (!User::checkEmailValidity($email)) {
            throw new ApiException(3006);
        }

        $user = User::makeByEmail($email);
        if ($user === null || !$user->checkPassword($password)) {
            throw new ApiException(3012);
        }

        $authorization = AuthorizatioBaseModel::create(
            $session,
            $user,
            null,
            $session->getIp(),
            $session->getUserAgentValue(),
        );

        $session->setUser($user);
        $session->save();

        return new AuthorizationInfoApiModel(
            session: $session,
            jwt_access_token: JwtPayload::getAccessToken($authorization),
            jwt_refresh_token: JwtPayload::getRefreshToken($authorization),
        );
    }
}