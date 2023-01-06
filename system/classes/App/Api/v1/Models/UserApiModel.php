<?php

declare(strict_types=1);

namespace App\Api\v1\Models;

use App\Api\v1\ApiModelMutable;
use App\Api\v1\Output\ApiField;
use App\Api\v1\Output\ApiFieldType;
use App\Sessions\Session;
use App\Users\User;

class UserApiModel extends ApiModelMutable
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public static function getDescription(): ?string
    {
        return 'Пользователь системы';
    }

    public static function getFields(): array
    {
        return [
            'id' => ApiField::scalar(ApiFieldType::Integer, false, description: 'ID пользователя'),
            'first_name' => ApiField::scalar(ApiFieldType::String, false, description: 'Имя'),
            'last_name' => ApiField::scalar(ApiFieldType::String, true, description: 'Фамилия'),
            'middle_name' => ApiField::scalar(ApiFieldType::String, true, description: 'Отчество'),
            'creation_date' => ApiField::scalar(ApiFieldType::Timestamp, false, description: 'Дата регистрации'),
        ];
    }

    public function getPublicData(Session $session): array
    {
        return [
            'id' => $this->user->getId(),
            'first_name' => $this->user->getFirstName(),
            'last_name' => $this->user->getLastName(),
            'middle_name' => $this->user->getMiddleName(),
            'creation_date' => $this->user->getCreationDate(),
        ];
    }
}