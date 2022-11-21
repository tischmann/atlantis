<?php

declare(strict_types=1);

// Настройки PHP

ini_set('html_errors', '1');

ini_set('date.timezone', getenv('APP_TIMEZONE') ?: 'UTC');

ini_set('session.cookie_lifetime', getenv('APP_COOKIE_LIFETIME') ?: '0');

ini_set('session.cookie_httponly', getenv('APP_COOKIE_HTTP_ONLY') ?: '1');

ini_set('session.use_only_cookies', getenv('APP_USE_ONLY_COOKIES') ?: '1');

ini_set('session.cookie_secure', getenv('APP_COOKIE_SECURE') ?: '1');

ini_set('session.use_strict_mode', getenv('APP_USE_STRICT_MODE') ?: '1');

ini_set('session.cookie_samesite', getenv('APP_COOKIE_SAMESITE') ?: 'Strict');

ini_set('session.use_trans_sid', getenv('APP_USE_TRANS_SID') ?: '0');

ini_set('session.cache_limiter', getenv('APP_CACHE_LIMITER') ?: 'nocache');

ini_set('session.sid_length', getenv('APP_SID_LENGTH') ?: '128');

ini_set('session.hash_function', getenv('APP_HASH_FUNCTION') ?: 'sha256');

mb_internal_encoding('UTF-8');

mb_regex_encoding('UTF-8');

mb_http_output('UTF-8');

mb_language('uni');

// Настройки приложения

putenv("APP_ROOT=" . dirname(__FILE__, 2));

putenv('APP_NONCE=' . bin2hex(random_bytes(32)));

$config = getenv('APP_ROOT') . "/.env";

if (!file_exists($config)) die("Файл конфигурации не найден!");

foreach (file($config, FILE_SKIP_EMPTY_LINES) as $line) {
    if (preg_match("/^\s*([A-Z_0-9]+=.*)$/", $line)) putenv(trim($line));
}
