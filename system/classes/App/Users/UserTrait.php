<?php

declare(strict_types=1);

namespace App\Users;

trait UserTrait
{
    protected ?int $user_id;

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getUser(): ?User
    {
        return $this->user_id === null ? null : User::requireById($this->user_id);
    }
}