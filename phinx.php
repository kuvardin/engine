<?php

$inclfiles_dir = 'system/inclfiles';

$system_settings = require $inclfiles_dir . '/settings.php';
if (file_exists($inclfiles_dir . '/settings.local.php')) {
    $system_settings = array_merge($system_settings, require $inclfiles_dir . '/settings.local.php');
}

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/system/phinx/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/system/phinx/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'production_db',
            'user' => 'root1',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => $system_settings['db.host'],
            'name' => $system_settings['db.base'],
            'user' => $system_settings['db.user'],
            'pass' => $system_settings['db.pass'],
            'port' => $system_settings['db.port'],
            'charset' => $system_settings['db.charset'],
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'testing_db',
            'user' => 'root',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
