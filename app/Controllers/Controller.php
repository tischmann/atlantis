<?php

declare(strict_types=1);

namespace App\Controllers;

use BadMethodCallException;

use Tischmann\Atlantis\{CSRF, Facade, Locale, Request, Response, View};

class Controller extends Facade
{
    public function index()
    {
        Response::send(View::make(view: 'welcome')->render());
    }

    public function __call($name, $arguments): mixed
    {
        throw new BadMethodCallException(Locale::get('error_404'), 404);
    }
}
