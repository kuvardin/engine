<?php

declare(strict_types=1);

namespace App\Api\v1\Methods;

use App\Api\v1\ApiMethodMutable;
use App\Api\v1\Exceptions\ApiException;
use App\Api\v1\Input\ApiInput;
use App\Api\v1\Input\ApiParameter;
use App\Api\v1\Models\AuthorizationInfoApiModel;
use App\Api\v1\Output\ApiField;
use App\Sessions\JwtPayload;
use App\Sessions\Session;
use Firebase\JWT\ExpiredException;
use App\Sessions\Authorization;
use UnexpectedValueException;

class RefreshAuthorization extends ApiMethodMutable
{
    public static function getDescription(): ?string
    {
        return 'Обновление авторизации';
    }

    public static function getResultField(): ?ApiField
    {
        return ApiField::object(AuthorizationInfoApiModel::class, false);
    }

    protected static function getParameters(): array
    {
        return [
            'refresh_token' => ApiParameter::string(3013, 'Действительный refresh token'),
        ];
    }

    public static function handle(ApiInput $input, Session $session): AuthorizationInfoApiModel
    {
        if (!$session->isAuthorized()) {
            throw new ApiException(2002);
        }

        $refresh_token = $input->requireString('refresh_token');

        try {
            $jwt_payload = JwtPayload::make($refresh_token);
        } catch (ExpiredException) {
            throw new ApiException(1004);
        } catch (UnexpectedValueException) {
            throw new ApiException(1003);
        }

        if ($jwt_payload === null || $jwt_payload->type !== JwtPayload::TYPE_REFRESH) {
            throw new ApiException(1003);
        }

        $old_authorization = $jwt_payload->getSessionsAuthorization();
        if ($old_authorization->isDeleted()) {
            throw new ApiException(1004);
        }

        if ($jwt_payload->getUserId() !== $session->getUserId()) {
            throw new ApiException(1004);
        }

        $authorization = Authorization::create(
            $session,
            $old_authorization->getUserId(),
            $old_authorization,
            $session->getIp(),
            $session->getUserAgentValue(),
        );

        $old_authorization->delete($session);

        return new AuthorizationInfoApiModel(
            session: $session,
            jwt_access_token: JwtPayload::getAccessToken($authorization),
            jwt_refresh_token: JwtPayload::getRefreshToken($authorization),
        );
    }
}