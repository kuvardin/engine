<?php

declare(strict_types=1);

namespace App\Telegram;

use App;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Kuvardin\TelegramBotsApiLite\Exceptions\LiteApiException;
use Kuvardin\TelegramBotsApiLite\LiteBot;
use Throwable;

/**
 * Class Notifier
 *
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class Notifier
{
    private const MESSAGE_MAX_LENGTH = 1500;

    private function __construct()
    {
    }

    public static function tryToSendError(string $error, int $attempts = 1): bool
    {
        try {
            return self::sendError($error, $attempts);
        } catch (Throwable $exception) {
            self::tryToSendException($exception);
            return false;
        }
    }

    /**
     * @throws LiteApiException
     */
    public static function sendError(string $error, int $attempts = 1): bool
    {
        if (App::settings('telegram.chats.for_errors') === null) {
            return true;
        }

        return self::sendMessage(App::settings('telegram.chats.for_errors'), $error, $attempts);
    }

    /**
     * @throws LiteApiException
     */
    public static function sendMessage(int $chat_id, string $message, int $attempts = 1): bool
    {
        /** @var JsonException|null $exception */
        $exception = null;

        for ($i = 0; $i < $attempts; $i++) {
            $exception = null;

            try {
                App::getTelegramLiteBot()
                    ->request('sendMessage', [
                        'chat_id' => $chat_id,
                        'text' => $message,
                        'disable_web_page_preview' => true,
                        'parse_mode' => LiteBot::PARSE_MODE_HTML,
                    ]);
                return true;
            } catch (JsonException $json_exception) {
                $exception = $json_exception;
                sleep(3);
            } catch (GuzzleException) {
                sleep(3);
            } catch (LiteApiException $api_exception) {
                if ($api_exception->isAntiflood()) {
                    return false;
                }

                throw $api_exception;
            }
        }

        if ($exception !== null) {
            throw $exception;
        }

        return false;
    }

    public static function tryToSendException(
        Throwable $exception,
        int $attempts = 1,
        $chat_id = null,
        string $title = null,
        string $footer = null,
        bool $try_again = true,
    ): bool
    {
        try {
            return self::sendException($exception, $attempts, $chat_id, $title, $footer);
        } catch (Throwable $new_exception) {
            if ($try_again) {
                self::tryToSendException($new_exception, $attempts, $chat_id, $title, $footer, false);
            }
            return false;
        }
    }

    /**
     * @throws LiteApiException
     */
    public static function sendException(
        Throwable $exception,
        int $attempts = 1,
        $chat_id = null,
        string $title = null,
        string $footer = null,
    ): bool
    {
        $chat_id ??= App::settings('telegram.chats.for_errors');
        if ($chat_id === null) {
            return true;
        }

        $trace_string = $exception->getTraceAsString();
        $trace_string = App::shortenString($trace_string, self::MESSAGE_MAX_LENGTH);
        $trace_string = LiteBot::filterString($trace_string);

        $file = $exception->getFile();

        $body = get_class($exception) . " #{$exception->getCode()}: {$exception->getMessage()}" .
            " on $file:{$exception->getLine()}\n\n$trace_string";

        $title_length = $title === null ? 0 : mb_strlen($title);
        $body_length = mb_strlen($body);
        $footer_length = $footer === null ? 0 : mb_strlen($footer);

        if ($title_length + $body_length + $footer_length > self::MESSAGE_MAX_LENGTH) {
            $body = App::shortenString($body, self::MESSAGE_MAX_LENGTH - $title_length - $footer_length);
        }


        $final_text = ($title === null ? '' : ('<b>' . LiteBot::filterString($title) . "</b>\n\n")) .
            '<pre>' . LiteBot::filterString($body) . '</pre>' .
            ($footer === null ? '' : ("\n\n" . LiteBot::filterString($footer)));

        return self::sendMessage($chat_id, $final_text, $attempts);
    }

    /**
     * @throws LiteApiException
     */
    public static function sendNotification(
        string $notification,
        int $attempts = 1,
    ): bool
    {
        return self::sendMessage(App::settings('telegram.chats.for_notifications'), $notification, $attempts);
    }

    public static function tryToSendNotification(string $notification, int $attempts = 1, bool $try_again = true): bool
    {
        try {
            return self::sendNotification($notification, $attempts);
        } catch (Throwable $new_exception) {
            if ($try_again) {
                self::tryToSendException($new_exception, $attempts, try_again: false);
            }
            return false;
        }
    }

    /**
     * @throws LiteApiException
     */
    public static function sendArray(
        int $chat_id,
        array $data,
        int $attempts = 1,
    ): bool
    {
        $message = LiteBot::filterString(App::shortenString(self::arrayToJson($data), self::MESSAGE_MAX_LENGTH));
        return self::sendMessage($chat_id, "<pre>$message</pre>", $attempts);
    }

    public static function arrayToJson(array $array = [], string $tab = ' '): string
    {
        $assoc = !array_is_list($array);
        $result = ($assoc ? '{' : '[') . "\n";
        $s = count($array);

        foreach ($array as $key => $value) {
            $result .= $tab . ($assoc ? '"' . $key . '": ' : '');
            if (is_array($value)) {
                $result .= self::arrayToJson($value, $tab . ' ');
            } elseif ($value === null) {
                $result .= 'null';
            } elseif (is_bool($value)) {
                $result .= $value ? 'true' : 'false';
            } elseif (is_numeric($value)) {
                $result .= $value;
            } else {
                $result .= '"' . $value . '"';
            }

            $result .= (--$s ? ',' : '') . "\n";
        }

        $result .= substr($tab, 1) . ($assoc ? '}' : ']');
        return $result;
    }
}