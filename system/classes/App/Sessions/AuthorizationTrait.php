<?php

declare(strict_types=1);

namespace App\Sessions;

trait AuthorizationTrait
{
    protected ?int $sessions_authorization_id;

    public function getSessionsAuthorizationId(): ?int
    {
        return $this->sessions_authorization_id;
    }

    public function getSessionsAuthorization(): ?Authorization
    {
        return $this->sessions_authorization_id === null
            ? null
            : Authorization::requireById($this->sessions_authorization_id);
    }
}