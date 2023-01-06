<?php

declare(strict_types=1);

namespace App\Api\v1\Models;

use App\Actions\Action;
use App\Api\v1\ApiModelMutable;
use App\Api\v1\Output\ApiField;
use App\Api\v1\Output\ApiFieldType;
use App\Sessions\Session;

class ActionApiModel extends ApiModelMutable
{
    protected Action $action;

    public function __construct(Action $action)
    {
        $this->action = $action;
    }

    public static function getDescription(): ?string
    {
        return 'Информация о действии';
    }

    public static function getFields(): array
    {
        return [
            'id' => ApiField::scalar(ApiFieldType::Integer, false, 'ID действия'),
            'session_id' => ApiField::scalar(ApiFieldType::Integer, true, 'ID сессии'),
            'user' => ApiField::object(UserApiModel::class, true, 'Пользователь'),
            'type' => ApiField::scalar(ApiFieldType::Integer, false, 'Тип действия'),
            'ip' => ApiField::scalar(ApiFieldType::String, true, 'IP-адрес'),
            'user_agent' => ApiField::scalar(ApiFieldType::String, true, 'User-Agent'),
            'creation_date' => ApiField::scalar(ApiFieldType::Timestamp, false, 'Дата создания'),
        ];
    }

    public function getPublicData(Session $session): array
    {
        return [
            'id' => $this->action->getId(),
            'session_id' => $this->action->getSessionId(),
            'user' => $this->action->getUser() === null
                ? null
                : new UserApiModel($this->action->getUser()),
            'type' => $this->action->getType(),
            'ip' => $this->action->getIp(),
            'user_agent' => $this->action->getUserAgent(),
            'creation_date' => $this->action->getCreationDate(),
        ];
    }
}