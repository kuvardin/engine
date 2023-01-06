<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use Kuvardin\TelegramBotsApiLite\LiteBot;
use Kuvardin\FastMysqli\Mysqli;

class App
{
    protected static array $settings;
    protected static ?Mysqli $mysqli = null;
    protected static ?LiteBot $telegram_lite_bot = null;

    public static function init(array $settings): void
    {
        self::$settings = $settings;
    }

    public static function connectMysqli(): void
    {
        if (self::$mysqli === null) {
            self::$mysqli = new Mysqli(
                self::$settings['db.host'],
                self::$settings['db.user'],
                self::$settings['db.pass'],
                self::$settings['db.base'],
            );

            self::$mysqli->set_charset(self::$settings['db.charset']);
            self::$mysqli->set_opt(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
            Kuvardin\FastMysqli\TableRow::setMysqli(self::$mysqli);
        } else {
            throw new RuntimeException('Already ran');
        }
    }

    public static function closeMysqli(): void
    {
        if (self::$mysqli !== null) {
            self::$mysqli->close();
            self::$mysqli = null;
        }
    }

    public static function settings(string $key): mixed
    {
        return self::$settings[$key];
    }

    public static function getTelegramLiteBot(): LiteBot
    {
        if (self::$telegram_lite_bot === null) {
            self::$telegram_lite_bot = new LiteBot(
                new Client(),
                self::$settings['telegram.bot.token'],
            );
        }

        return self::$telegram_lite_bot;
    }

    public static function mysqli(): Mysqli
    {
        return self::$mysqli;
    }

    public static function shortenString(string $string, int $max_length): string
    {
        return mb_strlen($string) > $max_length
            ? rtrim(mb_substr($string, 0, $max_length-2), " \n\r\t\0\x0B\"-~`!@#$%^&*()_+=,./?<>|\\[]{}") . '..'
            : $string;
    }

    /**
     * @param resource|null $context
     */
    public static function requireDir(string $pathname, bool $recursive = true, int $mode = 0777, $context = null): void
    {
        if (!file_exists($pathname) && !mkdir($pathname, $mode, $recursive, $context) && !is_dir($pathname)) {
            throw new RuntimeException("Directory $pathname was not created");
        }
    }

    public static function getRandomString(int $length, string $alphabet = null): string
    {
        if ($length < 1) {
            throw new Error('String length must pe positive number');
        }

        $alphabet ??= 'abcdefghijklmnopqrstuvwxyz0123456789';
        $alphabet_length = mb_strlen($alphabet);
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            try {
                $result .= mb_substr($alphabet, random_int(0, $alphabet_length - 1), 1);
            } catch (Exception $e) {
                throw new RuntimeException($e->getMessage(), $e->getCode(), $e->getPrevious());
            }
        }

        return $result;
    }

    public static function getGenerationTime(float $start_time = null): float
    {
        return (microtime(true) - ($start_time ?? START_TIME)) * 1000;
    }

    public static function roundNum(int|float $number, int $precision = null): string
    {
        if ($number > 1000000) {
            return round($number / 100000, 1) . 'M';
        }

        if ($number > 10000) {
            return round($number / 1000, 1) . 'k';
        }

        if ($number > 1000) {
            return round($number / 1000, 2) . 'k';
        }

        if (is_float($number)) {
            return (string)($precision === null ? $number : round($number, $precision));
        }

        return (string)$number;
    }

    public static function htmlFilter(?string $text): ?string
    {
        return $text === null ? null : htmlspecialchars($text);
    }

    public static function setCookie(string $name, ?string $value): void
    {
        setcookie(
            $name,
            $value ?? '',
            App::settings('cookies.expires'),
            App::settings('cookies.path'),
            App::settings('cookies.domain'),
        );
    }

    public static function checkStringLength(
        string $string,
        int $length_min = null,
        int $length_max = null,
        bool $use_mbstring = true,
    ): bool
    {
        $length = $use_mbstring ? mb_strlen($string) : strlen($string);
        if ($length_min !== null && $length < $length_min) {
            return false;
        }

        if ($length_max !== null && $length > $length_max) {
            return false;
        }

        return true;
    }

    public static function filterError(string $error): string
    {
        return str_replace('/var/www/kuvardin', '', $error);
    }
}