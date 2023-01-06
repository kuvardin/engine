<?php

declare(strict_types=1);

namespace App\Users;

use App;
use Kuvardin\FastMysqli\Exceptions\AlreadyExists;
use Kuvardin\FastMysqli\TableRow;
use RuntimeException;

class User extends TableRow
{
    protected const DB_TABLE = 'users';

    public const PERMISSIONS = [

    ];

    public const COL_EMAIL = 'email';
    public const COL_FIRST_NAME = 'first_name';
    public const COL_LAST_NAME = 'last_name';
    public const COL_MIDDLE_NAME = 'middle_name';
    public const COL_LAST_REQUEST_DATE = 'last_request_date';
    public const COL_PASSWORD_HASH = 'password_hash';

    protected string $email;
    protected string $password_hash;
    protected string $first_name;
    protected ?string $last_name;
    protected ?string $middle_name;
    protected ?int $last_request_date;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->email = $data[self::COL_EMAIL];
        $this->password_hash = $data[self::COL_PASSWORD_HASH];
        $this->first_name = $data[self::COL_FIRST_NAME];
        $this->last_name = $data[self::COL_LAST_NAME];
        $this->middle_name = $data[self::COL_MIDDLE_NAME];
        $this->last_request_date = $data[self::COL_LAST_REQUEST_DATE];
    }

    public static function getDatabaseTableName(): string
    {
        return self::DB_TABLE;
    }

    public static function makeByEmail(string $email): ?self
    {
        return self::makeByFieldsValues([self::COL_EMAIL => $email]);
    }

    /**
     * @throws AlreadyExists
     */
    public static function create(
        string $email,
        string $password,
        string $first_name,
        string $last_name = null,
        string $middle_name = null,
        int $last_request_date = null,
    ): self
    {
        if (!self::checkEmailValidity($email)) {
            throw new RuntimeException("Incorrect email: $email");
        }

        return self::createWithFieldsValues(null, [
            self::COL_EMAIL => $email,
            self::COL_PASSWORD_HASH => self::getPasswordHash($password),
            self::COL_FIRST_NAME => $first_name,
            self::COL_LAST_NAME => $last_name,
            self::COL_MIDDLE_NAME => $middle_name,
            self::COL_LAST_REQUEST_DATE => $last_request_date,
        ]);
    }

    public static function checkEmailValidity(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function getPasswordHash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public static function getFilters(
        string $query = null,
    ): array
    {
        $result = [

        ];

        if ($query !== null) {
            $result[] = App::mysqli()->fast_search_exp_gen($query, true, [
                self::COL_FIRST_NAME,
                self::COL_LAST_NAME,
                self::COL_MIDDLE_NAME,
            ]);
        }

        return $result;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->setFieldValue(self::COL_EMAIL, $this->email, $email);
    }

    public function setPassword(string $password): void
    {
        $this->setFieldValue(self::COL_PASSWORD_HASH, $this->password_hash, self::getPasswordHash($password));
    }

    public function getFullName(): string
    {
        return trim("{$this->last_name} {$this->first_name} {$this->middle_name}");
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): void
    {
        $this->setFieldValue(self::COL_FIRST_NAME, $this->first_name, $first_name);
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): void
    {
        $this->setFieldValue(self::COL_LAST_NAME, $this->last_name, $last_name);
    }

    public function getMiddleName(): ?string
    {
        return $this->middle_name;
    }

    public function setMiddleName(?string $middle_name): void
    {
        $this->setFieldValue(self::COL_MIDDLE_NAME, $this->middle_name, $middle_name);
    }

    public function getLastRequestDate(): ?int
    {
        return $this->last_request_date;
    }

    public function setLastRequestDate(?int $last_request_date): void
    {
        $this->setFieldValue(self::COL_LAST_REQUEST_DATE, $this->last_request_date, $last_request_date);
    }

    public function fixRequest(int $last_request_date = null): void
    {
        $this->setLastRequestDate($last_request_date ?? time());
    }

    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    /**
     * @param int|int[] $actions
     */
    public function can(string $class_name, int|array $actions, int $allowed_actions = 0): bool
    {
        $allowed_actions = $allowed_actions | (self::PERMISSIONS[$class_name] ?? 0);
//        return $this->getUsersPost() !== null && $this->getUsersPost()->can($class_name, $actions, $allowed_actions);
        return true; // TODO
    }

    public function isOnline(int $current_timestamp = null): bool
    {
        return (CURRENT_TIMESTAMP - $this->last_request_date) <= App::settings('time_online');
    }
}