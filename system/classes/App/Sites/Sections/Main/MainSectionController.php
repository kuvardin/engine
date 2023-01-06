<?php

declare(strict_types=1);

namespace App\Sites\Sections\Main;

use App;
use App\Actions\Action;
use App\Exceptions\NotEnoughRightsException;
use App\Languages\Language;
use App\Logger;
use App\Sessions\Session;
use App\Sessions\WebBot;
use App\Sites\Exceptions\SiteException;
use App\Sites\Input\SiteField;
use App\Sites\Input\SiteFieldType;
use App\Sites\Input\SiteInput;
use App\Sites\Sections\ControlPanel\Handlers\Authorization;
use App\Sites\SiteHandler;
use App\Sites\TemplatesEngine\Page;
use App\Sites\TemplatesEngine\Sidebar\Block as SidebarBlock;
use App\Sites\TemplatesEngine\Sidebar\Item as SidebarItem;
use App\Sites\TemplatesEngine\Sidebar\Subitem as SidebarSubitem;
use App\Telegram\Notifier;
use RuntimeException;
use Throwable;

class MainSectionController
{
    public static function handle(
        string $lang_code,
        array $route_parts,
        array $get,
        array $post,
        array $cookies,
        array $files,
        ?string $input_string,
        string $ip,
        ?string $user_agent,
    ): void
    {
        App::connectMysqli();

        $route = implode('/', $route_parts);

        $current_timestamp = time();

        $web_bot_code = $user_agent === null ? null : WebBot::makeByUserAgent($user_agent);
        $session = $web_bot_code === null
            ? Session::makeByCookies($cookies, $ip, $user_agent)
            : Session::makeByWebBotCode($web_bot_code->value);

        if ($session === null) {
            $session = Session::create($ip, $user_agent, App::settings('language.default'), $current_timestamp);
            $session->setCookie();
        } else {
            $session->fixRequest($ip, $user_agent, $current_timestamp);
            $session->save();
        }

        if ($session->getLanguageCode() !== $lang_code) {
            $session->setLanguageCode($lang_code);
        }

        $language = $session->getLanguage();
        $language->setPhrases(require PHRASES_DIR . '/sites/main_section.php');

        if (!$session->isAuthorized()) {
            if ($route !== 'registration') {
                $authorized_user = null;
                if (!empty($post['email']) && !empty($post['password'])) {
                    $authorized_user = Handlers\Authorization::getUser($post['email'], $post['password']);
                    if ($authorized_user !== null) {
                        $session->setUser($authorized_user);
                        $post = [];
                    }
                }

                if ($authorized_user === null) {
                    $route_parts = ['authorization'];
                }
            }
        } else {
            $session->requireUser()->fixRequest($current_timestamp);
        }

        $id_from_route = null;
        $route_last_part = array_pop($route_parts);
        if ($route_last_part !== null) {
            if (preg_match('|^\d+$|', $route_last_part)) {
                $id_from_route = (int)$route_last_part;
            } else {
                $route_parts[] = $route_last_part;
            }
        }

        $page = $input = null;

        try {
            try {
                $handler_class = self::getHandlerClass($route_parts);
                if (str_ends_with($handler_class, 'WithId')) {
                    throw new SiteException(SiteException::PAGE_NOT_FOUND);
                }

                if ($id_from_route !== null) {
                    $handler_class .= 'WithId';
                }

                if (!class_exists($handler_class)) {
                    throw new SiteException(SiteException::PAGE_NOT_FOUND);
                }

                foreach ($handler_class::getRequiredPermissions() as $object_class => $required_permission) {
                    if (!$session->can($object_class, $required_permission)) {
                        throw new NotEnoughRightsException($object_class, $required_permission);
                    }
                }

                $handler_phrases = $handler_class::getPhrases();
                if ($handler_phrases !== []) {
                    $language->setPhrases($handler_phrases);
                }

                $input_fields = $handler_class::getAllInputFields();
                $input = new SiteInput($route, $get, $post, $files, $input_string, $input_fields, $id_from_route);
                $page = $handler_class::handleRequest($input, $session);

                if ($page !== null) {
                    $page->no_indexing = true;
                }
            } catch (SiteException $site_exception) {
                throw $site_exception;
            } catch (NotEnoughRightsException) {
                throw new SiteException(SiteException::FORBIDDEN);
            } catch (Throwable $exception) {
                Notifier::tryToSendException($exception);
                Logger::writeException($exception);
                throw new SiteException(SiteException::INTERNAL_SERVER_ERROR, nl2br((string)$exception));
            }
        } catch (SiteException $site_exception) {
            http_response_code($site_exception->getCode());
            if ($site_exception->getRedirectUrl() !== null) {
                header("Location: {$site_exception->getRedirectUrl()}");
                $page = null;
            } else {
                $input ??= new SiteInput($route, $get, $post, $files, $input_string, $input_fields ?? [],
                    $id_from_route ?? null);
                $page = self::getErrorPage($input, $session, $site_exception->getCode(), $site_exception->getMessage());
            }
        } finally {
            $session->save();

            if ($page !== null) {
                $input ??= new SiteInput($route, $get, $post, $files, $input_string, $input_fields ?? [],
                    $id_from_route ?? null);

                http_response_code($page->getHttpStatus());

                if ($page->getRedirectUri() !== null) {
                    header("Location: {$page->getRedirectUri()}");
                }

                if ($page->not_use_main_template) {
                    echo $page->content;
                } else {
                    echo $page->render($session, 'site/main', $input, [
                        'route' => implode('/', $route_parts),
                        'route_parts' => $route_parts,
                        'sidebar_blocks' => self::getSidebarBlocks($session),
                    ]);
                }
            }
        }

    }

    public static function getUrl(Language|string $language, string $path, array $get = null): string
    {
        return '/' . (is_string($language) ? $language : $language->getCode()) . '/' . ltrim($path, '/') .
            ($get === null ? null : ('?' . http_build_query($get)));
    }


    private static function getHandlerClass(array $route_parts): SiteHandler|string
    {
        if (empty($route_parts[0])) {
            $route_parts[0] = 'index';
        }

        $result = 'App\\Sites\\Sections\\Main\\Handlers\\';

        foreach ($route_parts as $route_part) {
            $words = explode('_', $route_part);
            foreach ($words as $word) {
                $result .= ucfirst($word);
            }
            $result .= '\\';
        }

        return rtrim($result, '\\');
    }

    public static function getErrorPage(SiteInput $input, Session $session, int $code, ?string $message): Page
    {
        $page = new Page(
            $session->getLanguage()->require('error_with_code', [$code]),
            no_indexing: true,
        );

        $page->content .= $page->render($session, 'site/error', $input, [
            'error_code' => $code,
            'error_message' => $message,
        ]);

        return $page;
    }

    protected static function getSidebarBlocks(Session $session): array
    {
        $lang = $session->getLanguage();
        $result = [
            new SidebarBlock($lang->require('user_profile'), [
                !$session->isAuthorized()
                    ? new SidebarItem($lang->require('authorization'), 'authorization', 'fa fa-user', true,
                        path: 'authorization')
                    : null,
            ]),
        ];

        foreach ($result as $block_index => $block) {
            if ($block === null) {
                unset($result[$block_index]);
                continue;
            }

            foreach ($block->items as $item_index => $item) {
                if ($item === null) {
                    unset($block->items[$item_index]);
                    continue;
                }

                if ($item->path === null) {
                    foreach ($item->subitems as $subitem_index => $subitem) {
                        if ($subitem === null) {
                            unset($item->subitems[$subitem_index]);
                        }
                    }

                    if ($item->subitems === []) {
                        unset($block->items[$item_index]);
                    }
                }
            }

            if ($block->items === []) {
                unset($result[$block_index]);
            }
        }

        return $result;
    }
}