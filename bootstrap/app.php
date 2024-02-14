<?php

declare(strict_types=1);

use Tischmann\Atlantis\{Router, Cookie, Session};

require_once "require.php";

require_once "config.php";

require_once __DIR__ . '/../vendor/autoload.php';

Session::start(name: 'PHPSESSID', id: Cookie::get('PHPSESSID'));

Cookie::set('PHPSESSID', session_id());

Cookie::set('DEV_MODE', 1); // Установка куки для режима разработки

require_once "routes.php";

Router::bootstrap();
