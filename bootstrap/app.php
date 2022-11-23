<?php

declare(strict_types=1);

use Tischmann\Atlantis\{Router, Cookie, Session};

require_once "require.php";

require_once "config.php";

require_once "autoloader.php";

Session::start(name: 'PHPSESSID', id: Cookie::get('PHPSESSID'));

Cookie::set('PHPSESSID', session_id());

if (intval(getenv('APP_DEBUG'))) Cookie::set('XDEBUG_SESSION', 'VSCODE');

require_once "routes.php";

Router::bootstrap();