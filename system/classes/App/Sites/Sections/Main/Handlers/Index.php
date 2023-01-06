<?php

declare(strict_types=1);

namespace App\Sites\Sections\Main\Handlers;

use App\Sessions\Session;
use App\Sites\Input\SiteInput;
use App\Sites\SiteHandler;
use App\Sites\TemplatesEngine\Page;

class Index extends SiteHandler
{
    public static function getRequiredPermissions(): array
    {
        return [];
    }

    public static function handleRequest(SiteInput $input, Session $session): ?Page
    {
        $lang = $session->getLanguage();
        $page = new Page($lang->require('main_page_title'));

        $page->content .= $page->render($session, 'site/index', $input, [

        ]);

        return $page;
    }
}