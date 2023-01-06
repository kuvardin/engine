<?php

declare(strict_types=1);

namespace App\Users\Roles;

trait RoleTrait
{
    protected ?int $users_role_id;

    public function getUsersRoleId(): ?int
    {
        return $this->users_role_id;
    }

    public function getUsersRole(): ?Role
    {
        return $this->users_role_id === null ? null : Role::requireById($this->users_role_id);
    }
}