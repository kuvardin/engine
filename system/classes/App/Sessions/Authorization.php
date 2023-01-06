<?php

declare(strict_types=1);

namespace App\Sessions;

use App;
use App\Actions\DeletableTrait;
use App\Users\User;
use App\Users\UserRequiredTrait;
use Firebase\JWT\JWT;
use Kuvardin\FastMysqli\Exceptions\AlreadyExists;
use Kuvardin\FastMysqli\Mysqli;
use Kuvardin\FastMysqli\TableRow;

class Authorization extends TableRow
{
    use SessionRequiredTrait;
    use UserRequiredTrait;
    use DeletableTrait;

    private const DB_TABLE = 'authorizations';

    protected ?int $parent_id;
    protected int $access_token_expiration_date;
    protected int $refresh_token_expiration_date;
    protected string $ip;
    protected ?string $user_agent;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->initDeletable($data);
        $this->session_id = $data['session_id'];
        $this->user_id = $data['user_id'];
        $this->parent_id = $data['parent_id'];
        $this->access_token_expiration_date = $data['access_token_expiration_date'];
        $this->refresh_token_expiration_date = $data['refresh_token_expiration_date'];
        $this->ip = $data['ip'];
        $this->user_agent = $data['user_agent'];
    }

    public static function getDatabaseTableName(): string
    {
        return self::DB_TABLE;
    }

    /**
     * @throws AlreadyExists
     */
    public static function create(
        Session|int $session,
        User|int $user,
        self|int|null $parent,
        string $ip,
        ?string $user_agent,
        int $access_token_expiration_date = null,
        int $refresh_token_expiration_date = null,
    ): self
    {
        return self::createWithFieldsValues(null, [
            'session_id' => $session,
            'user_id' => $user,
            'parent_id' => $parent,
            'access_token_expiration_date' => $access_token_expiration_date
                    ?? (time() + App::settings('jwt.access_token.live_time')),
            'refresh_token_expiration_date' => $refresh_token_expiration_date
                    ?? (time() + App::settings('jwt.refresh_token.live_time')),
            'ip' => $ip,
            'user_agent' => $user_agent,
        ]);
    }

    public static function getFilters(
        bool $deleted = null,
        Session|array|int $session = null,
        User|array|int $user = null,
    ): array
    {
        return [
            'deletion_date' => Mysqli::get_not_null($deleted),
            'session_id' => $session === [] ? null : $session,
            'user_id' => $user === [] ? null : $user,
        ];
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function getParent(): ?self
    {
        return $this->parent_id === null ? null : self::requireById($this->parent_id);
    }

    public function getAccessTokenExpirationDate(): int
    {
        return $this->access_token_expiration_date;
    }

    public function getRefreshTokenExpirationDate(): int
    {
        return $this->refresh_token_expiration_date;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }
}