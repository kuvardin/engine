<?php

declare(strict_types=1);

namespace App\Api\v1\Models;

use App\Api\v1\ApiModelMutable;
use App\Api\v1\Output\ApiField;
use App\Api\v1\Output\ApiFieldType;
use App\Sessions\Session;

class SessionInfoApiModel extends ApiModelMutable
{
    protected Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public static function getDescription(): ?string
    {
        return 'Информация о текущем посетителе. Нужно запрашивать перед началом работы с API';
    }

    public static function getFields(): array
    {
        return [
            'session' => ApiField::object(SessionApiModel::class, false,
                description: 'Сессия посетителя'),
            'session_id' => ApiField::scalar(ApiFieldType::String, false,
                description: 'Секретный код сессии, который нужно передавать в каждом запросе'),
            'user' => ApiField::object(UserApiModel::class, true,
                description: 'Информация о пользователе, если посетитель авторизован'),
            'currency' => ApiField::object(CurrencyApiModel::class, false,
                description: 'Валюта'),
        ];
    }

    public function getPublicData(Session $session): array
    {
        $user = $this->session->getUser();
        return [
            'session' => new SessionApiModel($this->session),
            'session_id' => $this->session->getSecretCode(),
            'user' => $user === null ? null : new UserApiModel($user),
            'currency' => new CurrencyApiModel($this->session->getCurrency()),
        ];
    }
}