<?php

declare(strict_types=1);

namespace App\Sessions;

use App;
use App\Actions\Action;
use App\DateTime;
use App\Exceptions\NotEnoughRightsException;
use App\Languages\LanguageByCodeRequiredTrait;
use App\Users\User;
use App\Users\UserTrait;
use DateTimeZone;
use Kuvardin\FastMysqli\Exceptions\AlreadyExists;
use Kuvardin\FastMysqli\Mysqli;
use Kuvardin\FastMysqli\SelectionData;
use Kuvardin\FastMysqli\TableRow;

class Session extends TableRow
{
    use LanguageByCodeRequiredTrait;
    use UserAgentByValueTrait;
    use UserTrait;

    protected const DB_TABLE = 'sessions';

    public const PERMISSIONS = [

    ];

    /**
     * @var string Строковый идентификатов сессии /^[0-9a-f]{32}$/
     */
    protected string $secret_code;

    /**
     * @var string|null
     */
    protected ?string $web_bot_code;

    /**
     * @var string IP-адрес
     */
    protected string $ip;

    protected bool $has_cookies;
    protected ?int $parent_id;
    protected ?int $last_ad_display_date;
    protected int $last_request_date;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->secret_code = $data['secret_code'];
        $this->web_bot_code = $data['web_bot_code'];
        $this->user_id = $data['user'];
        $this->ip = $data['ip'];
        $this->has_cookies = (bool)$data['has_cookies'];
        $this->parent_id = $data['parent'];
        $this->user_agent_value = $data['user_agent'];
        $this->language_code = $data['language_code'];
        $this->last_ad_display_date = $data['last_ad_display_date'];
        $this->last_request_date = $data['last_request_date'];
    }

    public static function getDatabaseTableName(): string
    {
        return self::DB_TABLE;
    }

    /**
     * @throws AlreadyExists
     */
    public static function create(
        string $ip,
        ?string $user_agent,
        string $language_code,
        int $last_request_date = null,
        string $web_bot_code = null,
    ): self
    {
        if ($user_agent !== null) {
            $web_bot_code = WebBot::makeByUserAgent($user_agent);
            if ($web_bot_code !== null) {
                $session = self::makeByWebBotCode($web_bot_code->value);
                if ($session !== null) {
                    return $session;
                }
            }
        }

        return self::createWithFieldsValues(null, [
            'secret_code' => self::generateSecretCode(),
            'web_bot_code' => $web_bot_code?->value,
            'ip' => $ip,
            'user_agent' => $user_agent,
            'language_code' => $language_code,
            'has_cookies' => false,
            'last_request_date' => $last_request_date ?? time(),
        ]);
    }

    public static function makeByWebBotCode(string $web_bot_code): ?self
    {
        return self::makeByFieldsValues(['web_bot_code' => $web_bot_code]);
    }

    protected static function generateSecretCode(): string
    {
        do {
            $result = App::getRandomString(32, '0123456789abcdef');
        } while (App::mysqli()->fast_check(self::DB_TABLE, ['secret_code' => $result]));
        return $result;
    }

    public static function makeByCookies(array &$cookies, string $ip, ?string $user_agent): ?self
    {
        if ($cookies === []) {
            return self::makeByFieldsValues([
                'has_cookies' => false,
                'ip' => $ip,
                'user_agent' => $user_agent ?? Mysqli::is_null(),
                'user' => Mysqli::is_null(),
            ], TableRow::COL_ID, SelectionData::SORT_ASC);
        }

        if (empty($cookies[App::settings('cookies.names.session_id')])) {
            return null;
        }

        $result = self::makeBySecretCode($cookies[App::settings('cookies.names.session_id')]);
        if ($result !== null && !$result->hasCookies()) {
            $result->setHasCookies(true);
            $result->save();
        }

        return $result;
    }

    public static function makeBySecretCode(string $secret_code): ?self
    {
        return self::makeByFieldsValues(['secret_code' => $secret_code]);
    }

    public static function getFilters(
        string $ip = null,
        string $user_agent_query = null,
        string $language_code = null,
        DateTime $last_request_date_min = null,
        DateTime $last_request_date_max = null,
        DateTime $creation_date_min = null,
        DateTime $creation_date_max = null,
        bool $is_web_bot = null,
        bool $has_cookies = null,
        bool $has_parent = null,
    ): array
    {
        $result = [
            'ip' => $ip,
            'language_code' => $language_code,
            'has_cookies' => $has_cookies,
            'parent' => Mysqli::get_not_null($has_parent),
        ];

        //TODO

        if ($is_web_bot !== null) {
            $result['web_bot_code'] = Mysqli::get_not_null($is_web_bot);
        }

        if ($user_agent_query !== null) {
            $result[] = App::mysqli()->fast_search_exp_gen($user_agent_query, true, ['user_agent']);
        }

        return $result;
    }

    public function requireUser(): User
    {
        return User::requireById($this->user_id);
    }

    public function setCookie(): void
    {
        setcookie(
            App::settings('cookies.names.session_id'),
            $this->secret_code,
            App::settings('cookies.expires'),
            App::settings('cookies.path'),
            App::settings('cookies.domain'),
        );
    }

    public function getSecretCode(): string
    {
        return $this->secret_code;
    }

    public function isWebBot(): bool
    {
        return $this->web_bot_code !== null;
    }

    public function getWebBotCode(): ?string
    {
        return $this->web_bot_code;
    }

    public function setWebBotCode(?string $web_bot_code): void
    {
        $this->setFieldValue('web_bot_code', $this->web_bot_code, $web_bot_code);
    }

    public function setUser(?User $user): void
    {
        $this->setFieldValue('user', $this->user_id, $user);
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setLanguageCode(string $language_code): void
    {
        $this->setFieldValue('language_code', $this->language_code, $language_code);
    }

    public function getLastRequestDate(): int
    {
        return $this->last_request_date;
    }

    public function setLastRequestDate(int $last_request_date): void
    {
        $this->setFieldValue('last_request_date', $this->last_request_date, $last_request_date);
    }

    public function hasCookies(): bool
    {
        return $this->has_cookies;
    }

    public function setHasCookies(bool $has_cookies): void
    {
        $this->setFieldValue('has_cookies', $this->has_cookies, $has_cookies);
    }

    public function fixRequest(string $ip, ?string $user_agent_value, int $last_request_date = null): void
    {
        $this->setFieldValue('ip', $this->ip, $ip);
        $this->setFieldValue('user_agent', $this->user_agent_value, $user_agent_value);

        $last_request_date ??= time();
        $this->setLastRequestDate($last_request_date);

        if ($user_agent_value !== null) {
            $web_bot_code = WebBot::makeByUserAgent($user_agent_value);
            if ($web_bot_code !== null) {
                $this->setWebBotCode($web_bot_code->value);
            }
        }
    }

    public function getUrl(?string $path = null, array $get = null, string $lang_code = null): string
    {
        if ($path === null) {
            return '?' . (empty($get) ? '' : (http_build_query($get)));
        }

        return '/' . ($lang_code ?? $this->language_code) . '/' . ltrim($path, '/') .
            (empty($get) ? '' : ('?' . http_build_query($get)));
    }

    public function getInfo(): string
    {
        return $this->web_bot_code
            ?? ("[{$this->ip}]" . ($this->user_agent_value === null ? '' : " {$this->user_agent_value}"));
    }

    public function getDateTime(int $timestamp): DateTime
    {
        return DateTime::makeByTimestamp($timestamp, $this->getDateTimeZone());
    }

    public function getDateTimeZone(): DateTimeZone
    {
        return new DateTimeZone('Asia/Almaty');
    }

    public function requirePermission(string $object, int $actions): void
    {
        if (!$this->can($object, $actions)) {
            throw new NotEnoughRightsException($object, $actions);
        }
    }

    public function can(string $class_name, int $actions): bool
    {
//        return true;
        return in_array($this->id, [1, 3, 48529]);

        $allowed_actions = self::PERMISSIONS[$class_name] ?? 0;
        return $this->getUser()?->can($class_name, $actions, $allowed_actions)
            ?? (($allowed_actions & $actions) === $actions);
    }

    public function canShowDeletedItems(): bool
    {
        return $this->can(Action::CLASS_DELETED_ITEMS, Action::SHOW);
    }

    public function countSimilar(): int
    {
        /** @noinspection ProperNullCoalescingOperatorUsageInspection */
        return self::count([
            'ip' => $this->ip,
            'user_agent' => $this->user_agent ?? Mysqli::is_null(),
            "`id` <> {$this->id}",
        ]);
    }

    public function showDeletedItems(?bool $deleted): ?bool
    {
        if (!$this->can(Action::CLASS_DELETED_ITEMS, Action::SHOW)) {
            if ($deleted) {
                throw new NotEnoughRightsException(Action::CLASS_DELETED_ITEMS, Action::SHOW);
            }

            return false;
        }

        return $deleted;
    }

    public function isAuthorized(): bool
    {
        return $this->user_id !== null;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function getLastAdDisplayDate(): ?int
    {
        return $this->last_ad_display_date;
    }

    public function setLastAdDisplayDate(?int $last_ad_display_date): void
    {
        $this->setFieldValue('last_ad_display_date', $this->last_ad_display_date, $last_ad_display_date);
    }
}
