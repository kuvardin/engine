<?php

declare(strict_types=1);

namespace App\Sites\Sections\Main\Handlers;

use App\Sessions\Session;
use App\Sites\Exceptions\SiteException;
use App\Sites\Input\SiteField;
use App\Sites\Input\SiteFieldType;
use App\Sites\Input\SiteInput;
use App\Sites\SiteHandler;
use App\Sites\TemplatesEngine\Alert;
use App\Sites\TemplatesEngine\BootstrapColor;
use App\Sites\TemplatesEngine\Page;
use App\Users\User;

class Authorization extends SiteHandler
{
    public static function getRequiredPermissions(): array
    {
        return [];
    }

    public static function getInputFields(): array
    {
        return [
            'email' => new SiteField(SiteFieldType::String, true, 'Email адрес'),
            'password' => new SiteField(SiteFieldType::String, true, 'Пароль'),
        ];
    }

    /**
     * @throws SiteException
     */
    public static function handleRequest(SiteInput $input, Session $session): ?Page
    {
        if ($session->isAuthorized()) {
            throw new SiteException(SiteException::PAGE_NOT_FOUND);
        }

        $lang = $session->getLanguage();
        $page = new Page($lang->require('authorization'), no_indexing: true);

        if ($input->hasPost()) {
            $is_fine = true;

            $email = $input->getString('email');
            if ($email === null) {
                $page->errors['email'] = $lang->require('error_empty_field');
                $is_fine = false;
            } elseif (!User::checkEmailValidity($email)) {
                $page->errors['email'] = $lang->require('incorrect_email');
                $is_fine = false;
            }

            $password = $input->getString('password');
            if ($password === null) {
                $page->errors['password'] = $lang->require('error_empty_field');
                $is_fine = false;
            }

            if ($is_fine) {
                $authorized_user = self::getUser($email, $password);
                if ($authorized_user === null) {
                    $page->alerts[] = new Alert($lang->require('authorization_failed'), BootstrapColor::Danger);
                } else {
                    $session->setUser($authorized_user);
                    $session->save();

                    $authorized_user->fixRequest();
                    $authorized_user->save();
                }
            }
        }

        $page->content .= $page->render($session, 'site/authorization', $input, [

        ]);

        $page->not_use_main_template = true;

        return $page;
    }

    public static function getUser(string $email, string $password): ?User
    {
        $email = trim($email);
        if ($email === '') {
            return null;
        }

        $user = User::makeByEmail($email);
        if ($user === null || !$user->checkPassword($password)) {
            return null;
        }

        return $user;
    }
}