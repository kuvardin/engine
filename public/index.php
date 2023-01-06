<?php

declare(strict_types=1);

error_reporting(E_ALL);

set_time_limit(empty($argv) ? 60 : 0);
ini_set('max_execution_time', empty($argv) ? 60 : 0);

define('ROOT_DIR', dirname(__DIR__));

const SYSTEM_DIR = ROOT_DIR . '/system';
const PHRASES_DIR = SYSTEM_DIR . '/phrases';
const FILES_DIR = ROOT_DIR . '/public/files';
const CACHE_DIR = SYSTEM_DIR . '/cache';
const INCLFILES_DIR = SYSTEM_DIR . '/inclfiles';
const CLASSES_DIR = SYSTEM_DIR . '/classes';
const COOKIES_DIR = SYSTEM_DIR . '/cookies';
const TEMPLATES_DIR = SYSTEM_DIR . '/templates';
const LOGS_DIR = SYSTEM_DIR . '/logs';
const TEMP_DIR = '/tmp';
const IMAGES_DIR = ROOT_DIR . '/public/images';

$settings = require INCLFILES_DIR . '/settings.php';
if (file_exists(INCLFILES_DIR . '/settings.local.php')) {
    $settings = array_merge($settings, require INCLFILES_DIR . '/settings.local.php');
}

date_default_timezone_set($settings['timezone.default']);

define('START_TIME', microtime(true));
define('CURRENT_TIMESTAMP', time());

try {
    set_error_handler(static function (int $code, string $message, string $file, int $line) {
        App\Exceptions\LoggedError::fix('ERROR', $code, $message, $file, $line, null);
        $error_text = "Handled by error handler\nError #$code: $message on $file:$line\n";
        App\Telegram\Notifier::sendError($error_text);
        App\Logger::writeError($error_text);
        throw new Error($error_text, $code);
    });

    register_shutdown_function(static function () {
        $e = error_get_last();
        if ($e !== null) {
            App\Exceptions\LoggedError::fix('SHUTDOWN_ERROR', $e['type'], $e['message'], $e['file'], $e['line'], null);

            $error_text = "Handled by shutdown function\n" .
                "Fatal error #{$e['type']}: {$e['message']} on {$e['file']}:{$e['line']}\n";

            App\Logger::writeError($error_text);
            error_log($error_text);
            throw new Error($error_text);
        }
    });

    /** @var Composer\Autoload\ClassLoader $loader */
    $loader = require ROOT_DIR . '/vendor/autoload.php';
    require CLASSES_DIR . '/App.php';
    $loader->addPsr4('App\\', CLASSES_DIR . '/App');
    $loader->addPsr4('Kuvardin\\', CLASSES_DIR . '/Kuvardin');

    App\DateTime::cacheTimestamp(CURRENT_TIMESTAMP);
    App::init($settings);

    if (!empty($argv)) {
        $result_status = App\Cli\CliController::handle($argv);
        exit($result_status);
    }

    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $ip_remote_addr = $_SERVER["HTTP_CF_CONNECTING_IP"] ?? '';
        $ip_client = $_SERVER["HTTP_CF_CONNECTING_IP"] ?? '';
    } else {
        $ip_remote_addr = $_SERVER['REMOTE_ADDR'] ?? '';
        $ip_client = $_SERVER["HTTP_CLIENT_IP"] ?? '';
    }

    $ip = null;
    foreach ([$ip_client, ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? ''), $ip_remote_addr] as $one_ip) {
        if ($one_ip === '') {
            continue;
        }

        if (filter_var($one_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ||
            filter_var($one_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ip = $one_ip;
            break;
        }
    }

    if ($ip === null) {
        throw new RuntimeException('Empty IP');
    }

    $user_agent = empty($_SERVER['HTTP_USER_AGENT']) ? null : $_SERVER['HTTP_USER_AGENT'];

    $input_string = file_get_contents('php://input');
    $input_string = empty($input_string) ? null : $input_string;

    $route = trim(preg_replace('|\?(.*)$|su', '', $_SERVER['REQUEST_URI']), '/');
    $route = preg_replace('|/+|', '/', $route);
    $route_parts = explode('/', $route);

    App\Sites\SiteController::handle($route_parts, $_GET, $_POST, $_COOKIE, $_FILES, $input_string, $ip,
        $user_agent);
} catch (Throwable $exception) {
    http_response_code(5001);
    $error_text = "Handled by index.php\n<pre>$exception</pre>";
    echo $error_text;

    App\Exceptions\LoggedError::fixThrowable($exception);
    App\Logger::writeException($exception);
}