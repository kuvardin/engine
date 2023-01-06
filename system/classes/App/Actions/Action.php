<?php

declare(strict_types=1);

namespace App\Actions;

use App\Sessions\Session;
use App\Sessions\SessionRequiredTrait;
use App\Users\UserTrait;
use Kuvardin\FastMysqli\Exceptions\AlreadyExists;
use Kuvardin\FastMysqli\TableRow;
use RuntimeException;

class Action extends TableRow
{
    use SessionRequiredTrait;
    use UserTrait;

    private const DB_TABLE = 'actions';

    public const SHOW = 1;
    public const CREATE = 2;
    public const EDIT = 4;
    public const DELETE = 8;
    public const RESTORE = 16;
    public const APPROVE = 32;
    public const REJECT = 64;

    /** Основные */
    public const MAIN = self::SHOW | self::CREATE | self::EDIT | self::DELETE | self::RESTORE;

    /** Все действия */
    public const ALL = self::SHOW | self::CREATE | self::EDIT | self::DELETE | self::RESTORE | self::APPROVE |
        self::REJECT;

    /** Удаленные объекты */
    public const CLASS_DELETED_ITEMS = 'DELETED_ITEMS';

    /** IP-адреса */
    public const CLASS_IP_ADDRESSES = 'IP_ADDRESSES';

    /** Приватная информация пользователей */
    public const CLASS_PRIVATE_USERS_INFO = 'PRIVATE_USERS_INFO';

    readonly public string $object_class;
    readonly public  int $object_id;
    readonly public  int $type;
    readonly public  string $ip;
    readonly public  ?string $user_agent;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->session_id = $data['session_id'];
        $this->user_id = $data['user_id'];
        $this->object_class = $data['object_class'];
        $this->object_id = $data['object_id'];
        $this->type = $data['type'];
        $this->ip = $data['ip'];
        $this->user_agent = $data['user_agent'];
    }

    /**
     * @throws AlreadyExists
     */
    public static function create(Session $session, TableRow $object, int $type): self
    {
        if (!self::checkType($type)) {
            throw new RuntimeException("Unknown action type: $type");
        }

        return self::createWithFieldsValues(null, [
            'session_id' => $session,
            'user_id' => $session->getUserId(),
            'object_class' => get_class($object),
            'object_id' => $object,
            'type' => $type,
            'ip' => $session->getIp(),
            'user_agent' => $session->getUserAgentValue(),
        ]);
    }

    public static function checkType(int $type): bool
    {
        return $type === self::SHOW ||
            $type === self::CREATE ||
            $type === self::EDIT ||
            $type === self::DELETE ||
            $type === self::RESTORE ||
            $type === self::APPROVE ||
            $type === self::REJECT;
    }

    public static function getDatabaseTableName(): string
    {
        return self::DB_TABLE;
    }
}