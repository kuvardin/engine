<?php

declare(strict_types=1);

namespace App\Users\Roles;

use App\Actions\ClassName;
use App\Actions\ClassNameRequiredTrait;
use App\Actions\CreationTrait;
use App\Actions\DeletableTrait;
use App\Sessions\Session;
use Kuvardin\FastMysqli\Exceptions\AlreadyExists;
use Kuvardin\FastMysqli\Mysqli;
use Kuvardin\FastMysqli\TableRow;

class Permission extends TableRow
{
    use RoleRequiredTrait;
    use ClassNameRequiredTrait;
    use DeletableTrait;
    use CreationTrait;

    private const DB_TABLE = 'role_permissions';

    protected int $allowed_actions;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->initDeletable($data);
        $this->users_role_id = $data['role_name_id'];
        $this->class_name_id = $data['class_name_id'];
        $this->allowed_actions = $data['action'];
        $this->creation_id = $data['creation_id'];
    }

    public static function getDatabaseTableName(): string
    {
        return self::DB_TABLE;
    }

    /**
     * @throws AlreadyExists
     */
    public static function create(
        Session $session,
        Role|int $role,
        ClassName|int $class_name,
        int $allowed_actions,
    ): self
    {
        self::requireUnique([
            'role_name_id' => $role,
            'class_name_id' => $class_name,
            'deletion_date' => Mysqli::is_null(),
        ]);

        return self::createCreatableWithFieldsValues($session, null, [
            'role_name_id' => $role,
            'class_name_id' => $class_name,
            'action' => $allowed_actions,
        ]);
    }

    public function getAllowedActions(): int
    {
        return $this->allowed_actions;
    }

    public function setAllowedActions(int $allowed_actions): void
    {
        $this->allowed_actions = $allowed_actions;
    }
}