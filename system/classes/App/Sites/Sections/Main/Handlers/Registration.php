<?php

declare(strict_types=1);

namespace App\Sites\Sections\Main\Handlers;

use App\Sessions\Session;
use App\Sites\Exceptions\SiteException;
use App\Sites\Input\SiteField;
use App\Sites\Input\SiteFieldType;
use App\Sites\Input\SiteInput;
use App\Sites\SiteHandler;
use App\Sites\TemplatesEngine\Page;
use App\Users\User;
use Kuvardin\FastMysqli\Exceptions\AlreadyExists;

class Registration extends SiteHandler
{
    public static function getInputFields(): array
    {
        return [
            'email' => new SiteField(SiteFieldType::String, true),
            'password' => new SiteField(SiteFieldType::String, true),
            'first_name' => new SiteField(SiteFieldType::String, true),
            'last_name' => new SiteField(SiteFieldType::String, true),
            'middle_name' => new SiteField(SiteFieldType::String, true),
        ];
    }

    public static function handleRequest(SiteInput $input, Session $session): ?Page
    {
        if ($session->isAuthorized()) {
            throw new SiteException(SiteException::FORBIDDEN);
        }

        $lang = $session->getLanguage();
        $page = new Page($lang->require('registration'));

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

            $first_name = $input->getString('first_name');
            if ($first_name === null) {
                $page->errors['first_name'] = $lang->require('error_empty_field');
                $is_fine = false;
            }

            $last_name = $input->getString('last_name');
            $middle_name = $input->getString('middle_name');

            if ($is_fine) {
                try {
                    $user = User::create($email, $password, $first_name, $last_name, $middle_name);
                    $session->setUser($user);
                    $session->save();

                    throw new SiteException(SiteException::MOVED_TEMPORARILY, redirect_url: '/');
                } catch (AlreadyExists) {
                    $page->errors['email'] = $lang->require('item_already_exists');
                }
            }

        }

        $page->content .= $page->render($session, 'site/registration', $input);
        $page->not_use_main_template = true;
        return $page;
    }
}