<?php

// Чтобы перезаписать одну или несколько настроек, создайте в этой же папке файл settings.local.php
// Не рекомендуется перезаписывать значения, которые явно в этом не нуждаются

return [
    'db.host' => '',
    'db.port' => '3306',
    'db.user' => '',
    'db.pass' => '',
    'db.base' => '',
    'db.charset' => 'utf8mb4',

    'languages' => [
        'ru',
        'kk',
        'en',
    ],

    'site.host' => 'https://kuvard.in',
    'site.domain' => 'kuvard.in',
    'site.domain_regexp' => '|^(.+?\.)?kuvard\.in$|ui',

    'email' => 'info@macdent.kz',
    'site.copyright' => 'Skidki.Me - 2022',

    'items_limit.default' => 30,
    'items_limit_max.default' => 30,
    'language.default' => 'ru',
    'timezone.default' => 'Asia/Almaty',
    'host.default' => 'kuvard.in',
    'time_online' => 300,

    'telegram.bot.username' => '',
    'telegram.bot.token' => '',
    'telegram.chats.for_errors' => -1001517367464,
    'telegram.chats.for_notifications' => 196067998,
    'telegram.chats.for_pm_images_errors' => -790992837,
    'telegram.chats.for_pm_guzzle_errors' => -622036960,
    'telegram.chats.for_pm_errors' => -1001682600747,

    'export_key_salt' => 'Rsu3mj0Hh5K4V53j',

    'cookies.names.session_id' => 'session_id',
    'cookies.names.lang_code' => 'language',
    'cookies.expires' => time() + 31536000,
    'cookies.path' => '/',
    'cookies.domain' => '.kuvard.in',

    'jwt.access_token.live_time' => 2 * 3600,
    'jwt.refresh_token.live_time' => 7 * 24 * 3600,
    'jwt.algorithm' => 'HS256',
    'jwt.salt' => '0D57&yI3)&Ifoq*1',
    'jwt.key' => 'E2si26G957eqHQjK',
];