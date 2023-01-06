<?php

declare(strict_types=1);

namespace App\Sites\Sections\Main\Handlers;

use App\Sessions\Session;
use App\Sites\Exceptions\SiteException;
use App\Sites\Input\SiteInput;
use App\Sites\SiteHandler;
use App\Sites\TemplatesEngine\Page;

class Quit extends SiteHandler
{
    public static function handleRequest(SiteInput $input, Session $session): ?Page
    {
        $session->setUser(null);
        $session->save();

        throw new SiteException(SiteException::MOVED_TEMPORARILY, redirect_url: '/');
    }
}