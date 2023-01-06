<?php

declare(strict_types=1);

namespace App\Users\Roles;

trait RoleRequiredTrait
{
    protected int $users_role_id;

    public function getUsersRoleId(): int
    {
        return $this->users_role_id;
    }

    public function getUsersRole(): Role
    {
        return Role::requireById($this->users_role_id);
    }
}