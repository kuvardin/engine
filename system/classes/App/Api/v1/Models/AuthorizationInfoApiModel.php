<?php

declare(strict_types=1);

namespace App\Api\v1\Models;

use App\Api\v1\ApiModelMutable;
use App\Api\v1\Output\ApiField;
use App\Api\v1\Output\ApiFieldType;
use App\Sessions\Session;

class AuthorizationInfoApiModel extends ApiModelMutable
{
    protected Session $session;
    protected string $jwt_access_token;
    protected string $jwt_refresh_token;

    public function __construct(Session $session, string $jwt_access_token, string $jwt_refresh_token)
    {
        $this->session = $session;
        $this->jwt_access_token = $jwt_access_token;
        $this->jwt_refresh_token = $jwt_refresh_token;
    }

    public static function getFields(): array
    {
        return [
            'session_info' => ApiField::object(SessionInfoApiModel::class, false, 'Информация о сессии'),
            'access_token' => ApiField::scalar(ApiFieldType::String, false, 'JWT access token'),
            'refresh_token' => ApiField::scalar(ApiFieldType::String, false, 'JWT refresh token'),
        ];
    }

    public static function getDescription(): ?string
    {
        return 'Информация об авторизации';
    }

    public function getPublicData(Session $session): array
    {
        return [
            'session_info' => new SessionInfoApiModel($this->session),
            'access_token' => $this->jwt_access_token,
            'refresh_token' => $this->jwt_refresh_token,
        ];
    }
}