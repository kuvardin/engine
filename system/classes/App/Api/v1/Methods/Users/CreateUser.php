<?php

declare(strict_types=1);

namespace App\Api\v1\Methods\Users;

use App\Actions\Action;
use App\Api\v1\ApiMethodMutable;
use App\Api\v1\Exceptions\ApiException;
use App\Api\v1\Input\ApiInput;
use App\Api\v1\Input\ApiParameter;
use App\Api\v1\Input\ApiParameterType;
use App\Api\v1\Models\UserApiModel;
use App\Api\v1\Output\ApiField;
use App\Sessions\Session;
use App\Users\User;
use Kuvardin\FastMysqli\Exceptions\AlreadyExists;

class CreateUser extends ApiMethodMutable
{
    public static function getDescription(): ?string
    {
        return 'Добавление нового пользователя';
    }

    public static function getResultField(): ?ApiField
    {
        return ApiField::object(UserApiModel::class, false, 'Созданный пользователь');
    }

    public static function getParameters(): array
    {
        return [
            'email' => ApiParameter::string(
                required_and_empty_error: 3002,
                description: 'Email',
                max_length: 128,
            ),
            'password' => ApiParameter::string(
                required_and_empty_error: 3004,
                description: 'Пароль (если не отправить, будет сгенерирован случайный)',
                min_length: 4,
                max_length: 30,
            ),
            'first_name' => ApiParameter::string(
                required_and_empty_error: 3005,
                description: 'Имя',
                min_length: 2,
                max_length: 30
            ),
            'last_name' => ApiParameter::string(
                required_and_empty_error: null,
                description: 'Фамилия',
                min_length: 2,
                max_length: 30,
            ),
            'patronymic' => ApiParameter::scalar(ApiParameterType::String, null, 'Отчество'),
            'post' => ApiParameter::scalar(ApiParameterType::Integer, null, 'ID должности'),
        ];
    }


    public static function handle(ApiInput $input, Session $session): UserApiModel
    {
        $email = $input->requireString('email');
        $password = $input->requireString('password');
        $first_name = $input->requireString('first_name');
        $last_name = $input->getString('last_name');
        $patronymic = $input->getString('patronymic');

//        $session->requirePermission(User::class, Action::CREATE);

        // TODO: $post_id = $input->getInt('post');

        if (!User::checkEmailValidity($email)) {
            throw new ApiException(3006);
        }

        if (User::makeByEmail($email) !== null) {
            throw new ApiException(3007);
        }

        try {
            $user = User::create($session, $email, $password, $first_name, $last_name, $patronymic);
        } catch (AlreadyExists) {
            throw new ApiException(3007);
        }

        return new UserApiModel($user);
    }
}