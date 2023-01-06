<?php

declare(strict_types=1);

namespace App\Sessions;

enum WebBot: string
{
    case Facebook = 'FACEBOOK';
    case Yandex = 'YANDEX';
    case Telegram = 'TELEGRAM';
    case Google = 'GOOGLE';
    case Twitter = 'TWITTER';
    case Other = 'OTHER';

    public static function makeByUserAgent(string $user_agent): ?self
    {
        switch (true) {
            case str_starts_with($user_agent, 'facebookexternal'):
                return self::Facebook;

            case str_contains($user_agent, 'YandexBot'):
            case str_contains($user_agent, 'http://yandex.com/bots'):
                return self::Yandex;

            case str_starts_with($user_agent, 'TelegramBot'):
                return self::Telegram;

            case str_contains($user_agent, 'TwitterBot'):
                return self::Twitter;

            case str_contains($user_agent, 'Googlebot'):
            case str_contains($user_agent, 'Bot-Google'):
                return self::Google;


            case $user_agent === 'Go http package':
            case $user_agent === 'fasthttp':
            case str_contains($user_agent, 'Go-http-client'):
            case str_contains($user_agent, 'webprosbot'):
            case str_contains($user_agent, 'zgrab/'):
            case str_contains($user_agent, 'CensysInspect'):
            case str_contains($user_agent, 'libwww-perl'):
            case str_contains($user_agent, 'expanseinc.com'):
            case str_contains($user_agent, 'python-requests'):
            case str_contains($user_agent, 'NetSystemsResearch'):
            case str_contains($user_agent, 'https://security.ipip.net'):
            case str_contains($user_agent, 'l9tcpid'):
            case str_contains($user_agent, 'GitHub-Hookshot'):
            case str_contains($user_agent, 'l9explore'):
            case str_starts_with($user_agent, 'httpx'):
            case str_contains($user_agent, 'github.com/projectdiscovery/httpx'):
            case str_starts_with($user_agent, 'curl/'):
            case str_starts_with($user_agent, 'amoService'):
            case str_starts_with($user_agent, 'https://gdnplus.com'):
            case str_contains($user_agent, 'Yahoo! Slurp'):
            case str_contains($user_agent, 'https://github.com/Fay48'):
            case str_contains($user_agent, 'bingbot/'):
            case str_contains($user_agent, 'axios/'):
            case str_starts_with($user_agent, 'GetIntent Crawler'):
            case str_contains($user_agent, 'aiohttp'):
            case str_starts_with($user_agent, 'Wget'):
                return self::Other;
        }

        return null;
    }
}