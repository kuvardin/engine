<?php

declare(strict_types=1);

namespace App\Users\Roles;

use App;
use App\Actions\CreationTrait;
use App\Actions\DeletableTrait;
use App\Sessions\Session;
use App\Users\User;
use App\Users\UserRequiredTrait;
use Kuvardin\FastMysqli\Exceptions\AlreadyExists;
use Kuvardin\FastMysqli\Mysqli;
use Kuvardin\FastMysqli\TableRow;

class UserOnRole extends TableRow
{
    use RoleRequiredTrait;
    use UserRequiredTrait;
    use DeletableTrait;
    use CreationTrait;

    private const DB_TABLE = 'user_roles';

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->initDeletable($data);
        $this->users_role_id = $data['role_name_id'];
        $this->user_id = $data['user_id'];
        $this->creation_id = $data['creation_id'];
    }

    public static function getDatabaseTableName(): string
    {
        return self::DB_TABLE;
    }

    public static function getFilters(
        bool $deleted = null,
        Role|int|array $role = null,
        array $role_filters = null,
        User|int|array $user = null,
        array $user_filters = null,
    ): array
    {
        $result = [
            'deletion_date' => Mysqli::get_not_null($deleted),
            'role_name_id' => $role === [] ? null : $role,
            'user_id' => $user === [] ? null : $user,
        ];

        if (!empty($role_filters)) {
            $result[] = App::mysqli()->fast_where_in('role_name_id', Role::getDatabaseTableName(), $role_filters);
        }

        if (!empty($user_filters)) {
            $result[] = App::mysqli()->fast_where_in('user_id', User::getDatabaseTableName(), $user_filters);
        }

        return $result;
    }

    /**
     * @throws AlreadyExists
     */
    public static function create(
        Session $session,
        Role|int $role,
        User|int $user,
    ): self
    {
        self::requireUnique([
            'role_name_id' => $role,
            'user_id' => $user,
            'deletion_date' => Mysqli::is_null(),
        ]);

        return self::createCreatableWithFieldsValues($session, null, [
            'role_name_id' => $role,
            'user_id' => $user,
        ]);
    }
}