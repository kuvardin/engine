<?php

declare(strict_types=1);

namespace App\Api\v1\Models;

use App\Api\v1\ApiModelMutable;
use App\Api\v1\Output\ApiField;
use App\Api\v1\Output\ApiFieldType;
use App\Sessions\Session;

class SessionApiModel extends ApiModelMutable
{
    protected Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function getPublicData(Session $session): array
    {
        return [
            'last_request_date' => $this->session->getLastRequestDate(),
            'creation_date' => $this->session->getCreationDate(),
        ];
    }

    public static function getFields(): array
    {
        return [
            'last_request_date' => ApiField::scalar(ApiFieldType::Timestamp, false),
            'creation_date' => ApiField::scalar(ApiFieldType::Timestamp, false),
        ];
    }
}